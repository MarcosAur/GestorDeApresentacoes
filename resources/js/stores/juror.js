import { defineStore } from 'pinia';
import axios from 'axios';

export const useJurorStore = defineStore('juror', {
    state: () => ({
        jurors: [],
        pagination: null,
        loading: false,
        errors: null,
        options: {
            contests: []
        }
    }),

    actions: {
        async fetchJurors(page = 1) {
            this.loading = true;
            try {
                const response = await axios.get(`/api/jurors?page=${page}`);
                this.jurors = response.data.data;
                this.pagination = {
                    current_page: response.data.current_page,
                    last_page: response.data.last_page,
                    total: response.data.total
                };
            } catch (error) {
                console.error('Failed to fetch jurors', error);
            } finally {
                this.loading = false;
            }
        },

        async fetchOptions() {
            try {
                const response = await axios.get('/api/contests?all=true');
                this.options.contests = response.data.data || response.data;
            } catch (error) {
                console.error('Failed to fetch options', error);
            }
        },

        async saveJuror(jurorData) {
            this.loading = true;
            this.errors = null;
            try {
                if (jurorData.id) {
                    await axios.put(`/api/jurors/${jurorData.id}`, jurorData);
                } else {
                    await axios.post('/api/jurors', jurorData);
                }
                await this.fetchJurors();
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors;
                }
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteJuror(id) {
            this.loading = true;
            try {
                await axios.delete(`/api/jurors/${id}`);
                await this.fetchJurors();
            } catch (error) {
                console.error('Failed to delete juror', error);
            } finally {
                this.loading = false;
            }
        }
    }
});
