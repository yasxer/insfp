import axios from '../axios'

export default {
  // ── CRUD ──────────────────────────────────────────────────────────────────
  getSchedules(params = {}) {
    return axios.get('/api/admin/schedules', { params })
  },
  getSchedule(id) {
    return axios.get(`/api/admin/schedules/${id}`)
  },
  createSchedule(data) {
    return axios.post('/api/admin/schedules', data)
  },
  updateSchedule(id, data) {
    return axios.put(`/api/admin/schedules/${id}`, data)
  },
  deleteSchedule(id) {
    return axios.delete(`/api/admin/schedules/${id}`)
  },

  // ── Session-based schedule management ────────────────────────────────────
  getSessions() {
    return axios.get('/api/admin/schedules/sessions')
  },
  getSessionSpecialties(sessionId) {
    return axios.get(`/api/admin/schedules/sessions/${sessionId}/specialties`)
  },
  publishSpecialty(sessionId, data) {
    return axios.post(`/api/admin/schedules/sessions/${sessionId}/publish`, data)
  },
  unpublishSpecialty(sessionId, data) {
    return axios.post(`/api/admin/schedules/sessions/${sessionId}/unpublish`, data)
  },

  // ── Legacy ────────────────────────────────────────────────────────────────
  getGroups(specialty_id) {
    return axios.get('/api/admin/schedules/groups', { params: { specialty_id } })
  },
  getSpecialtySemesters(academic_year = null) {
    return axios.get('/api/admin/schedules/specialty-semesters', { params: { academic_year } })
  },
}
