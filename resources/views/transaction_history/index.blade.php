@extends('layout.layout1')

@section('title', 'Dashboard')
@section('content')
    <section class="max-w-7xl mx-auto p-6 shadow-md rounded-lg bg-white">
        <h1 class="text-3xl font-bold mb-6">Transaction History</h1>
        {{-- <div class="flex">
            <a href="{{ route('transaction-history.create') }}" class="bg-gray-400 text-[11px] text-white px-2 py-1 rounded-sm mb-4 inline-flex items-center">
                <i class="fas fa-plus mr-2 text-xs"></i>
                Add
            </a>
        </div> --}}
        <form method="GET" id="search-product">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="batch" class="block text-sm font-medium text-gray-700 mb-1">Batch</label>
                    <input type="text" id="batch" name="batch" class="w-full text-sm border border-gray-300 rounded-md p-2" placeholder="">
                </div>
            </div>
            <button class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md">Search</button>
        </form>
    </section>

    <section class="shadow-md rounded-lg bg-white mt-2 p-2">
        <table class="" id="main-table" width="100%">
            <thead class="text-nowrap">
                <tr>
                    <th>Batch</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Lokasi</th>
                    <th>Kode Barang</th>
                    <th>Tanggal Masuk</th>
                    <th>Qty</th>
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
                    url: "{{ route('transaction-history.data') }}",
                    type: "POST",
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: function (d) {
                        d.batch = $('#batch').val();
                    }
                },
                columns: [
                    { data: 'batch' },
                    { data: 'date' },
                    { data: 'hour' },
                    { data: 'location_name' },
                    { data: 'product_code' },
                    { data: 'date_of_transaction' },
                    { data: 'qty' },
                ]
            });

            $('#search-product').on('submit', function (e) {
                e.preventDefault();
                oTable.ajax.reload();
            });

        });
    </script>
@endsection
