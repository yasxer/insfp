import axios from '../axios'

export default {
  // Get all schedules
  getSchedules(params = {}) {
    return axios.get('/api/admin/schedules', { params })
  },

  // Get single schedule
  getSchedule(id) {
    return axios.get(`/api/admin/schedules/${id}`)
  },

  // Create schedule
  createSchedule(data) {
    return axios.post('/api/admin/schedules', data)
  },

  // Update schedule
  updateSchedule(id, data) {
    return axios.put(`/api/admin/schedules/${id}`, data)
  },

  // Delete schedule
  deleteSchedule(id) {
    return axios.delete(`/api/admin/schedules/${id}`)
  },

  // Get available groups for a specialty
  getGroups(specialty_id) {
    return axios.get('/api/admin/schedules/groups', { 
      params: { specialty_id } 
    })
  },

  // Get specialty-semester combinations with student counts
  getSpecialtySemesters(academic_year = null) {
    return axios.get('/api/admin/schedules/specialty-semesters', {
      params: { academic_year }
    })
  }
}
