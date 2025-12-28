import { defineStore } from 'pinia'
import axios from '@/api/axios'

export const useTeachersStore = defineStore('teachers', {
  state: () => ({
    teachers: [],
    currentTeacher: null,
    currentTeacherSchedule: null,
    loading: false,
    error: null,
    pagination: {
      total: 0,
      per_page: 1000,
      current_page: 1,
      last_page: 1
    },
    filters: {
      search: '',
      specialization: null,
      approved: null
    }
  }),

  getters: {
    getTeacherById: (state) => (id) => {
      return state.teachers.find(teacher => teacher.id === id)
    }
  },

  actions: {
    async fetchTeachers(page = 1) {
      this.loading = true
      this.error = null
      try {
        const params = {
          page: 1,
          per_page: 1000,
          ...this.filters
        }
        // Remove null/empty filters
        Object.keys(params).forEach(key => {
          if (params[key] === null || params[key] === '') {
            delete params[key]
          }
        })

        const response = await axios.get('/api/admin/teachers', { params })
        this.teachers = response.data.teachers || []
        this.pagination = {
          total: response.data.count || 0,
          per_page: 1000,
          current_page: 1,
          last_page: 1
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch teachers'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchTeacher(id) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.get(`/api/admin/teachers/${id}`)
        this.currentTeacher = response.data.teacher
        return response.data.teacher
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch teacher details'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchTeacherSchedule(id) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.get(`/api/admin/teachers/${id}/schedule`)
        this.currentTeacherSchedule = response.data.schedule
        return response.data.schedule
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch teacher schedule'
        throw error
      } finally {
        this.loading = false
      }
    },

    async createTeacher(teacherData) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.post('/api/admin/teachers', teacherData)
        await this.fetchTeachers(this.pagination.current_page)
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create teacher'
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateTeacher(id, teacherData) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.put(`/api/admin/teachers/${id}`, teacherData)
        await this.fetchTeachers(this.pagination.current_page)
        if (this.currentTeacher && this.currentTeacher.id === id) {
          this.currentTeacher = { ...this.currentTeacher, ...response.data.teacher }
        }
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update teacher'
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteTeacher(id) {
      this.loading = true
      this.error = null
      try {
        await axios.delete(`/api/admin/teachers/${id}`)
        await this.fetchTeachers(this.pagination.current_page)
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete teacher'
        throw error
      } finally {
        this.loading = false
      }
    },

    async resetTeacherPassword(id, passwordData) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.post(`/api/admin/teachers/${id}/reset-password`, passwordData)
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to reset teacher password'
        throw error
      } finally {
        this.loading = false
      }
    },

    setFilters(newFilters) {
      this.filters = { ...this.filters, ...newFilters }
      this.pagination.current_page = 1 // Reset to first page on filter change
    },

    clearFilters() {
      this.filters = {
        search: '',
        specialization: null,
        approved: null
      }
      this.pagination.current_page = 1
    }
  }
})
