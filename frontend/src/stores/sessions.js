import { defineStore } from 'pinia';
import sessionsApi from '@/api/endpoints/sessions';

export const useSessionsStore = defineStore('sessions', {
    state: () => ({
        sessions: [],
        archivedSessions: [],
        currentSession: null,
        specialties: [],
        studyTypes: {},
        loading: false,
        error: null
    }),

    getters: {
        currentSessions: (state) => state.sessions.filter(s => s.status !== 'terminée'),
        upcomingSessions: (state) => state.sessions.filter(s => s.status === 'à venir'),
        activeSessions: (state) => state.sessions.filter(s => s.status === 'en cours'),
        
        getSessionById: (state) => (id) => {
            return state.sessions.find(s => s.id === id) || 
                   state.archivedSessions.find(s => s.id === id);
        },

        studyTypesList: (state) => {
            return Object.entries(state.studyTypes).map(([key, label]) => ({
                value: key,
                label: label
            }));
        }
    },

    actions: {
        async fetchSessions() {
            this.loading = true;
            this.error = null;
            try {
                const response = await sessionsApi.getSessions();
                this.sessions = response.data.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Erreur lors du chargement des sessions';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchArchivedSessions() {
            this.loading = true;
            this.error = null;
            try {
                const response = await sessionsApi.getArchivedSessions();
                this.archivedSessions = response.data.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Erreur lors du chargement des sessions archivées';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchSession(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await sessionsApi.getSession(id);
                this.currentSession = response.data.data;
                return this.currentSession;
            } catch (error) {
                this.error = error.response?.data?.message || 'Erreur lors du chargement de la session';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async createSession(data) {
            this.loading = true;
            this.error = null;
            try {
                const response = await sessionsApi.createSession(data);
                await this.fetchSessions();
                return response.data.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Erreur lors de la création de la session';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateSession(id, data) {
            this.loading = true;
            this.error = null;
            try {
                const response = await sessionsApi.updateSession(id, data);
                await this.fetchSessions();
                return response.data.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Erreur lors de la mise à jour de la session';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteSession(id) {
            this.loading = true;
            this.error = null;
            try {
                await sessionsApi.deleteSession(id);
                await this.fetchSessions();
            } catch (error) {
                this.error = error.response?.data?.message || 'Erreur lors de la suppression de la session';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async addSpecialtyToSession(sessionId, data) {
            this.loading = true;
            this.error = null;
            try {
                const response = await sessionsApi.addSpecialtyToSession(sessionId, data);
                if (this.currentSession && this.currentSession.id === sessionId) {
                    await this.fetchSession(sessionId);
                }
                await this.fetchSessions();
                return response.data.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Erreur lors de l\'ajout de la spécialité';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async removeSpecialtyFromSession(sessionId, sessionSpecialtyId) {
            this.loading = true;
            this.error = null;
            try {
                await sessionsApi.removeSpecialtyFromSession(sessionId, sessionSpecialtyId);
                if (this.currentSession && this.currentSession.id === sessionId) {
                    await this.fetchSession(sessionId);
                }
                await this.fetchSessions();
            } catch (error) {
                this.error = error.response?.data?.message || 'Erreur lors de la suppression de la spécialité';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchSpecialties() {
            try {
                const response = await sessionsApi.getSpecialties();
                this.specialties = response.data.data;
            } catch (error) {
                console.error('Error fetching specialties:', error);
            }
        },

        async fetchStudyTypes() {
            try {
                const response = await sessionsApi.getStudyTypes();
                this.studyTypes = response.data.data;
            } catch (error) {
                console.error('Error fetching study types:', error);
            }
        },

        clearCurrentSession() {
            this.currentSession = null;
        }
    }
});
