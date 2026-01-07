import apiClient from '../axios'

export default {
  // Documents/Files
  async getDocuments(page = 1, targetType = 'all') {
    try {
      let url = `/api/admin/documents?page=${page}`
      if (targetType !== 'all') {
        url += `&target_type=${targetType}`
      }
      const response = await apiClient.get(url)
      return response.data
    } catch (error) {
      console.error('Admin API Error (getDocuments):', error)
      throw error
    }
  },

  async uploadDocument(formData) {
    try {
      const response = await apiClient.post('/api/admin/documents', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      return response.data
    } catch (error) {
      console.error('Admin API Error (uploadDocument):', error)
      throw error
    }
  },

  async deleteDocument(documentId) {
    try {
      const response = await apiClient.delete(`/api/admin/documents/${documentId}`)
      return response.data
    } catch (error) {
      console.error('Admin API Error (deleteDocument):', error)
      throw error
    }
  },

  async downloadDocument(documentId) {
    try {
      const response = await apiClient.get(`/api/admin/documents/${documentId}/download`, {
        responseType: 'blob'
      })
      return response
    } catch (error) {
      console.error('Admin API Error (downloadDocument):', error)
      throw error
    }
  },

  async getSessions() {
    try {
      const response = await apiClient.get('/api/admin/documents/sessions')
      return response.data
    } catch (error) {
      console.error('Admin API Error (getSessions):', error)
      throw error
    }
  },

  async getSessionSpecialties(sessionId) {
    try {
      const response = await apiClient.get(`/api/admin/documents/sessions/${sessionId}/specialties`)
      return response.data
    } catch (error) {
      console.error('Admin API Error (getSessionSpecialties):', error)
      throw error
    }
  }
}
