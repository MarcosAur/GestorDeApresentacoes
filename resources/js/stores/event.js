import { defineStore } from 'pinia';
import axios from 'axios';

export const useEventStore = defineStore('event', {
    state: () => ({
        events: [],
        pagination: null,
        loading: false,
        errors: null,
    }),

    actions: {
        async fetchEvents(page = 1) {
            this.loading = true;
            try {
                const response = await axios.get(`/api/events?page=${page}`);
                this.events = response.data.data;
                this.pagination = {
                    current_page: response.data.current_page,
                    last_page: response.data.last_page,
                    total: response.data.total
                };
            } catch (error) {
                console.error('Failed to fetch events', error);
            } finally {
                this.loading = false;
            }
        },

        async saveEvent(eventData) {
            this.loading = true;
            this.errors = null;
            try {
                if (eventData.id) {
                    await axios.put(`/api/events/${eventData.id}`, eventData);
                } else {
                    await axios.post('/api/events', eventData);
                }
                await this.fetchEvents();
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors;
                }
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteEvent(id) {
            this.loading = true;
            try {
                await axios.delete(`/api/events/${id}`);
                await this.fetchEvents();
            } catch (error) {
                if (error.response?.data?.message) {
                    alert(error.response.data.message);
                }
                throw error;
            } finally {
                this.loading = false;
            }
        }
    }
});
