@extends('layout.layout1')

@section('title', 'Dashboard')
@section('content')
    <section class="max-w-7xl mx-auto p-6 shadow-md rounded-lg bg-white">
        <h1 class="text-3xl font-bold mb-6">Stock Products</h1>
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
                    <th>Lokasi</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Saldo</th>
                    <th>Tanggal Masuk</th>
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
                    { data: 'location' },
                    { data: 'product_code' },
                    { data: 'product_name' },
                    { data: 'stock' },
                    { data: 'date_added' },
                ]
            });

            $('#search-product').on('submit', function (e) {
                e.preventDefault();
                oTable.ajax.reload();
            });

        });
    </script>
@endsection
