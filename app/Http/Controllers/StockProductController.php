<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\StockProduct;
use App\Models\TransactionHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockProductController extends Controller
{
    public function __construct()
    {
        $this->program = 'stock_product_management';
        $this->url = '/stock-product';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('stock_product.index');
    }

    public function data(Request $request)
    {
        $tcode = $this->program;
        session([
            $tcode . 'product_name'       =>  $request->input('product_name') != '' || $request->input('product_name') != null ?    strtoupper(trim($request->input('product_name', ''))) : '',
            $tcode . 'product_code'       =>  $request->input('product_code') != '' || $request->input('product_code') != null ?    strtoupper(trim($request->input('product_code', ''))) : '',
            $tcode . 'location'           =>  $request->input('location') != '' || $request->input('location') != null ?    strtoupper(trim($request->input('location', ''))) : '',
        ]);

        try {
            $product_name = session($tcode . 'product_name', '');
            $product_code = session($tcode . 'product_code', '');
            $location     = session($tcode . 'location', '');

            $data = StockProduct::select('stock_products.*', 'products.name as product_name', 'products.product_code', 'location.name as location_name', 'stock_products.date_of_entry', 'stock_products.stock')
                ->join('products', 'stock_products.product_id', '=', 'products.id')
                ->leftJoin('location', 'stock_products.location_id', '=', 'location.id')
                ->when($product_name, function ($q) use ($product_name) {
                    $q->where('products.name', 'like', '%' . $product_name . '%');
                })
                ->when($product_code, function ($q) use ($product_code) {
                    $q->where('products.product_code', 'like', '%' . $product_code . '%');
                })
                ->when($location, function ($q) use ($location) {
                    $q->where('location.name', 'like', '%' . $location . '%');
                })
                ->get();


            $datatable = DataTables::of($data);

            // $datatable->addColumn('action', function ($item) {
            //     $txt = '';

            //     $txt .= "<a href=\"#\" onclick=\"showItem('$item->id', '$item->asset_type_label');\" title=\"detail\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-eye fa-fw fa-xs\"></i></a>";
            //     $txt .= "<a href=\"#\" onclick=\"editItem('$item->id');\" title=\"edit\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-edit fa-fw fa-xs\"></i></a>";
            //     $txt .= "<a href=\"#\" onclick=\"deleteItem('$item->id');\" title=\"delete\" class=\"btn btn-xs btn-danger\"><i class=\"fa fa-trash fa-fw fa-xs\"></i></a>";

            //     return $txt;
            // });
            return $datatable->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'draw'            => 0,
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => $e->getMessage(),
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // prepare data for create view
        $products = Products::select('id', 'name', 'product_code')->orderBy('name')->get();

        // try to load locations if model exists, otherwise pass empty collection
        try {
            $locations = \App\Models\Location::select('id', 'name')->orderBy('name')->get();
        } catch (\Throwable $e) {
            $locations = collect();
        }

        return view('stock_product.create', compact('products', 'locations'));
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

            // dd($existing);

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
        // dd('debug');

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


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}