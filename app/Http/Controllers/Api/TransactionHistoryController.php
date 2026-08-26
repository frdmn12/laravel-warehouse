<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionHistoryResource;
use App\Models\TransactionHistory;
use Illuminate\Http\Request;

class TransactionHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = TransactionHistory::query()
            ->with(['product', 'location'])
            ->when($request->input('batch'), function ($q, $v) {
                $q->where('batch', 'like', '%' . strtoupper(trim($v)) . '%');
            })
            ->when($request->input('product_name'), function ($q, $v) {
                $q->whereHas('product', fn ($p) => $p->where('name', 'like', '%' . $v . '%'));
            })
            ->when($request->input('product_code'), function ($q, $v) {
                $q->whereHas('product', fn ($p) => $p->where('product_code', 'like', '%' . $v . '%'));
            });

        $sort = $request->input('sort', 'date_of_transaction');
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';
        if (in_array($sort, ['batch', 'date_of_transaction', 'qty'], true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('date_of_transaction', 'desc');
        }

        $transactions = $query->paginate((int) $request->input('per_page', 15));

        return TransactionHistoryResource::collection($transactions);
    }

    public function generateBatch(Request $request)
    {
        $transactionType = $request->input('transaction_type', 'in'); // 'in' or 'out'
        $date = $request->input('date', date('Y-m-d'));
        $prefix = strtoupper($transactionType) === 'OUT' ? 'KURANG' : 'TAMBAH';

        try {
            $dateObj = new \DateTime($date);
            $datePart = $dateObj->format('Ymd');
        } catch (\Exception $e) {
            $datePart = date('Ymd');
        }

        $like = $prefix . '-' . $datePart . '-%';

        $lastIncrement = TransactionHistory::where('batch', 'like', $like)
            ->pluck('batch')
            ->map(function ($batch) {
                preg_match('/-(\d{3})$/', $batch, $matches);

                return $matches ? (int) $matches[1] : 0;
            })
            ->max();

        $newIncrement = str_pad(($lastIncrement ?? 0) + 1, 3, '0', STR_PAD_LEFT);

        $newBatch = $prefix . '-' . $datePart . '-' . $newIncrement;

        return response()->json(['batch' => $newBatch]);
    }
}
