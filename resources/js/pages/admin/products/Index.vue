<script setup>
import { ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import DataTable from '../../../components/DataTable.vue';
import ConfirmDialog from '../../../components/ConfirmDialog.vue';
import IconEye from '../../../components/icons/IconEye.vue';
import IconPencil from '../../../components/icons/IconPencil.vue';
import IconTrash from '../../../components/icons/IconTrash.vue';
import IconPlus from '../../../components/icons/IconPlus.vue';
import * as productsApi from '../../../api/products';

const router = useRouter();
const table = ref(null);
const confirmDelete = ref(null);

const columns = [
    { key: 'name', label: 'Nama Barang', sortable: true },
    { key: 'product_code', label: 'Kode Barang', sortable: true },
];

const filterFields = [
    { key: 'product_name', label: 'Cari nama barang' },
    { key: 'product_code', label: 'Cari kode barang' },
];

function fetchProducts(params) {
    return productsApi.list(params);
}

async function performDelete() {
    if (!confirmDelete.value) return;
    await productsApi.destroy(confirmDelete.value.id);
    confirmDelete.value = null;
    table.value?.reload();
}
</script>

<template>
    <div>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Master Barang</h1>
                <p class="mt-1 text-sm text-slate-500">Kelola daftar produk gudang.</p>
            </div>
            <RouterLink
                :to="{ name: 'admin.products.create' }"
                class="inline-flex items-center gap-2 rounded-md bg-emerald-700 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800"
            >
                <IconPlus /> Tambah Barang
            </RouterLink>
        </div>

        <DataTable ref="table" :columns="columns" :fetch-fn="fetchProducts" :filter-fields="filterFields" initial-sort="name">
            <template #actions="{ row }">
                <RouterLink :to="{ name: 'admin.products.show', params: { id: row.id } }" class="rounded p-1.5 text-slate-500 hover:bg-slate-100 hover:text-slate-800" title="Detail">
                    <IconEye />
                </RouterLink>
                <RouterLink :to="{ name: 'admin.products.edit', params: { id: row.id } }" class="rounded p-1.5 text-slate-500 hover:bg-slate-100 hover:text-slate-800" title="Edit">
                    <IconPencil />
                </RouterLink>
                <button type="button" class="rounded p-1.5 text-red-500 hover:bg-red-50" title="Hapus" @click="confirmDelete = row">
                    <IconTrash />
                </button>
            </template>
        </DataTable>

        <ConfirmDialog
            :open="!!confirmDelete"
            title="Hapus Barang"
            :message="`Yakin ingin menghapus '${confirmDelete?.name}'?`"
            @cancel="confirmDelete = null"
            @confirm="performDelete"
        />
    </div>
</template>
