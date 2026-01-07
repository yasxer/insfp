import api from '../axios';

export default {
    // Get current sessions
    getSessions(status = null) {
        const params = status ? { status } : {};
        return api.get('/api/admin/sessions', { params });
    },

    // Get archived sessions
    getArchivedSessions() {
        return api.get('/api/admin/sessions/archived');
    },

    // Get single session
    getSession(id) {
        return api.get(`/api/admin/sessions/${id}`);
    },

    // Create new session
    createSession(data) {
        return api.post('/api/admin/sessions', data);
    },

    // Update session
    updateSession(id, data) {
        return api.put(`/api/admin/sessions/${id}`, data);
    },

    // Delete session
    deleteSession(id) {
        return api.delete(`/api/admin/sessions/${id}`);
    },

    // Add specialty to session
    addSpecialtyToSession(sessionId, data) {
        return api.post(`/api/admin/sessions/${sessionId}/specialties`, data);
    },

    // Remove specialty from session
    removeSpecialtyFromSession(sessionId, sessionSpecialtyId) {
        return api.delete(`/api/admin/sessions/${sessionId}/specialties/${sessionSpecialtyId}`);
    },

    // Get specialties for dropdown
    getSpecialties() {
        return api.get('/api/admin/sessions/specialties');
    },

    // Get study types
    getStudyTypes() {
        return api.get('/api/admin/sessions/study-types');
    },

    // Get sessions for dropdown (student assignment)
    getSessionsForDropdown() {
        return api.get('/api/admin/sessions/dropdown');
    }
};
