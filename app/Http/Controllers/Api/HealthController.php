<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'ok',
            'time' => now()->toIso8601String(),
        ]);
    }

    public function db()
    {
        try {
            DB::connection()->select('select 1');
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database connection failed.',
            ], 503);
        }

        return response()->json([
            'status' => 'ok',
            'time' => now()->toIso8601String(),
        ]);
    }
}
