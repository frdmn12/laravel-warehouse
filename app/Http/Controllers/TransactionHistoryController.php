<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TransactionHistory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TransactionHistoryController extends Controller
{
    public function __construct()
    {
        $this->program = 'transaction_history';
        $this->url = '/transaction-history';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('transaction_history.index');
    }

    public function data(Request $request)
    {
        $tcode = $this->program;
        session([
            $tcode . 'batch'       =>  $request->input('batch') != '' || $request->input('batch') != null ?    strtoupper(trim($request->input('batch', ''))) : '',
        ]);

        try {
            $product_name = session($tcode . 'product_name', '');
            $product_code = session($tcode . 'product_code', '');

            $transactionTable = (new TransactionHistory())->getTable();

            $data = TransactionHistory::select(
                $transactionTable . '.id',
                $transactionTable . '.batch',
                $transactionTable . '.date',
                'location.name as location_name',
                'products.product_code as product_code',
                $transactionTable . '.date_of_transaction',
                $transactionTable . '.qty',
            )
                ->leftJoin('products', 'products.id', '=', $transactionTable . '.product_id')
                ->leftJoin('location', 'location.id', '=', $transactionTable . '.location_id')
                ->where($transactionTable . '.batch', 'like', '%' . session($tcode . 'batch', '') . '%')
                ->when($product_name, function ($q) use ($product_name) {
                    $q->where('products.name', 'like', '%' . $product_name . '%');
                })
                ->when($product_code, function ($q) use ($product_code) {
                    $q->where('products.product_code', 'like', '%' . $product_code . '%');
                })
                ->orderBy($transactionTable . '.date_of_transaction', 'desc')
                ->get();

            $datatable = DataTables::of($data);

            $datatable->addColumn('action', function ($item) {
                $txt = '';

                $txt .= "<a href=\"#\" onclick=\"showItem('$item->id');\" title=\"detail\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-eye fa-fw fa-xs\"></i></a>";
                $txt .= "<a href=\"#\" onclick=\"editItem('$item->id');\" title=\"edit\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-edit fa-fw fa-xs\"></i></a>";
                $txt .= "<a href=\"#\" onclick=\"deleteItem('$item->id');\" title=\"delete\" class=\"btn btn-xs btn-danger\"><i class=\"fa fa-trash fa-fw fa-xs\"></i></a>";

                return $txt;
            })
                ->editColumn('hour', function ($item) {
                    return date('H:i:s', strtotime($item->date));
                })
                ->editColumn('date_of_transaction', function ($item) {
                    return \Carbon\Carbon::parse($item->date_of_transaction)->format('j F Y');
                })
            ;

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

    public function generateBatch(Request $request)
    {
        $transactionType = $request->input('transaction_type', 'in'); // 'in' or 'out'
        $date = $request->input('date', date('Y-m-d'));
        // Use uppercase prefix to match existing DB values like "TAMBAH-20251107-001"
        $prefix = strtoupper($transactionType) === 'OUT' ? 'KURANG' : 'TAMBAH';

        try {
            $dateObj = new \DateTime($date);
            $datePart = $dateObj->format('Ymd');
        } catch (\Exception $e) {
            $datePart = date('Ymd');
        }

        $like = $prefix . '-' . $datePart . '-%';

        // Order by numeric suffix to get the highest increment for that date
        $lastBatch = TransactionHistory::where('batch', 'like', $like)
            ->orderByRaw("CAST(RIGHT(batch, 3) AS UNSIGNED) DESC")
            ->first();

        if ($lastBatch && preg_match('/-(\d{3})$/', $lastBatch->batch, $matches)) {
            $lastIncrement = (int)$matches[1];
            $newIncrement = str_pad($lastIncrement + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newIncrement = '001';
        }

        $newBatch = $prefix . '-' . $datePart . '-' . $newIncrement;

        return response()->json(['batch' => $newBatch]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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