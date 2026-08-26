<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useRouter, RouterLink } from 'vue-router';
import IconArrowLeft from '../../../components/icons/IconArrowLeft.vue';
import * as productsApi from '../../../api/products';

const props = defineProps({ id: [String, Number] });
const router = useRouter();
const form = reactive({ name: '', product_code: '' });
const errors = ref({});
const submitting = ref(false);
const loading = ref(true);

onMounted(async () => {
    const product = await productsApi.get(props.id);
    form.name = product.name;
    form.product_code = product.product_code;
    loading.value = false;
});

async function submit() {
    submitting.value = true;
    errors.value = {};

    try {
        await productsApi.update(props.id, form);
        router.push({ name: 'admin.products.index' });
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors || {};
        }
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <div class="max-w-xl">
        <RouterLink :to="{ name: 'admin.products.index' }" class="mb-4 inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900">
            <IconArrowLeft /> Kembali
        </RouterLink>

        <h1 class="text-2xl font-bold text-slate-900 mb-6">Edit Barang</h1>

        <form v-if="!loading" class="space-y-4 rounded-lg border border-slate-200 bg-white p-6" @submit.prevent="submit">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Barang</label>
                <input id="name" v-model="form.name" type="text" required class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600" />
                <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name[0] }}</p>
            </div>

            <div>
                <label for="product_code" class="block text-sm font-medium text-slate-700 mb-1">Kode Barang</label>
                <input id="product_code" v-model="form.product_code" type="text" required class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600" />
                <p v-if="errors.product_code" class="mt-1 text-xs text-red-600">{{ errors.product_code[0] }}</p>
            </div>

            <button type="submit" :disabled="submitting" class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800 disabled:opacity-60">
                {{ submitting ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
        </form>
    </div>
</template>
