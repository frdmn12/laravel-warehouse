<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import { useRouter, RouterLink } from 'vue-router';
import IconArrowLeft from '../../../components/icons/IconArrowLeft.vue';
import * as productsApi from '../../../api/products';
import * as locationsApi from '../../../api/locations';
import * as transactionHistoryApi from '../../../api/transactionHistory';
import * as stockProductsApi from '../../../api/stockProducts';

const router = useRouter();

const today = new Date().toISOString().slice(0, 10);

const form = reactive({
    transaction_type: '',
    batch: '',
    product_id: '',
    stock: '',
    date_of_entry: today,
    location_id: '',
});

const products = ref([]);
const locations = ref([]);
const submitting = ref(false);
const generatingBatch = ref(false);
const errorMessage = ref('');
const errors = ref({});
const successMessage = ref('');

onMounted(async () => {
    const [productList, locationList] = await Promise.all([
        productsApi.list({ all: 1 }),
        locationsApi.list(),
    ]);
    products.value = productList.data;
    locations.value = locationList;
});

watch(() => form.transaction_type, async (type) => {
    if (!type) return;

    generatingBatch.value = true;
    try {
        const apiType = type === 'MASUK' ? 'in' : 'out';
        form.batch = await transactionHistoryApi.generateBatch(apiType, form.date_of_entry);
    } catch (e) {
        form.batch = '';
    } finally {
        generatingBatch.value = false;
    }
});

async function submit() {
    submitting.value = true;
    errorMessage.value = '';
    errors.value = {};
    successMessage.value = '';

    try {
        await stockProductsApi.create({
            transaction_type: form.transaction_type,
            batch: form.batch,
            product_id: form.product_id || null,
            stock: parseInt(form.stock || '0', 10),
            date_of_entry: form.date_of_entry,
            location_id: form.location_id || null,
        });
        successMessage.value = 'Transaksi berhasil disimpan.';
        router.push({ name: 'admin.stockProducts.index' });
    } catch (e) {
        if (e.response?.status === 422) {
            errorMessage.value = e.response.data.message || 'Validasi gagal.';
            errors.value = e.response.data.errors || {};
        } else {
            errorMessage.value = 'Terjadi kesalahan saat menyimpan transaksi.';
        }
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <div class="max-w-2xl">
        <RouterLink :to="{ name: 'admin.stockProducts.index' }" class="mb-4 inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900">
            <IconArrowLeft /> Kembali
        </RouterLink>

        <h1 class="text-2xl font-bold text-slate-900 mb-6">Transaksi Stok Baru</h1>

        <form class="space-y-4 rounded-lg border border-slate-200 bg-white p-6" @submit.prevent="submit">
            <div v-if="errorMessage" class="rounded-md bg-red-50 border border-red-200 px-3 py-2 text-sm text-red-700">
                {{ errorMessage }}
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="transaction_type" class="block text-sm font-medium text-slate-700 mb-1">Jenis Transaksi</label>
                    <select id="transaction_type" v-model="form.transaction_type" required class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="MASUK">MASUK</option>
                        <option value="KELUAR">KELUAR</option>
                    </select>
                </div>

                <div>
                    <label for="batch" class="block text-sm font-medium text-slate-700 mb-1">Batch</label>
                    <input
                        id="batch"
                        v-model="form.batch"
                        type="text"
                        required
                        readonly
                        :placeholder="generatingBatch ? 'Membuat batch...' : 'Pilih jenis transaksi dahulu'"
                        class="w-full rounded-md border border-slate-300 bg-slate-50 px-3 py-2 text-sm"
                    />
                </div>

                <div>
                    <label for="product_id" class="block text-sm font-medium text-slate-700 mb-1">Barang</label>
                    <select id="product_id" v-model="form.product_id" required class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600">
                        <option value="">-- Pilih Barang --</option>
                        <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }} ({{ p.product_code }})</option>
                    </select>
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-slate-700 mb-1">Jumlah</label>
                    <input id="stock" v-model="form.stock" type="number" min="1" step="1" required placeholder="0" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600" />
                </div>

                <div>
                    <label for="date_of_entry" class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                    <input id="date_of_entry" v-model="form.date_of_entry" type="date" required class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600" />
                </div>

                <div>
                    <label for="location_id" class="block text-sm font-medium text-slate-700 mb-1">Lokasi</label>
                    <select id="location_id" v-model="form.location_id" required class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600">
                        <option value="">-- Pilih Lokasi --</option>
                        <option v-for="l in locations" :key="l.id" :value="l.id">{{ l.name }}</option>
                    </select>
                </div>
            </div>

            <button type="submit" :disabled="submitting" class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800 disabled:opacity-60">
                {{ submitting ? 'Menyimpan...' : 'Simpan Transaksi' }}
            </button>
        </form>
    </div>
</template>
