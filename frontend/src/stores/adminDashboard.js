import { defineStore } from 'pinia'
import axios from '@/api/axios'

export const useAdminDashboardStore = defineStore('adminDashboard', {
  state: () => ({
    statistics: null,
    featuredStudents: [],
    studentsBySpecialty: [],
    teachersBySpecialty: [],
    loading: false,
    error: null
  }),
  
  actions: {
    async fetchDashboardData() {
      this.loading = true
      this.error = null
      
      try {
        const [stats, students, studentsChart, teachersChart] = await Promise.all([
          axios.get('/api/admin/statistics'),
          axios.get('/api/admin/students/random?limit=9'),
          axios.get('/api/admin/charts/students-by-specialty'),
          axios.get('/api/admin/charts/teachers-by-specialty')
        ])
        
        this.statistics = stats.data
        this.featuredStudents = students.data
        this.studentsBySpecialty = studentsChart.data
        this.teachersBySpecialty = teachersChart.data
        
      } catch (error) {
        this.error = error.response?.data?.message || error.message || 'Une erreur est survenue lors du chargement des données.'
        console.error('Dashboard fetch error:', error)
      } finally {
        this.loading = false
      }
    }
  }
})

