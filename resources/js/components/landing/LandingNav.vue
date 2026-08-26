<script setup>
</script>

<template>
    <header class="border-b border-slate-200">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <RouterLink to="/" class="flex items-center gap-2 text-lg font-semibold text-slate-900">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-700 text-white">📦</span>
                Gudangku
            </RouterLink>

            <div class="flex items-center gap-3">
                <RouterLink to="/login" class="text-sm font-medium text-slate-600 hover:text-slate-900">Masuk</RouterLink>
                <RouterLink
                    to="/register"
                    class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800"
                >
                    Daftar Gratis
                </RouterLink>
            </div>
        </nav>
    </header>
</template>
