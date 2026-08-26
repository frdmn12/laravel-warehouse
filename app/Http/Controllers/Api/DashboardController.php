<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionHistoryResource;
use App\Models\Location;
use App\Models\Products;
use App\Models\StockProduct;
use App\Models\TransactionHistory;

class DashboardController extends Controller
{
    public function summary()
    {
        $recentTransactions = TransactionHistory::with(['product', 'location'])
            ->orderByDesc('date')
            ->limit(5)
            ->get();

        return response()->json([
            'data' => [
                'total_products' => Products::count(),
                'total_locations' => Location::count(),
                'total_stock' => (int) StockProduct::sum('stock'),
                'recent_transactions' => TransactionHistoryResource::collection($recentTransactions),
            ],
        ]);
    }
}
