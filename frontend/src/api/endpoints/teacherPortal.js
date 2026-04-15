import apiClient from '../axios'

export default {
  // Dashboard
  async getDashboardStats() {
    try {
      const response = await apiClient.get('/api/teacher/dashboard')
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (getDashboardStats):', error)
      throw error
    }
  },

  // Profile
  async getProfile() {
    try {
      const response = await apiClient.get('/api/teacher/profile')
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (getProfile):', error)
      throw error
    }
  },
  
  async updateProfile(data) {
    try {
      const response = await apiClient.put('/api/teacher/profile', data)
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (updateProfile):', error)
      throw error
    }
  },

  // Modules & Students
  async getModules(params = {}) {
    try {
      const response = await apiClient.get('/api/teacher/modules', { params })
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (getModules):', error)
      throw error
    }
  },
  
  async getModuleStudents(moduleId) {
    try {
      const response = await apiClient.get(`/api/teacher/modules/${moduleId}/students`)
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (getModuleStudents):', error)
      throw error
    }
  },

  // Attendance
  async getAttendanceSessions(params = {}) {
    try {
      const response = await apiClient.get('/api/teacher/attendance/sessions', { params })
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (getAttendanceSessions):', error)
      throw error
    }
  },
  
  async getSessionStudents(scheduleId, date) {
    try {
      const response = await apiClient.get(`/api/teacher/attendance/sessions/${scheduleId}/students`, {
        params: { date }
      })
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (getSessionStudents):', error)
      throw error
    }
  },
  
  async markAttendance(scheduleId, data) {
    try {
      const response = await apiClient.post(`/api/teacher/attendance/sessions/${scheduleId}`, data)
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (markAttendance):', error)
      throw error
    }
  },
  
  async getAttendanceHistory() {
    try {
      const response = await apiClient.get('/api/teacher/attendance/history')
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (getAttendanceHistory):', error)
      throw error
    }
  },

  // Exams & Grades
  async storeExam(data) {
    try {
      const response = await apiClient.post('/api/teacher/exams', data)
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (storeExam):', error)
      throw error
    }
  },
  
  async updateExam(id, data) {
    try {
      const response = await apiClient.put(`/api/teacher/exams/${id}`, data)
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (updateExam):', error)
      throw error
    }
  },
  
  async updateExamStatus(id, status) {
    try {
      const response = await apiClient.patch(`/api/teacher/exams/${id}/status`, { status })
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (updateExamStatus):', error)
      throw error
    }
  },

  async getExams(params = {}) {
    try {
      const response = await apiClient.get('/api/teacher/exams', { params })
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (getExams):', error)
      throw error
    }
  },
  
  async getExamsHistory() {
    try {
      const response = await apiClient.get('/api/teacher/exams/history')
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (getExamsHistory):', error)
      throw error
    }
  },
  
  async getExamStudents(examId) {
    try {
      const response = await apiClient.get(`/api/teacher/exams/${examId}/students`)
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (getExamStudents):', error)
      throw error
    }
  },
  
  async storeExamResults(examId, data) {
    try {
      const response = await apiClient.post(`/api/teacher/exams/${examId}/results`, data)
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (storeExamResults):', error)
      throw error
    }
  },

  // Schedule
  async getSchedule(week = 'current') {
    try {
      const response = await apiClient.get('/api/teacher/schedule', { params: { week } })
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (getSchedule):', error)
      throw error
    }
  },

  // Messages
  async getMessages(params = {}) {
    try {
      const response = await apiClient.get('/api/teacher/messages', { params })
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (getMessages):', error)
      throw error
    }
  },
  
  async getMessage(id) {
    try {
      const response = await apiClient.get(`/api/teacher/messages/${id}`)
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (getMessage):', error)
      throw error
    }
  },
  
  async getUnreadMessagesCount() {
    try {
      const response = await apiClient.get('/api/teacher/messages/unread/count')
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (getUnreadMessagesCount):', error)
      return { count: 0 }
    }
  },

  // Documents
  async getDocuments(params = {}) {
    try {
      const response = await apiClient.get('/api/teacher/documents', { params })
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (getDocuments):', error)
      throw error
    }
  },

  // Courses (Lessons)
  async getCourses() {
    try {
      const response = await apiClient.get('/api/teacher/courses')
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (getCourses):', error)
      throw error
    }
  },

  async uploadCourse(formData) {
    try {
      const response = await apiClient.post('/api/teacher/courses', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (uploadCourse):', error)
      throw error
    }
  },

  async deleteCourse(id) {
    try {
      const response = await apiClient.delete(`/api/teacher/courses/${id}`)
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (deleteCourse):', error)
      throw error
    }
  },
  
  async getNewDocumentsCount() {
    try {
      const response = await apiClient.get('/api/teacher/documents/new/count')
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (getNewDocumentsCount):', error)
      return { count: 0 }
    }
  },
  
  async downloadDocument(id) {
    try {
      const response = await apiClient.get(`/api/teacher/documents/${id}/download`, {
        responseType: 'blob'
      })
      return response.data
    } catch (error) {
      console.error('Teacher Portal API Error (downloadDocument):', error)
      throw error
    }
  }
}
