import axios from '../axios'

export default {
  // Get all modules
  getModules(params = {}) {
    return axios.get('/api/admin/modules', { params })
  },

  // Get single module
  getModule(id) {
    return axios.get(`/api/admin/modules/${id}`)
  },

  // Create module
  createModule(data) {
    return axios.post('/api/admin/modules', data)
  },

  // Update module
  updateModule(id, data) {
    return axios.put(`/api/admin/modules/${id}`, data)
  },

  // Delete module
  deleteModule(id) {
    return axios.delete(`/api/admin/modules/${id}`)
  },

  // Assign teacher
  assignTeacher(data) {
    return axios.post('/api/admin/modules/assign-teacher', data)
  },

  // Remove teacher
  removeTeacher(data) {
    return axios.post('/api/admin/modules/remove-teacher', data)
  }
}
