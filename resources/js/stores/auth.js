import { defineStore } from 'pinia';
import * as authApi from '../api/auth';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        checked: false,
    }),

    getters: {
        isAuthenticated: (state) => state.user !== null,
    },

    actions: {
        async fetchUser() {
            try {
                this.user = await authApi.fetchUser();
            } catch (e) {
                this.user = null;
            } finally {
                this.checked = true;
            }

            return this.user;
        },

        async login(credentials) {
            this.user = await authApi.login(credentials);
            this.checked = true;
            return this.user;
        },

        async register(payload) {
            this.user = await authApi.register(payload);
            this.checked = true;
            return this.user;
        },

        async logout() {
            await authApi.logout();
            this.user = null;
        },
    },
});
