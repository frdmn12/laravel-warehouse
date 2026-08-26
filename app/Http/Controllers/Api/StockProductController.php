<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockProductResource;
use App\Models\StockProduct;
use App\Models\TransactionHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockProductController extends Controller
{
    public function index(Request $request)
    {
        $productName = $request->input('product_name', '');
        $productCode = $request->input('product_code', '');
        $location = $request->input('location', '');

        $query = StockProduct::select(
            'stock_products.*',
            'products.name as product_name',
            'products.product_code',
            'location.name as location_name'
        )
            ->join('products', 'stock_products.product_id', '=', 'products.id')
            ->leftJoin('location', 'stock_products.location_id', '=', 'location.id')
            ->when($productName, function ($q) use ($productName) {
                $q->where('products.name', 'like', '%' . $productName . '%');
            })
            ->when($productCode, function ($q) use ($productCode) {
                $q->where('products.product_code', 'like', '%' . $productCode . '%');
            })
            ->when($location, function ($q) use ($location) {
                $q->where('location.name', 'like', '%' . $location . '%');
            });

        $sort = $request->input('sort', 'date_of_entry');
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        if (in_array($sort, ['product_name', 'product_code', 'location_name', 'stock', 'date_of_entry'], true)) {
            $query->orderBy($sort, $direction);
        }

        $stockProducts = $query->paginate((int) $request->input('per_page', 15));

        return StockProductResource::collection($stockProducts);
    }

    public function store(Request $request)
    {
        // validasi awal (akan dikembalikan sebagai JSON jika gagal)
        try {
            $validated = $request->validate([
                'transaction_type' => 'required|in:MASUK,KELUAR',
                'batch' => 'required|string',
                'product_id' => 'required|integer',
                'stock' => 'required|integer|min:1',
                'date_of_entry' => 'required|date',
                'location_id' => 'required|integer',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        }

        $userId = Auth::id();
        $productId = $validated['product_id'];
        $locationId = $validated['location_id'];
        $dateOfEntry = Carbon::parse($validated['date_of_entry'])->startOfDay();
        $qty = (int) $validated['stock'];
        $type = $validated['transaction_type'];

        DB::beginTransaction();
        try {
            // ================
            // VALIDASI TANGGAL DIBANDINGKAN TRANSACTION_HISTORY
            // ================
            $latestTxnDate = TransactionHistory::where('product_id', $productId)
                ->where('location_id', $locationId)
                ->max('date_of_transaction'); // may be null

            if ($latestTxnDate) {
                $latestTxnDate = Carbon::parse($latestTxnDate)->startOfDay();
                if ($dateOfEntry->lt($latestTxnDate)) {
                    throw new \Exception("Transaksi ditolak: tanggal transaksi ({$dateOfEntry->toDateString()}) tidak boleh lebih kecil dari tanggal transaksi terakhir ({$latestTxnDate->toDateString()}).");
                }
            }

            // ================
            // Jika KELUAR -> ambil batches dengan lock lalu cek total stok dari hasil lock (menghindari race)
            // ================
            if ($type === 'KELUAR') {
                // ambil semua batch yang masih punya stok, lock untuk update
                $batches = StockProduct::where('product_id', $productId)
                    ->where('location_id', $locationId)
                    ->where('stock', '>', 0)
                    ->orderBy('date_of_entry', 'asc')
                    ->lockForUpdate()
                    ->get();

                $totalStock = $batches->sum('stock');

                if ($totalStock < $qty) {
                    throw new \Exception("Transaksi ditolak: saldo barang tidak mencukupi. (Stok saat ini: {$totalStock})");
                }
            }

            // ================
            // SIMPAN TRANSACTION HISTORY (audit log)
            // ================
            $transaction = new TransactionHistory();
            $transaction->batch = $validated['batch'];
            $transaction->date = now();
            $transaction->date_of_transaction = $dateOfEntry->toDateString();
            $transaction->location_id = $locationId;
            $transaction->qty = $qty;
            $transaction->product_id = $productId;
            $transaction->created_by = $userId;
            $transaction->created_at = now();

            $transaction->save();

            // ================
            // PROSES FIFO (MASUK / KELUAR)
            // ================
            if ($type === 'MASUK') {
                // jika ada baris stock_products dengan tanggal yang sama, gabung/menambah stok
                $existing = StockProduct::where('product_id', $productId)
                    ->where('location_id', $locationId)
                    ->whereDate('date_of_entry', $dateOfEntry->toDateString())
                    ->lockForUpdate()    // lock row jika ada agar aman saat concurrency
                    ->first();

                if ($existing) {
                    $existing->stock += $qty;
                    $existing->updated_by = $userId;
                    $existing->save();
                } else {
                    // buat entry baru
                    $new = new StockProduct();
                    $new->product_id = $productId;
                    $new->location_id = $locationId;
                    $new->stock = $qty;
                    $new->date_of_entry = $dateOfEntry->toDateString();
                    $new->created_by = $userId;
                    $new->created_at = now();
                    $new->save();
                }
            } else { // KELUAR (FIFO)
                // jika belum pernah mengambil $batches sebelumnya (misal karena kondisi KELUAR validasi dilakukan dulu),
                // dapatkan batches dan lock lagi (sudah di-lock di atas jika kita ambil sebelumnya)
                $batches = isset($batches) ? $batches : StockProduct::where('product_id', $productId)
                    ->where('location_id', $locationId)
                    ->where('stock', '>', 0)
                    ->orderBy('date_of_entry', 'asc')
                    ->lockForUpdate()
                    ->get();

                $qtyToDeduct = $qty;
                foreach ($batches as $batch) {
                    if ($qtyToDeduct <= 0) break;

                    $available = $batch->stock;
                    $deduct = min($available, $qtyToDeduct);

                    $batch->stock -= $deduct;
                    $batch->updated_by = $userId;
                    $batch->save();

                    $qtyToDeduct -= $deduct;
                }

                if ($qtyToDeduct > 0) {
                    // seharusnya tidak terjadi karena kita sudah cek totalStock di atas
                    throw new \Exception("Transaksi ditolak: terjadi kesalahan stok saat pemrosesan FIFO.");
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(string $id)
    {
        $stockProduct = StockProduct::with(['product', 'location'])->findOrFail($id);

        return new StockProductResource($stockProduct);
    }
}
