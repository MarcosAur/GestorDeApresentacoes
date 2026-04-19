import { defineStore } from 'pinia';
import axios from 'axios';

export const useContestStore = defineStore('contest', {
    state: () => ({
        contests: [],
        pagination: null,
        loading: false,
        errors: null,
        options: {
            events: [],
            jurors: []
        }
    }),

    actions: {
        async fetchContests(page = 1) {
            this.loading = true;
            try {
                const response = await axios.get(`/api/contests?page=${page}`);
                this.contests = response.data.data;
                this.pagination = {
                    current_page: response.data.current_page,
                    last_page: response.data.last_page,
                    total: response.data.total
                };
            } catch (error) {
                console.error('Failed to fetch contests', error);
            } finally {
                this.loading = false;
            }
        },

        async fetchOptions() {
            try {
                const [eventsRes, jurorsRes] = await Promise.all([
                    axios.get('/api/events?all=true'),
                    axios.get('/api/jurors?all=true')
                ]);
                this.options.events = eventsRes.data;
                this.options.jurors = jurorsRes.data;
            } catch (error) {
                console.error('Failed to fetch options', error);
            }
        },

        async saveContest(contestData) {
            this.loading = true;
            this.errors = null;
            try {
                if (contestData.id) {
                    await axios.put(`/api/contests/${contestData.id}`, contestData);
                } else {
                    await axios.post('/api/contests', contestData);
                }
                await this.fetchContests();
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors;
                }
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteContest(id) {
            this.loading = true;
            try {
                await axios.delete(`/api/contests/${id}`);
                await this.fetchContests();
            } catch (error) {
                console.error('Failed to delete contest', error);
            } finally {
                this.loading = false;
            }
        }
    }
});
