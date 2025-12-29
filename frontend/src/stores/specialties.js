import { defineStore } from 'pinia'
import axios from '@/api/axios'

export const useSpecialtiesStore = defineStore('specialties', {
  state: () => ({
    specialties: [],
    currentSpecialty: null,
    loading: false,
    error: null
  }),

  actions: {
    async fetchSpecialties() {
      this.loading = true
      try {
        const response = await axios.get('/api/admin/specialties')
        this.specialties = response.data.specialties
      } catch (error) {
        this.error = error.response?.data?.message || 'Error fetching specialties'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchSpecialty(id) {
      this.loading = true
      try {
        const response = await axios.get(`/api/admin/specialties/${id}`)
        this.currentSpecialty = response.data.specialty
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Error fetching specialty details'
        throw error
      } finally {
        this.loading = false
      }
    },

    async createSpecialty(formData) {
      this.loading = true
      try {
        const response = await axios.post('/api/admin/specialties', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        await this.fetchSpecialties()
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Error creating specialty'
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateSpecialty(id, formData) {
      this.loading = true
      try {
        // Note: For file uploads with PUT/PATCH, Laravel sometimes has issues. 
        // It's safer to use POST with _method: 'PUT' if sending FormData.
        formData.append('_method', 'PUT')
        const response = await axios.post(`/api/admin/specialties/${id}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        await this.fetchSpecialties()
        if (this.currentSpecialty && this.currentSpecialty.id === id) {
          await this.fetchSpecialty(id)
        }
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Error updating specialty'
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteSpecialty(id) {
      this.loading = true
      try {
        await axios.delete(`/api/admin/specialties/${id}`)
        await this.fetchSpecialties()
      } catch (error) {
        this.error = error.response?.data?.message || 'Error deleting specialty'
        throw error
      } finally {
        this.loading = false
      }
    }
  }
})
