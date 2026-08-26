<script setup>
import DataTable from '../../../components/DataTable.vue';
import * as transactionHistoryApi from '../../../api/transactionHistory';

const columns = [
    { key: 'batch', label: 'Batch', sortable: true },
    { key: 'date_of_transaction', label: 'Tanggal Transaksi', sortable: true },
    { key: 'hour', label: 'Jam' },
    { key: 'product_code', label: 'Kode Barang' },
    { key: 'location_name', label: 'Lokasi' },
    { key: 'qty', label: 'Qty', sortable: true },
];

const filterFields = [
    { key: 'batch', label: 'Cari batch' },
];

function fetchTransactions(params) {
    return transactionHistoryApi.list(params);
}
</script>

<template>
    <div>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Riwayat Transaksi</h1>
            <p class="mt-1 text-sm text-slate-500">Jejak audit seluruh transaksi stok masuk dan keluar.</p>
        </div>

        <DataTable :columns="columns" :fetch-fn="fetchTransactions" :filter-fields="filterFields" initial-sort="date_of_transaction" />
    </div>
</template>
