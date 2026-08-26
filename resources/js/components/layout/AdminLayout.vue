<script setup>
import AdminSidebar from './AdminSidebar.vue';
</script>

<template>
    <div class="flex h-screen bg-slate-50">
        <AdminSidebar />
        <main class="flex-1 overflow-y-auto">
            <div class="mx-auto max-w-6xl px-6 py-8 sm:px-8">
                <RouterView />
            </div>
        </main>
    </div>
</template>
