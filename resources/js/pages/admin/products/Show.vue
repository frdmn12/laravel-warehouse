<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import IconArrowLeft from '../../../components/icons/IconArrowLeft.vue';
import * as productsApi from '../../../api/products';

const props = defineProps({ id: [String, Number] });
const product = ref(null);

onMounted(async () => {
    product.value = await productsApi.get(props.id);
});
</script>

<template>
    <div class="max-w-xl">
        <RouterLink :to="{ name: 'admin.products.index' }" class="mb-4 inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900">
            <IconArrowLeft /> Kembali
        </RouterLink>

        <h1 class="text-2xl font-bold text-slate-900 mb-6">Detail Barang</h1>

        <dl v-if="product" class="divide-y divide-slate-100 rounded-lg border border-slate-200 bg-white">
            <div class="grid grid-cols-3 gap-4 px-6 py-4">
                <dt class="text-sm font-medium text-slate-500">Nama Barang</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ product.name }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 px-6 py-4">
                <dt class="text-sm font-medium text-slate-500">Kode Barang</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ product.product_code }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 px-6 py-4">
                <dt class="text-sm font-medium text-slate-500">Dibuat oleh</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ product.created_by_name || '-' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 px-6 py-4">
                <dt class="text-sm font-medium text-slate-500">Diubah oleh</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ product.updated_by_name || '-' }}</dd>
            </div>
        </dl>
    </div>
</template>
