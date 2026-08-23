<script setup>
defineProps({
    open: { type: Boolean, default: false },
    title: { type: String, default: 'Konfirmasi' },
    message: { type: String, default: 'Apakah Anda yakin?' },
});

const emit = defineEmits(['confirm', 'cancel']);
</script>

<template>
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 px-4">
        <div class="w-full max-w-sm rounded-lg bg-white p-6 shadow-xl">
            <h3 class="text-base font-semibold text-slate-900">{{ title }}</h3>
            <p class="mt-2 text-sm text-slate-500">{{ message }}</p>
            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    class="rounded-md border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    @click="emit('cancel')"
                >
                    Batal
                </button>
                <button
                    type="button"
                    class="rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                    @click="emit('confirm')"
                >
                    Hapus
                </button>
            </div>
        </div>
    </div>
</template>
