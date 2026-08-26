import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/',
        name: 'landing',
        component: () => import('../pages/Landing.vue'),
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../pages/auth/Login.vue'),
        meta: { guestOnly: true },
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../pages/auth/Register.vue'),
        meta: { guestOnly: true },
    },
    {
        path: '/admin',
        component: () => import('../components/layout/AdminLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            { path: '', redirect: { name: 'admin.dashboard' } },
            {
                path: 'dashboard',
                name: 'admin.dashboard',
                component: () => import('../pages/admin/Dashboard.vue'),
            },
            {
                path: 'products',
                name: 'admin.products.index',
                component: () => import('../pages/admin/products/Index.vue'),
            },
            {
                path: 'products/create',
                name: 'admin.products.create',
                component: () => import('../pages/admin/products/Create.vue'),
            },
            {
                path: 'products/:id/edit',
                name: 'admin.products.edit',
                component: () => import('../pages/admin/products/Edit.vue'),
                props: true,
            },
            {
                path: 'products/:id',
                name: 'admin.products.show',
                component: () => import('../pages/admin/products/Show.vue'),
                props: true,
            },
            {
                path: 'stock-products',
                name: 'admin.stockProducts.index',
                component: () => import('../pages/admin/stockProducts/Index.vue'),
            },
            {
                path: 'stock-products/create',
                name: 'admin.stockProducts.create',
                component: () => import('../pages/admin/stockProducts/Create.vue'),
            },
            {
                path: 'transaction-history',
                name: 'admin.transactionHistory.index',
                component: () => import('../pages/admin/transactionHistory/Index.vue'),
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to) => {
    const auth = useAuthStore();

    if (!auth.checked) {
        await auth.fetchUser();
    }

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        return { name: 'login', query: { redirect: to.fullPath } };
    }

    if (to.meta.guestOnly && auth.isAuthenticated) {
        return { name: 'admin.dashboard' };
    }

    return true;
});

export default router;
