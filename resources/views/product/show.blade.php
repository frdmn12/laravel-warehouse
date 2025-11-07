@extends('layout.layout1')

@section('title', 'Show Product')
@section('content')
    <section class="max-w-7xl mx-auto p-6 shadow-md rounded-lg bg-white">
        {{-- button back --}}
        <a href="javascript:history.back()" class="mb-4 inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>
            Back
        </a>
        <h1 class="text-3xl font-bold mb-6">Show Product Details</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <div class="w-full text-sm border border-gray-200 rounded-md p-2 bg-gray-50">
                    {{ $product->name ?? '-' }}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Produk</label>
                <div class="w-full text-sm border border-gray-200 rounded-md p-2 bg-gray-50">
                    {{ $product->product_code ?? '-' }}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Created At</label>
                <div class="w-full text-sm border border-gray-200 rounded-md p-2 bg-gray-50">
                    {{ $product->created_at ? \Carbon\Carbon::parse($product->created_at)->format('d M Y H:i') : '-' }}
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Created By</label>
                <div class="w-full text-sm border border-gray-200 rounded-md p-2 bg-gray-50">
                    {{ $created_by->name ?? '-' }}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Updated At</label>
                <div class="w-full text-sm border border-gray-200 rounded-md p-2 bg-gray-50">
                    {{ $product->updated_at ? \Carbon\Carbon::parse($product->updated_at)->format('d M Y H:i') : '-' }}
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Updated By</label>
                <div class="w-full text-sm border border-gray-200 rounded-md p-2 bg-gray-50">
                    {{ $updated_by->name ?? '-' }}
                </div>
            </div>

        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('products.edit', $product->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded-md">Edit</a>
            <a href="{{ route('products.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md">Back to List</a>
        </div>
    </section>
@endsection

@section('content_headscript')
@endsection

@section('content_tailscript')
@endsection
