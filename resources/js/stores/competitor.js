import { defineStore } from 'pinia';
import axios from 'axios';

export const useCompetitorStore = defineStore('competitor', {
    state: () => ({
        presentations: [],
        documents: [],
        availableContests: [],
        loading: false,
        errors: null,
    }),

    actions: {
        async fetchDashboardData() {
            this.loading = true;
            try {
                const [presRes, docRes, contestRes] = await Promise.all([
                    axios.get('/api/presentations'),
                    axios.get('/api/documents'),
                    axios.get('/api/contests?all=true')
                ]);
                this.presentations = presRes.data;
                this.documents = docRes.data;
                
                // Filter contests that competitor is not yet enrolled in
                const enrolledContestIds = this.presentations.map(p => p.contest_id);
                this.availableContests = contestRes.data.filter(c => 
                    c.status === 'AGENDADO' && !enrolledContestIds.includes(c.id)
                );
            } catch (error) {
                console.error('Failed to fetch dashboard data', error);
            } finally {
                this.loading = false;
            }
        },

        async enroll(enrollmentData) {
            this.loading = true;
            this.errors = null;
            try {
                await axios.post('/api/presentations', enrollmentData);
                await this.fetchDashboardData();
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors;
                }
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async uploadDocument(formData) {
            this.loading = true;
            this.errors = null;
            try {
                await axios.post('/api/documents', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                await this.fetchDashboardData();
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors;
                }
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deletePresentation(id) {
            try {
                await axios.delete(`/api/presentations/${id}`);
                await this.fetchDashboardData();
            } catch (error) {
                console.error('Failed to delete presentation', error);
            }
        },

        async deleteDocument(id) {
            try {
                await axios.delete(`/api/documents/${id}`);
                await this.fetchDashboardData();
            } catch (error) {
                console.error('Failed to delete document', error);
            }
        }
    }
});
