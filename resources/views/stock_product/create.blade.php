@extends('layout.layout1')

@section('title', 'Create Stock Product')
@section('content')
    <section class="max-w-7xl mx-auto p-6 shadow-md rounded-lg bg-white">
        {{-- button back --}}
        <a href="javascript:history.back()" class="mb-4 inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>
            Back
        </a>

        <h1 class="text-3xl font-bold mb-6">Create Stock Product (New Transaction)</h1>

        <form method="POST" id="form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    {{-- transaction_type (MASUK || KELUAR) --}}
                    <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-1">Transaction Type</label>
                    <select id="transaction_type" name="transaction_type" class="w-full text-sm border border-gray-300 rounded-md p-2" onclick="generateBatch()" required>
                        <option value="">-- Select Transaction Type --</option>
                        <option value="MASUK">MASUK</option>
                        <option value="KELUAR">KELUAR</option>
                    </select>
                </div>

                <div>
                    {{-- Batch --}}
                    <label for="batch" class="block text-sm font-medium text-gray-700 mb-1">Batch</label>
                    <input type="text" id="batch" name="batch" class="w-full text-sm border border-gray-300 rounded-md p-2" placeholder="Batch identifier" required readonly >
                </div>

                <div>
                    <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                    <select id="product_id" name="product_id" class="w-full text-sm border border-gray-300 rounded-md p-2" required>
                        <option value="">-- Select Product --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name ?? $product->product_name ?? $product->product_code ?? 'Product '.$product->id }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                    <input type="number" id="stock" name="stock" class="w-full text-sm border border-gray-300 rounded-md p-2" min="0" step="1" placeholder="0" required>
                </div>

                <div>
                    <label for="date_of_entry" class="block text-sm font-medium text-gray-700 mb-1">Date of Entry</label>
                    <input type="date" id="date_of_entry" name="date_of_entry" class="w-full text-sm border border-gray-300 rounded-md p-2" value="{{ date('Y-m-d') }}" required>
                </div>

                <div>
                    <label for="location_id" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    @if(isset($locations) && count($locations))
                        <select id="location_id" name="location_id" class="w-full text-sm border border-gray-300 rounded-md p-2" required>
                            <option value="">-- Select Location --</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name ?? $location->location_name ?? 'Location '.$location->id }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" id="location_id" name="location_id" class="w-full text-sm border border-gray-300 rounded-md p-2" placeholder="Location id or name" required>
                    @endif
                </div>
            </div>

            {{-- Hidden audit fields (can be handled server-side as well) --}}
            <input type="hidden" name="created_at" value="{{ now() }}">
            <input type="hidden" name="created_by" value="{{ auth()->id() }}">
            <input type="hidden" name="updated_at" value="{{ now() }}">
            <input type="hidden" name="updated_by" value="{{ auth()->id() }}">

            <button class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md">Create</button>
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

                // make button to disabled to prevent multiple submits
                $('button', this).attr('disabled', 'disabled');


                $.ajax({
                    type: 'POST',
                    url: '{{ route("stock-products.store") }}',
                    headers: {
                        'X-CSRF-TOKEN': '{!! csrf_token() !!}'
                    },
                    data: {
                        transaction_type: $('#transaction_type').val(),
                        batch: $('#batch').val(),
                        product_id: $('#product_id').val() || null,
                        stock: parseInt($('#stock').val() || '0', 10),
                        date_of_entry: $('#date_of_entry').val(),
                        location_id: $('#location_id').val() || null,
                    },
                    success: function (response) {
                        alert('New transaction created successfully!');
                        window.location.href = '{{ route("stock-products.index") }}';
                    },
                    error: function (xhr) {
                        let msg = 'An error occurred while creating the stock product.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        alert(msg);
                    }
                });

                // re-enable the button
                $('button', this).removeAttr('disabled');

            });
        });

        function generateBatch() {
            let transactionType = $('#transaction_type').val();
            let batch = '';

            // normalize transaction type (handle both "MASUK"/"KELUAR" and "in"/"out")
            let t = (transactionType || '').toString().toLowerCase();
            if (t === 'masuk') t = 'in';
            if (t === 'keluar') t = 'out';

            // prefer server-generated batch (server should handle increment & daily reset)
            $.ajax({
                url: '{{ route("transaction-history.generateBatch") }}',
                method: 'POST',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    transaction_type: t,
                    date: $('#date_of_entry').val() || new Date().toISOString().slice(0,10)
                },
                success: function (res) {
                    if (res && res.batch) {
                        batch = res.batch;
                    } else {
                        // fallback to client-side generator below
                        batch = '';
                    }
                    if (!batch) {
                        // fallback logic below will run after ajax success if server didn't return batch
                        // (move to fallback block)
                    }
                    $('#batch').val(batch);
                },
                error: function () {
                    // Server not available — fallback to client-side incremental generator with daily reset using localStorage
                    let today = ($('#date_of_entry').val() || new Date().toISOString().slice(0,10)).replace(/-/g,'');
                    let key = 'batch_' + t + '_' + today;
                    let cnt = parseInt(localStorage.getItem(key) || '0', 10) + 1;
                    localStorage.setItem(key, cnt);
                    let num = String(cnt).padStart(3, '0');
                    if (t === 'in') {
                        batch = 'TAMBAH' + num;
                    } else if (t === 'out') {
                        batch = 'KURANG' + num;
                    } else {
                        batch = 'BATCH' + num;
                    }
                    $('#batch').val(batch);
                }
            });

            // If server returned an empty response (handled above), ensure fallback runs to set a batch
            // (This covers the case success returned no batch)
            $(document).one('ajaxStop', function() {
                if (!$('#batch').val()) {
                    let tt = t;
                    let today = ($('#date_of_entry').val() || new Date().toISOString().slice(0,10)).replace(/-/g,'');
                    let key = 'batch_' + tt + '_' + today;
                    let cnt = parseInt(localStorage.getItem(key) || '0', 10) + 1;
                    localStorage.setItem(key, cnt);
                    let num = String(cnt).padStart(3, '0');
                    if (tt === 'in') {
                        batch = 'TAMBAH' + num;
                    } else if (tt === 'out') {
                        batch = 'KURANG' + num;
                    } else {
                        batch = 'BATCH' + num;
                    }
                    $('#batch').val(batch);
                }
            });

            $('#batch').val(batch);
        }


    </script>
@endsection
