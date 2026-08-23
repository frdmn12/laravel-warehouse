<script setup>
</script>

<template>
    <section class="mx-auto max-w-6xl px-6 py-20 sm:py-28">
        <div class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
            Terhubung &middot; Akurat &middot; Berbasis FIFO
        </div>

        <h1 class="mt-6 max-w-3xl text-5xl font-extrabold leading-[1.05] tracking-tight text-slate-900 sm:text-6xl">
            Sistem Manajemen Gudang untuk stok yang selalu akurat.
        </h1>

        <p class="mt-6 max-w-xl text-lg text-slate-600">
            Kelola master barang, catat stok masuk dan keluar dengan metode FIFO, serta pantau riwayat
            transaksi lengkap dalam satu dashboard yang sederhana.
        </p>

        <div class="mt-8 flex flex-wrap items-center gap-4">
            <RouterLink
                to="/register"
                class="rounded-md bg-emerald-700 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-800"
            >
                Mulai Sekarang
            </RouterLink>
            <RouterLink
                to="/login"
                class="rounded-md border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
                Masuk ke Akun
            </RouterLink>
        </div>
    </section>
</template>
