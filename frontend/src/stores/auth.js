import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import apiClient from '@/api/axios'
import authApi from '@/api/endpoints/auth'
import studentApi from '@/api/endpoints/student'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user') || sessionStorage.getItem('user')) || null)
  const token = ref(localStorage.getItem('token') || sessionStorage.getItem('token') || null)
  const loading = ref(false)
  const error = ref(null)

  const isAuthenticated = computed(() => !!token.value)
  const userRole = computed(() => user.value?.role || null)
  const isStudent = computed(() => user.value?.role === 'student')
  const isTeacher = computed(() => user.value?.role === 'teacher')
  const isAdmin = computed(() => user.value?.role === 'administration')
  const userName = computed(() => user.value?.name || '')
  
  // Check if student profile is actually complete (has date_of_birth and address)
  const isProfileComplete = computed(() => {
    if (!isStudent.value) return true
    if (!user.value) return false
    
    // Check if student has completed required profile fields
    const hasDateOfBirth = user.value.date_of_birth && user.value.date_of_birth !== null
    const hasAddress = user.value.address && user.value.address !== null && user.value.address.length >= 10
    
    return hasDateOfBirth && hasAddress
  })

  async function login(credentials, remember = false) {
    loading.value = true
    error.value = null
    try {
      console.log('📡 Calling login API...')
      const data = await authApi.login(credentials)
      console.log('📦 Login API response:', data)
      
      // Expect data to contain { token, user }
      token.value = data.token
      user.value = data.user
      
      console.log('✅ Token set:', token.value ? 'Yes' : 'No')
      console.log('✅ User set:', user.value)
      console.log('✅ User role:', user.value?.role)
      
      if (remember) {
        localStorage.setItem('token', data.token)
        localStorage.setItem('user', JSON.stringify(data.user))
      } else {
        sessionStorage.setItem('token', data.token)
        sessionStorage.setItem('user', JSON.stringify(data.user))
      }
      
      // For students, fetch full profile to check if complete
      if (data.user?.role === 'student') {
        console.log('👨‍🎓 User is student, fetching full profile...')
        try {
          const profileData = await studentApi.getProfile()
          console.log('📦 Profile data:', profileData)
          
          // Merge profile data with existing user data (preserve role!)
          user.value = {
            ...user.value,
            ...profileData,
            role: data.user.role  // Keep the role from login response!
          }
          
          console.log('✅ User after merge:', user.value)
          
          // Update storage with full profile data
          if (remember) {
            localStorage.setItem('user', JSON.stringify(user.value))
          } else {
            sessionStorage.setItem('user', JSON.stringify(user.value))
          }
        } catch (err) {
          console.error('Failed to fetch profile:', err)
        }
      }
      
      console.log('✅ Final user value:', user.value)
      console.log('✅ Final user role:', user.value?.role)
      console.log('✅ Profile complete:', isProfileComplete.value)
      
      return { success: true, profileComplete: isProfileComplete.value }
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

  async function logout() {
    try {
      await authApi.logout()
    } catch (err) {
      console.error('Logout error:', err)
    } finally {
      token.value = null
      user.value = null
      
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
    profileComplete: isProfileComplete,
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
    fetchUser
  }
})
