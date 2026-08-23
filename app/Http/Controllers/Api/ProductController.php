<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Products;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Products::query()
            ->when($request->input('product_name'), function ($q, $v) {
                $q->where('name', 'like', '%' . $v . '%');
            })
            ->when($request->input('product_code'), function ($q, $v) {
                $q->where('product_code', 'like', '%' . $v . '%');
            });

        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        if (in_array($sort, ['name', 'product_code', 'created_at'], true)) {
            $query->orderBy($sort, $direction);
        }

        if ($request->boolean('all')) {
            return ProductResource::collection($query->get());
        }

        $products = $query->paginate((int) $request->input('per_page', 15));

        return ProductResource::collection($products);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => 'required|string|max:100|unique:products,product_code',
        ]);

        try {
            $product = Products::create([
                'name' => trim(strtoupper($validatedData['name'])),
                'product_code' => trim(strtoupper($validatedData['product_code'])),
                'created_at' => Carbon::now(),
                'created_by' => Auth::id(),
            ]);

            return (new ProductResource($product))->response()->setStatusCode(201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        $product = Products::with(['creator', 'updater'])->findOrFail($id);

        return new ProductResource($product);
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => 'required|string|max:100|unique:products,product_code,' . $id,
        ]);

        try {
            $product = Products::findOrFail($id);

            $product->name = trim(strtoupper($validatedData['name']));
            $product->product_code = trim(strtoupper($validatedData['product_code']));
            $product->updated_at = Carbon::now();
            $product->updated_by = Auth::id();
            $product->save();

            return new ProductResource($product);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $product = Products::findOrFail($id);
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage(),
            ], 500);
        }
    }
}
