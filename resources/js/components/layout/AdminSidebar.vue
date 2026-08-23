<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import IconDashboard from '../icons/IconDashboard.vue';
import IconBox from '../icons/IconBox.vue';
import IconLayers from '../icons/IconLayers.vue';
import IconHistory from '../icons/IconHistory.vue';
import IconLogout from '../icons/IconLogout.vue';

const auth = useAuthStore();
const router = useRouter();

const navGroups = [
    {
        label: 'Gudang',
        items: [
            { to: { name: 'admin.dashboard' }, label: 'Dashboard', icon: IconDashboard },
            { to: { name: 'admin.products.index' }, label: 'Master Barang', icon: IconBox },
            { to: { name: 'admin.stockProducts.index' }, label: 'Stok Barang', icon: IconLayers },
            { to: { name: 'admin.transactionHistory.index' }, label: 'Riwayat Transaksi', icon: IconHistory },
        ],
    },
];

async function handleLogout() {
    await auth.logout();
    router.push({ name: 'login' });
}
</script>

<template>
    <aside class="flex h-full w-64 flex-col border-r border-slate-200 bg-white">
        <div class="flex items-center gap-2 px-6 py-5">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-700 text-white">📦</span>
            <span class="text-lg font-semibold text-slate-900">Gudangku</span>
        </div>

        <nav class="flex-1 overflow-y-auto px-4 py-2">
            <div v-for="group in navGroups" :key="group.label" class="mb-6">
                <p class="px-2 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">{{ group.label }}</p>
                <ul class="space-y-1">
                    <li v-for="item in group.items" :key="item.label">
                        <RouterLink
                            :to="item.to"
                            class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-emerald-50 hover:text-emerald-700"
                            active-class="bg-emerald-50 text-emerald-700"
                        >
                            <component :is="item.icon" />
                            {{ item.label }}
                        </RouterLink>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="border-t border-slate-200 p-4">
            <div class="mb-3 flex items-center gap-3 px-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-200 text-sm font-semibold text-slate-600">
                    {{ auth.user?.name?.charAt(0) ?? '?' }}
                </span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium text-slate-900">{{ auth.user?.name }}</p>
                    <p class="truncate text-xs text-slate-500">{{ auth.user?.email }}</p>
                </div>
            </div>
            <button
                type="button"
                class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-red-50 hover:text-red-700"
                @click="handleLogout"
            >
                <IconLogout /> Keluar
            </button>
        </div>
    </aside>
</template>
