<script setup>
import { onMounted, ref } from 'vue';
import { useAuthStore } from '../../stores/auth';
import IconBox from '../../components/icons/IconBox.vue';
import IconLayers from '../../components/icons/IconLayers.vue';
import IconMapPin from '../../components/icons/IconMapPin.vue';
import * as dashboardApi from '../../api/dashboard';

const auth = useAuthStore();
const data = ref(null);
const loading = ref(true);

const cards = [
    { key: 'total_products', label: 'Total Barang', icon: IconBox },
    { key: 'total_stock', label: 'Total Stok', icon: IconLayers },
    { key: 'total_locations', label: 'Total Lokasi', icon: IconMapPin },
];

onMounted(async () => {
    data.value = await dashboardApi.summary();
    loading.value = false;
});
</script>

<template>
    <div>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Selamat datang, {{ auth.user?.name }}</h1>
            <p class="mt-1 text-sm text-slate-500">Ringkasan kondisi gudang Anda saat ini.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div v-for="card in cards" :key="card.key" class="rounded-lg border border-slate-200 bg-white p-5">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                        <component :is="card.icon" />
                    </span>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ card.label }}</p>
                        <p class="text-xl font-bold text-slate-900">
                            {{ loading ? '...' : (data?.[card.key] ?? 0) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="mb-3 text-lg font-semibold text-slate-900">Transaksi Terbaru</h2>
            <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Batch</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Tanggal</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Barang</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Lokasi</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Qty</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-if="loading">
                            <td colspan="5" class="px-4 py-6 text-center text-slate-400">Memuat...</td>
                        </tr>
                        <tr v-else-if="!data?.recent_transactions?.length">
                            <td colspan="5" class="px-4 py-6 text-center text-slate-400">Belum ada transaksi.</td>
                        </tr>
                        <tr v-for="tx in data?.recent_transactions" v-else :key="tx.id" class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-700">{{ tx.batch }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ tx.date_of_transaction }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ tx.product_code }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ tx.location_name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ tx.qty }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
