import apiClient from '../axios'

export default {
  async getDashboard() {
    try {
      const response = await apiClient.get('/api/student/dashboard')
      return response.data
    } catch (error) {
      console.error('Student API Error (getDashboard):', error)
      throw error
    }
  },

  async getProfile() {
    try {
      const response = await apiClient.get('/api/student/profile')
      return response.data
    } catch (error) {
      console.error('Student API Error (getProfile):', error)
      throw error
    }
  },

  async updateProfile(payload) {
    try {
      const response = await apiClient.put('/api/student/profile', payload)
      return response.data
    } catch (error) {
      console.error('Student API Error (updateProfile):', error)
      throw error
    }
  },

  async updatePassword(payload) {
    try {
      const response = await apiClient.put('/api/student/profile/password', payload)
      return response.data
    } catch (error) {
      console.error('Student API Error (updatePassword):', error)
      throw error
    }
  },

  async getModules() {
    try {
      const response = await apiClient.get('/api/student/modules')
      return response.data
    } catch (error) {
      console.error('Student API Error (getModules):', error)
      throw error
    }
  },

  async getAttendance(params = {}) {
    try {
      const response = await apiClient.get('/api/student/attendance', { params })
      return response.data
    } catch (error) {
      console.error('Student API Error (getAttendance):', error)
      throw error
    }
  },

  async getSchedule(week = 'current') {
    try {
      const response = await apiClient.get(`/api/student/schedule?week=${week}`)
      return response.data
    } catch (error) {
      console.error('Student API Error (getSchedule):', error)
      throw error
    }
  },

  async getExamResults() {
    try {
      const response = await apiClient.get('/api/student/exams/results')
      return response.data
    } catch (error) {
      console.error('Student API Error (getExamResults):', error)
      throw error
    }
  },

  async getUpcomingExams() {
    try {
      const response = await apiClient.get('/api/student/exams/upcoming')
      return response.data
    } catch (error) {
      console.error('Student API Error (getUpcomingExams):', error)
      throw error
    }
  },

  async completeProfile(payload) {
    try {
      const response = await apiClient.post('/api/student/complete-profile', payload)
      return response.data
    } catch (error) {
      console.error('Student API Error (completeProfile):', error)
      throw error
    }
  }
}
