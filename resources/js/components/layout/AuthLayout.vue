<script setup>
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
});
</script>

<template>
    <div class="min-h-screen flex bg-slate-50">
        <div class="hidden lg:flex lg:w-1/2 bg-emerald-700 text-white flex-col justify-between p-12">
            <RouterLink to="/" class="flex items-center gap-2 text-lg font-semibold">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white/15">📦</span>
                Gudangku
            </RouterLink>

            <div>
                <h2 class="text-3xl font-bold leading-tight mb-3">
                    Kelola stok gudang Anda dengan presisi FIFO.
                </h2>
                <p class="text-emerald-100 max-w-md">
                    Pantau master barang, stok masuk/keluar, dan riwayat transaksi dalam satu dashboard.
                </p>
            </div>

            <p class="text-sm text-emerald-200">&copy; {{ new Date().getFullYear() }} Gudangku</p>
        </div>

        <div class="flex flex-1 items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-sm">
                <div class="mb-8 lg:hidden">
                    <RouterLink to="/" class="inline-flex items-center gap-2 text-lg font-semibold text-slate-900">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-700 text-white">📦</span>
                        Gudangku
                    </RouterLink>
                </div>

                <h1 class="text-2xl font-bold text-slate-900">{{ title }}</h1>
                <p v-if="subtitle" class="mt-1 text-sm text-slate-500">{{ subtitle }}</p>

                <div class="mt-8">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
