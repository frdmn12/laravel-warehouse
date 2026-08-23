<script setup>
const stats = [
    { label: 'Master Barang', value: 'Terpusat' },
    { label: 'Perhitungan Stok', value: 'Metode FIFO' },
    { label: 'Riwayat Transaksi', value: 'Jejak Audit Lengkap' },
];
</script>

<template>
    <section class="border-y border-slate-200 bg-slate-50">
        <div class="mx-auto grid max-w-6xl grid-cols-1 gap-8 px-6 py-10 sm:grid-cols-3">
            <div v-for="stat in stats" :key="stat.label">
                <p class="text-xl font-bold text-slate-900">{{ stat.value }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ stat.label }}</p>
            </div>
        </div>
    </section>
</template>
