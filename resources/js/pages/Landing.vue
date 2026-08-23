<script setup>
import LandingNav from '../components/landing/LandingNav.vue';
import Hero from '../components/landing/Hero.vue';
import StatsRow from '../components/landing/StatsRow.vue';
import FeatureSection from '../components/landing/FeatureSection.vue';
</script>

<template>
    <div class="bg-white">
        <LandingNav />
        <Hero />
        <StatsRow />
        <FeatureSection />

        <section class="mx-auto max-w-6xl px-6 pb-24">
            <div class="rounded-2xl bg-emerald-700 px-8 py-12 text-center sm:px-16">
                <h2 class="text-3xl font-bold text-white">Siap mengelola stok gudang Anda?</h2>
                <p class="mx-auto mt-3 max-w-md text-emerald-100">
                    Daftar dan mulai catat transaksi stok masuk dan keluar hari ini.
                </p>
                <RouterLink
                    to="/register"
                    class="mt-8 inline-block rounded-md bg-white px-6 py-3 text-sm font-semibold text-emerald-700 hover:bg-emerald-50"
                >
                    Daftar Gratis
                </RouterLink>
            </div>
        </section>

        <footer class="border-t border-slate-200 py-8">
            <div class="mx-auto max-w-6xl px-6 text-sm text-slate-500">
                &copy; {{ new Date().getFullYear() }} Gudangku. Sistem Manajemen Gudang.
            </div>
        </footer>
    </div>
</template>
