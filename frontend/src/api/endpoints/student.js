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
  },

  // Messages
  async getMessages(page = 1) {
    try {
      const response = await apiClient.get(`/api/student/messages?page=${page}`)
      return response.data
    } catch (error) {
      console.error('Student API Error (getMessages):', error)
      throw error
    }
  },

  async getMessage(id) {
    try {
      const response = await apiClient.get(`/api/student/messages/${id}`)
      return response.data
    } catch (error) {
      console.error('Student API Error (getMessage):', error)
      throw error
    }
  },

  async getUnreadMessagesCount() {
    try {
      const response = await apiClient.get('/api/student/messages/unread/count')
      return response.data
    } catch (error) {
      console.error('Student API Error (getUnreadMessagesCount):', error)
      throw error
    }
  },

  // Lessons
  async getLessonModules() {
    try {
      const response = await apiClient.get('/api/student/lessons/modules')
      return response.data
    } catch (error) {
      console.error('Student API Error (getLessonModules):', error)
      throw error
    }
  },

  async getModuleLessons(moduleId) {
    try {
      const response = await apiClient.get(`/api/student/lessons/modules/${moduleId}`)
      return response.data
    } catch (error) {
      console.error('Student API Error (getModuleLessons):', error)
      throw error
    }
  },

  async downloadLesson(lessonId) {
    try {
      const response = await apiClient.get(`/api/student/lessons/${lessonId}/download`, {
        responseType: 'blob'
      })
      return response
    } catch (error) {
      console.error('Student API Error (downloadLesson):', error)
      throw error
    }
  },

  async getNewLessonsCount() {
    try {
      const response = await apiClient.get('/api/student/lessons/new/count')
      return response.data
    } catch (error) {
      console.error('Student API Error (getNewLessonsCount):', error)
      throw error
    }
  },

  // Documents
  async getDocuments(page = 1) {
    try {
      const response = await apiClient.get(`/api/student/documents?page=${page}`)
      return response.data
    } catch (error) {
      console.error('Student API Error (getDocuments):', error)
      throw error
    }
  },

  async downloadDocument(documentId) {
    try {
      const response = await apiClient.get(`/api/student/documents/${documentId}/download`, {
        responseType: 'blob'
      })
      return response
    } catch (error) {
      console.error('Student API Error (downloadDocument):', error)
      throw error
    }
  },

  async getNewDocumentsCount() {
    try {
      const response = await apiClient.get('/api/student/documents/new/count')
      return response.data
    } catch (error) {
      console.error('Student API Error (getNewDocumentsCount):', error)
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
  }
}
