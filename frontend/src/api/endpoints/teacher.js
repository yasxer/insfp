import apiClient from '../axios'

export default {
  // Get all teachers
  async getTeachers(params = {}) {
    try {
      const response = await apiClient.get('/api/admin/teachers', { params })
      return response.data
    } catch (error) {
      console.error('Teacher API Error (getTeachers):', error)
      throw error
    }
  },

  // Get teacher by ID
  async getTeacher(id) {
    try {
      const response = await apiClient.get(`/api/admin/teachers/${id}`)
      return response.data
    } catch (error) {
      console.error('Teacher API Error (getTeacher):', error)
      throw error
    }
  },

  // Get teacher schedule
  async getTeacherSchedule(id) {
    try {
      const response = await apiClient.get(`/api/admin/teachers/${id}/schedule`)
      return response.data
    } catch (error) {
      console.error('Teacher API Error (getTeacherSchedule):', error)
      throw error
    }
  },

  // Create teacher
  async createTeacher(payload) {
    try {
      const response = await apiClient.post('/api/admin/teachers', payload)
      return response.data
    } catch (error) {
      console.error('Teacher API Error (createTeacher):', error)
      throw error
    }
  },

  // Update teacher
  async updateTeacher(id, payload) {
    try {
      const response = await apiClient.put(`/api/admin/teachers/${id}`, payload)
      return response.data
    } catch (error) {
      console.error('Teacher API Error (updateTeacher):', error)
      throw error
    }
  },

  // Delete teacher
  async deleteTeacher(id) {
    try {
      const response = await apiClient.delete(`/api/admin/teachers/${id}`)
      return response.data
    } catch (error) {
      console.error('Teacher API Error (deleteTeacher):', error)
      throw error
    }
  },

  // Reset teacher password
  async resetTeacherPassword(id, payload) {
    try {
      const response = await apiClient.post(`/api/admin/teachers/${id}/reset-password`, payload)
      return response.data
    } catch (error) {
      console.error('Teacher API Error (resetTeacherPassword):', error)
      throw error
    }
  }
}
