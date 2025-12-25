import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import apiClient from '@/api/axios'
import authApi from '@/api/endpoints/auth'
import studentApi from '@/api/endpoints/student'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user') || sessionStorage.getItem('user')) || null)
  const token = ref(localStorage.getItem('token') || sessionStorage.getItem('token') || null)
  const profileComplete = ref(true)
  const loading = ref(false)
  const error = ref(null)

  const isAuthenticated = computed(() => !!token.value)
  const userRole = computed(() => user.value?.role || null)
  const isStudent = computed(() => user.value?.role === 'student')
  const isTeacher = computed(() => user.value?.role === 'teacher')
  const isAdmin = computed(() => user.value?.role === 'administration')
  const userName = computed(() => user.value?.name || '')

  async function login(credentials, remember = false) {
    loading.value = true
    error.value = null
    try {
      const data = await authApi.login(credentials)
      // Expect data to contain { token, user }
      token.value = data.token
      user.value = data.user
      profileComplete.value = data.profile_complete ?? true
      
      if (remember) {
        localStorage.setItem('token', data.token)
        localStorage.setItem('user', JSON.stringify(data.user))
      } else {
        sessionStorage.setItem('token', data.token)
        sessionStorage.setItem('user', JSON.stringify(data.user))
      }
      
      return { success: true, profileComplete: profileComplete.value }
    } catch (err) {
      console.error('Login error:', err)
      error.value =
        err?.response?.data?.message ||
        err?.message ||
        'Login failed. Please check your credentials.'
      return { success: false }
    } finally {
      loading.value = false
    }
  }

  function setProfileComplete(status) {
    profileComplete.value = status
  }

  async function logout() {
    try {
      await authApi.logout()
    } catch (err) {
      console.error('Logout error:', err)
    } finally {
      token.value = null
      user.value = null
      profileComplete.value = true
      
      // Clear storage
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      sessionStorage.removeItem('token')
      sessionStorage.removeItem('user')
    }
  }

  async function fetchUser() {
    if (!token.value) return
    try {
      const data = await studentApi.getProfile()
      // data can be { data: {...} } or plain object; support both
      user.value = data.data || data
    } catch (err) {
      console.error('Fetch user error:', err)
      if (err?.response?.status === 401) {
        token.value = null
        user.value = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        sessionStorage.removeItem('token')
        sessionStorage.removeItem('user')
      }
    }
  }

  return {
    user,
    token,
    profileComplete,
    loading,
    error,
    isAuthenticated,
    userRole,
    isStudent,
    isTeacher,
    isAdmin,
    userName,
    login,
    logout,
    fetchUser,
    setProfileComplete
  }
})
