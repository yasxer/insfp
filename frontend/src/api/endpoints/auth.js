import apiClient from '../axios'

export default {
  async login(credentials) {
    try {
      const response = await apiClient.post('/api/login', credentials)
      return response.data
    } catch (error) {
      console.error('Auth API Error (login):', error)
      throw error
    }
  },

  async logout() {
    try {
      const response = await apiClient.post('/api/logout')
      return response.data
    } catch (error) {
      console.error('Auth API Error (logout):', error)
      throw error
    }
  }
}
