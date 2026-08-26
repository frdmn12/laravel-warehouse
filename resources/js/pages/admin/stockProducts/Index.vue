<script setup>
import { RouterLink } from 'vue-router';
import DataTable from '../../../components/DataTable.vue';
import IconPlus from '../../../components/icons/IconPlus.vue';
import * as stockProductsApi from '../../../api/stockProducts';

const columns = [
    { key: 'product_name', label: 'Nama Barang', sortable: true },
    { key: 'product_code', label: 'Kode Barang', sortable: true },
    { key: 'location_name', label: 'Lokasi', sortable: true },
    { key: 'stock', label: 'Stok', sortable: true },
    { key: 'date_of_entry', label: 'Tanggal', sortable: true },
];

const filterFields = [
    { key: 'product_name', label: 'Cari nama barang' },
    { key: 'product_code', label: 'Cari kode barang' },
    { key: 'location', label: 'Cari lokasi' },
];

function fetchStockProducts(params) {
    return stockProductsApi.list(params);
}
</script>

<template>
    <div>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Stok Barang</h1>
                <p class="mt-1 text-sm text-slate-500">Saldo stok per barang dan lokasi (FIFO per batch tanggal masuk).</p>
            </div>
            <RouterLink
                :to="{ name: 'admin.stockProducts.create' }"
                class="inline-flex items-center gap-2 rounded-md bg-emerald-700 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800"
            >
                <IconPlus /> Transaksi Baru
            </RouterLink>
        </div>

        <DataTable :columns="columns" :fetch-fn="fetchStockProducts" :filter-fields="filterFields" initial-sort="date_of_entry" />
    </div>
</template>
