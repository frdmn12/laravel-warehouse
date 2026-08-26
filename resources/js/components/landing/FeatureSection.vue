<script setup>
const features = [
    {
        number: '01',
        title: 'Master Barang',
        description: 'Kelola data barang beserta kode unik masing-masing, siap digunakan di seluruh transaksi gudang.',
    },
    {
        number: '02',
        title: 'Stok Masuk & Keluar (FIFO)',
        description: 'Setiap transaksi masuk membentuk batch berdasarkan tanggal, dan transaksi keluar otomatis mengurangi batch tertua lebih dulu.',
    },
    {
        number: '03',
        title: 'Riwayat Transaksi',
        description: 'Seluruh pergerakan stok tercatat lengkap dengan nomor batch, tanggal, lokasi, dan jumlah untuk kebutuhan audit.',
    },
];
</script>

<template>
    <section class="mx-auto max-w-6xl px-6 py-20">
        <h2 class="text-3xl font-bold text-slate-900">Semua yang dibutuhkan untuk mengelola gudang</h2>
        <p class="mt-3 max-w-xl text-slate-600">Tiga modul inti yang saling terhubung, dari data barang hingga jejak audit stok.</p>

        <div class="mt-12 divide-y divide-slate-200 border-y border-slate-200">
            <div v-for="feature in features" :key="feature.number" class="grid grid-cols-1 gap-4 py-8 sm:grid-cols-[80px_1fr]">
                <span class="text-sm font-semibold text-emerald-700">{{ feature.number }}</span>
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">{{ feature.title }}</h3>
                    <p class="mt-2 max-w-2xl text-sm text-slate-600">{{ feature.description }}</p>
                </div>
            </div>
        </div>
    </section>
</template>
