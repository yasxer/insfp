import { defineStore } from 'pinia'
import axios from '@/api/axios'

export const useStudentsStore = defineStore('students', {
  state: () => ({
    students: [],
    currentStudent: null,
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
      specialty_id: null,
      semester: null,
      group: null,
      study_mode: null,
      approved: null,
      is_graduated: null
    }
  }),

  getters: {
    getStudentById: (state) => (id) => {
      return state.students.find(student => student.id === id)
    }
  },

  actions: {
    async fetchStudents(page = 1) {
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

        const response = await axios.get('/api/admin/students', { params })
        this.students = response.data.data
        this.pagination = {
          total: response.data.total,
          per_page: response.data.per_page,
          current_page: response.data.current_page,
          last_page: response.data.last_page
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch students'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchStudent(id) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.get(`/api/admin/students/${id}`)
        this.currentStudent = response.data.student
        return response.data.student
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch student details'
        throw error
      } finally {
        this.loading = false
      }
    },

    async createStudent(studentData) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.post('/api/admin/students', studentData)
        await this.fetchStudents(this.pagination.current_page)
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create student'
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateStudent(id, studentData) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.put(`/api/admin/students/${id}`, studentData)
        await this.fetchStudents(this.pagination.current_page)
        if (this.currentStudent && this.currentStudent.id === id) {
          this.currentStudent = { ...this.currentStudent, ...response.data.student }
        }
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update student'
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteStudent(id) {
      this.loading = true
      this.error = null
      try {
        await axios.delete(`/api/admin/students/${id}`)
        await this.fetchStudents(this.pagination.current_page)
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete student'
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
        specialty_id: null,
        semester: null,
        group: null,
        study_mode: null,
        approved: null,
        is_graduated: null
      }
      this.pagination.current_page = 1
    }
  }
})
