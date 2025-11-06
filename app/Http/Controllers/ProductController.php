<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
// use DB;
use Illuminate\Support\Facades\DB as DB;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->program = 'product_management';
        $this->url = '/products';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('product.index');
    }

    public function data(Request $request)
    {
        $tcode = $this->program;
        session([
            $tcode . 'product_name'       =>  $request->input('product_name') != '' || $request->input('product_name') != null ?    strtoupper(trim($request->input('product_name', ''))) : '',
            $tcode . 'product_code'       =>  $request->input('product_code') != '' || $request->input('product_code') != null ?    strtoupper(trim($request->input('product_code', ''))) : '',
        ]);

        try {
            $product_name = session($tcode . 'product_name', '');
            $product_code = session($tcode . 'product_code', '');

            $data = Products::where('name', 'like', '%' . $product_name . '%')
                ->where('product_code', 'like', '%' . $product_code . '%')
                ->get();

            $datatable = DataTables::of($data);

            $datatable->addColumn('action', function ($item) {
                $txt = '';

                $txt .= "<a href=\"#\" onclick=\"showItem('$item->id', '$item->asset_type_label');\" title=\"detail\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-eye fa-fw fa-xs\"></i></a>";
                $txt .= "<a href=\"#\" onclick=\"editItem('$item->id');\" title=\"edit\" class=\"btn btn-xs btn-secondary\"><i class=\"fa fa-edit fa-fw fa-xs\"></i></a>";
                $txt .= "<a href=\"#\" onclick=\"deleteItem('$item->id');\" title=\"delete\" class=\"btn btn-xs btn-danger\"><i class=\"fa fa-trash fa-fw fa-xs\"></i></a>";

                return $txt;
            });
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
        //
        return view('product.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => 'required|string|max:100|unique:products,product_code',
        ]);

        // Create a new product
        try {
            $userId = Auth::user()->id;
            Products::create([
                'name' => trim(strtoupper($validatedData['name'])),
                'product_code' => trim(strtoupper($validatedData['product_code'])),
                'created_at' => Carbon::now(),
                'created_by' => $userId,
            ]);

            // return redirect()->route('products.index')->with('success', 'Product created successfully.');
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $product = Products::findOrFail($id);
        $created_by = DB::table('users')->where('id', $product->created_by)->first();
        $updated_by = DB::table('users')->where('id', $product->updated_by)->first();
        return view('product.show', ['product' => $product, 'created_by' => $created_by, 'updated_by' => $updated_by]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        try {
            $product = Products::findOrFail($id);
        } catch (\Throwable $e) {
            $product = null;
        }


        return view('product.edit', ['id' => $id, 'product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => 'required|string|max:100|unique:products,product_code,' . $id,
        ]);
        try {
            $product = Products::findOrFail($id);

            $product->name = trim(strtoupper($validatedData['name']));
            $product->product_code = trim(strtoupper($validatedData['product_code']));
            $product->updated_at = Carbon::now();
            $product->updated_by = Auth::user()->id;
            $product->save();

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
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
