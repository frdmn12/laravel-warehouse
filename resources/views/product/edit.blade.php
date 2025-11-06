@extends('layout.layout1')

@section('title', 'Edit Product')
@section('content')
    <section class="max-w-7xl mx-auto p-6 shadow-md rounded-lg bg-white">
        <h1 class="text-3xl font-bold mb-6">Edit Product</h1>
        <form method="POST" id="form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="product_name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" id="product_name" name="product_name" class="w-full text-sm border border-gray-300 rounded-md p-2"
                           value="{{ old('product_name', $product->name ?? '') }}" placeholder="">
                </div>
                <div>
                    <label for="product_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Produk</label>
                    <input type="text" id="product_code" name="product_code" class="w-full text-sm border border-gray-300 rounded-md p-2"
                           value="{{ old('product_code', $product->product_code ?? '') }}" placeholder="">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md">Update</button>
                <a href="{{ route('products.index') }}" class="mt-4 bg-gray-200 text-gray-700 px-4 py-2 rounded-md">Cancel</a>
            </div>
        </form>
    </section>
@endsection

@section('content_headscript')
@endsection

@section('content_tailscript')
    <script>
        $(document).ready(function () {
            $('#form-data').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    type: 'PUT',
                    url: '{{ route("products.update", $product->id) }}',
                    headers: {
                        'X-CSRF-TOKEN': '{!! csrf_token() !!}'
                    },
                    data: {
                        'name': $('#product_name').val(),
                        'product_code': $('#product_code').val()
                    },
                    success: function (response) {
                        alert('Product updated successfully!');
                        window.location.href = '{{ route("products.index") }}';
                    },
                    error: function (xhr) {
                        alert('An error occurred while updating the product.');
                    }
                });
            });
        });
    </script>
@endsection