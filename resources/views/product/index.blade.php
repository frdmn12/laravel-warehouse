@extends('layout.layout1')

@section('title', 'Master Products')
@section('content')
    <section class="max-w-7xl mx-auto p-6 shadow-md rounded-lg bg-white">
        <h1 class="text-3xl font-bold mb-6">Master Products</h1>
        <div class="flex">
            <a href="{{ route('products.create') }}" class="bg-gray-400 text-[11px] text-white px-2 py-1 rounded-sm mb-4 inline-flex items-center">
                <i class="fas fa-plus mr-2 text-xs"></i>
                Add
            </a>
        </div>
        <form method="GET" id="search-product">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="product_name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" id="product_name" name="product_name" class="w-full text-sm border border-gray-300 rounded-md p-2" placeholder="">
                </div>
                <div>
                    <label for="product_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Produk</label>
                    <input type="text" id="product_code" name="product_code" class="w-full text-sm border border-gray-300 rounded-md p-2" placeholder="">
                </div>
            </div>
            <button class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md">Search</button>
        </form>
    </section>

    <section class="shadow-md rounded-lg bg-white mt-2 p-2">
        <table class="" id="main-table" width="100%">
            <thead class="text-nowrap">
                <tr>
                    <th>Action</th>
                    <th>Nama Barang</th>
                    <th>Kode Barang</th>
                    <th>Created At</th>
                </tr>
            </thead>
        </table>
    </section>

@endsection
@section('content_headscript')
@endsection
@section('content_tailscript')
    <script>
        $(function () {
            var oTable = $('#main-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('products.data') }}",
                    type: "POST",
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: function (d) {
                        d.product_name = $('#product_name').val();
                        d.product_code = $('#product_code').val();
                    }
                },
                columns: [
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        className: 'text-nowrap'
                    },
                    {
                        data: 'product_code',
                        className: 'text-nowrap'
                    },
                    { data: 'created_at' },
                ]
            });

            $('#search-product').on('submit', function (e) {
                e.preventDefault();
                oTable.ajax.reload();
            });

        });

        function editItem(id) {
            location.replace('{{ url('/products') }}/' + id + '/edit');
        }
        function showItem(id) {
            location.replace('{{ url('/products') }}/' + id);
        }
        function deleteItem(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                $.ajax({
                    type: 'DELETE',
                    url: '{{ url('/products') }}/' + id,
                    headers: {
                        'X-CSRF-TOKEN': '{!! csrf_token() !!}'
                    },
                    success: function (response) {
                        alert('Product deleted successfully!');
                        $('#main-table').DataTable().ajax.reload();
                    },
                    error: function (xhr) {
                        alert('An error occurred while deleting the product.');
                    }
                });
            }
        }

    </script>
@endsection
