<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import AuthLayout from '../../components/layout/AuthLayout.vue';

const auth = useAuthStore();
const router = useRouter();

const form = reactive({ name: '', email: '', password: '', password_confirmation: '' });
const errors = ref({});
const generalError = ref('');
const submitting = ref(false);

async function submit() {
    submitting.value = true;
    errors.value = {};
    generalError.value = '';

    try {
        await auth.register(form);
        router.push({ name: 'admin.dashboard' });
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors || {};
            generalError.value = e.response.data.message || '';
        } else {
            generalError.value = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <AuthLayout title="Buat akun baru" subtitle="Mulai kelola gudang Anda hari ini.">
        <form class="space-y-4" @submit.prevent="submit">
            <div v-if="generalError" class="rounded-md bg-red-50 border border-red-200 px-3 py-2 text-sm text-red-700">
                {{ generalError }}
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama</label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    autofocus
                    class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600"
                />
                <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name[0] }}</p>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600"
                />
                <p v-if="errors.email" class="mt-1 text-xs text-red-600">{{ errors.email[0] }}</p>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    required
                    class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600"
                />
                <p v-if="errors.password" class="mt-1 text-xs text-red-600">{{ errors.password[0] }}</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    required
                    class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600"
                />
            </div>

            <button
                type="submit"
                :disabled="submitting"
                class="w-full rounded-md bg-emerald-700 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800 disabled:opacity-60"
            >
                {{ submitting ? 'Memproses...' : 'Daftar' }}
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            Sudah punya akun?
            <RouterLink to="/login" class="font-medium text-emerald-700 hover:text-emerald-800">Masuk</RouterLink>
        </p>
    </AuthLayout>
</template>
