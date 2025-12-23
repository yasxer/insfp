import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import apiClient from '@/api/axios'
import authApi from '@/api/endpoints/auth'
import studentApi from '@/api/endpoints/student'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token') || null)
  const loading = ref(false)
  const error = ref(null)

  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const userName = computed(() => user.value?.name || '')

  async function login(credentials) {
    loading.value = true
    error.value = null
    try {
      const data = await authApi.login(credentials)
      // Expect data to contain { token, user }
      token.value = data.token
      user.value = data.user
      localStorage.setItem('auth_token', token.value)
      return true
    } catch (err) {
      console.error('Login error:', err)
      error.value =
        err?.response?.data?.message ||
        err?.message ||
        'Login failed. Please check your credentials.'
      return false
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      await authApi.logout()
    } catch (err) {
      console.error('Logout error:', err)
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('auth_token')
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
        localStorage.removeItem('auth_token')
      }
    }
  }

  return {
    user,
    token,
    loading,
    error,
    isAuthenticated,
    userName,
    login,
    logout,
    fetchUser,
  }
})
