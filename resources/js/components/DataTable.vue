<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import IconChevronUpDown from './icons/IconChevronUpDown.vue';

const props = defineProps({
    columns: { type: Array, required: true }, // [{ key, label, sortable }]
    fetchFn: { type: Function, required: true }, // (params) => Promise<{ data, meta }>
    filterFields: { type: Array, default: () => [] }, // [{ key, label, placeholder }]
    initialSort: { type: String, default: '' },
});

const rows = ref([]);
const meta = ref({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
const loading = ref(false);
const error = ref('');

const filters = reactive({});
props.filterFields.forEach((f) => { filters[f.key] = ''; });

const state = reactive({
    page: 1,
    sort: props.initialSort,
    direction: 'asc',
});

let debounceTimer = null;

async function load() {
    loading.value = true;
    error.value = '';

    try {
        const params = {
            page: state.page,
            sort: state.sort || undefined,
            direction: state.direction,
            ...filters,
        };

        const result = await props.fetchFn(params);
        rows.value = result.data;
        meta.value = result.meta ?? { current_page: 1, last_page: 1, total: result.data.length, per_page: result.data.length };
    } catch (e) {
        error.value = 'Gagal memuat data.';
    } finally {
        loading.value = false;
    }
}

function toggleSort(column) {
    if (!column.sortable) return;

    if (state.sort === column.key) {
        state.direction = state.direction === 'asc' ? 'desc' : 'asc';
    } else {
        state.sort = column.key;
        state.direction = 'asc';
    }
    state.page = 1;
    load();
}

function goToPage(page) {
    if (page < 1 || page > meta.value.last_page) return;
    state.page = page;
    load();
}

watch(filters, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        state.page = 1;
        load();
    }, 350);
}, { deep: true });

onMounted(load);

defineExpose({ reload: load });
</script>

<template>
    <div>
        <div v-if="filterFields.length" class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div v-for="f in filterFields" :key="f.key">
                <input
                    v-model="filters[f.key]"
                    type="text"
                    :placeholder="f.placeholder || f.label"
                    class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600"
                />
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            class="px-4 py-3 text-left font-medium text-slate-600 select-none"
                            :class="col.sortable ? 'cursor-pointer hover:text-slate-900' : ''"
                            @click="toggleSort(col)"
                        >
                            <span class="inline-flex items-center gap-1">
                                {{ col.label }}
                                <IconChevronUpDown v-if="col.sortable" class="opacity-50" />
                            </span>
                        </th>
                        <th v-if="$slots.actions" class="px-4 py-3 text-right font-medium text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-if="loading">
                        <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-4 py-6 text-center text-slate-400">
                            Memuat...
                        </td>
                    </tr>
                    <tr v-else-if="error">
                        <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-4 py-6 text-center text-red-500">
                            {{ error }}
                        </td>
                    </tr>
                    <tr v-else-if="rows.length === 0">
                        <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-4 py-6 text-center text-slate-400">
                            Tidak ada data.
                        </td>
                    </tr>
                    <tr v-for="row in rows" v-else :key="row.id" class="hover:bg-slate-50">
                        <td v-for="col in columns" :key="col.key" class="px-4 py-3 text-slate-700">
                            <slot :name="`cell-${col.key}`" :row="row">{{ row[col.key] }}</slot>
                        </td>
                        <td v-if="$slots.actions" class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <slot name="actions" :row="row" />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="meta.last_page > 1" class="mt-4 flex items-center justify-between text-sm text-slate-600">
            <span>Halaman {{ meta.current_page }} dari {{ meta.last_page }} ({{ meta.total }} data)</span>
            <div class="flex gap-2">
                <button
                    type="button"
                    class="rounded-md border border-slate-300 px-3 py-1.5 disabled:opacity-40"
                    :disabled="meta.current_page <= 1"
                    @click="goToPage(meta.current_page - 1)"
                >
                    Sebelumnya
                </button>
                <button
                    type="button"
                    class="rounded-md border border-slate-300 px-3 py-1.5 disabled:opacity-40"
                    :disabled="meta.current_page >= meta.last_page"
                    @click="goToPage(meta.current_page + 1)"
                >
                    Selanjutnya
                </button>
            </div>
        </div>
    </div>
</template>
