import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        loading: false,
        errors: null,
    }),

    getters: {
        isLoggedIn: (state) => !!state.user,
        isAdmin: (state) => state.user?.role?.slug === 'admin',
        isJurado: (state) => state.user?.role?.slug === 'jurado',
        isCompetidor: (state) => state.user?.role?.slug === 'competidor',
    },

    actions: {
        async login(credentials) {
            this.loading = true;
            this.errors = null;
            try {
                await axios.get('/sanctum/csrf-cookie');
                const response = await axios.post('/api/login', credentials);
                this.user = response.data.user;
                return response.data;
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors;
                }
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async register(userData) {
            this.loading = true;
            this.errors = null;
            try {
                await axios.get('/sanctum/csrf-cookie');
                const response = await axios.post('/api/register', userData);
                this.user = response.data.user;
                return response.data;
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors;
                }
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            try {
                await axios.post('/api/logout');
                this.user = null;
            } catch (error) {
                console.error('Logout failed', error);
            }
        },

        async fetchUser() {
            try {
                const response = await axios.get('/api/user');
                this.user = response.data;
            } catch (error) {
                this.user = null;
            }
        }
    }
});
