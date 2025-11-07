<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\StockProduct;
use App\Models\TransactionHistory;
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

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'transaction_type' => 'required|in:MASUK,KELUAR',
        'batch' => 'required|string',
        'product_id' => 'required|integer',
        'stock' => 'required|integer|min:1',
        'date_of_entry' => 'required|date',
        'location_id' => 'required|integer',
    ]);

    $userId = Auth::id();

    DB::beginTransaction();
    try {
        $product_id = $validated['product_id'];
        $location_id = $validated['location_id'];
        $date_of_entry = $validated['date_of_entry'];
        $qty = $validated['stock'];
        $type = $validated['transaction_type'];

        // ==========================
        // 🔍 VALIDASI 1: CEK TANGGAL TRANSAKSI
        // ==========================
        $latestEntry = StockProduct::where('product_id', $product_id)
            ->where('location_id', $location_id)
            ->max('date_of_entry');

        if ($latestEntry && $date_of_entry < $latestEntry) {
            throw new \Exception("Transaksi ditolak: tanggal transaksi tidak boleh lebih kecil dari tanggal masuk terakhir ({$latestEntry}).");
        }

        // ==========================
        // 🔍 VALIDASI 2: CEK SALDO UNTUK TRANSAKSI KELUAR
        // ==========================
        if ($type === 'KELUAR') {
            $totalStock = StockProduct::where('product_id', $product_id)
                ->where('location_id', $location_id)
                ->sum('stock');

            if ($totalStock < $qty) {
                throw new \Exception("Transaksi ditolak: saldo barang tidak mencukupi. (Stok saat ini: {$totalStock})");
            }
        }

        // ==========================
        // 🧾 BUAT TRANSACTION HISTORY
        // ==========================
        $transaction = new TransactionHistory();
        $transaction->batch = $validated['batch'];
        $transaction->date = now();
        $transaction->date_of_transaction = $date_of_entry;
        $transaction->location_id = $location_id;
        $transaction->qty = $qty;
        $transaction->product_id = $product_id;
        $transaction->created_by = $userId;
        $transaction->created_at = now();
        $transaction->save();

        // ==========================
        // ⚙️ PROSES FIFO
        // ==========================
        if ($type === 'MASUK') {
            // Tambah atau update stok untuk tanggal sama
            $existingStock = StockProduct::where('product_id', $product_id)
                ->where('location_id', $location_id)
                ->whereDate('date_of_entry', $date_of_entry)
                ->first();

            if ($existingStock) {
                $existingStock->stock += $qty;
                $existingStock->updated_at = now();
                $existingStock->updated_by = $userId;
                $existingStock->save();
            } else {
                $newStock = new StockProduct();
                $newStock->product_id = $product_id;
                $newStock->location_id = $location_id;
                $newStock->stock = $qty;
                $newStock->date_of_entry = $date_of_entry;
                $newStock->created_by = $userId;
                $newStock->created_at = now();
                $newStock->save();
            }

        } else { // === KELUAR ===
            $qtyToDeduct = $qty;

            $batches = StockProduct::where('product_id', $product_id)
                ->where('location_id', $location_id)
                ->where('stock', '>', 0)
                ->orderBy('date_of_entry', 'asc')
                ->lockForUpdate()
                ->get();

            foreach ($batches as $batch) {
                if ($qtyToDeduct <= 0) break;

                $available = $batch->stock;
                $deduct = min($available, $qtyToDeduct);

                $batch->stock -= $deduct;
                $batch->updated_at = now();
                $batch->updated_by = $userId;
                $batch->save();

                $qtyToDeduct -= $deduct;
            }

            if ($qtyToDeduct > 0) {
                throw new \Exception("Stok tidak mencukupi untuk produk ini.");
            }
        }

        DB::commit();
        return redirect()->back()->with('success', 'Transaksi berhasil disimpan.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
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