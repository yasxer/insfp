import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import apiClient from '@/api/axios'
import authApi from '@/api/endpoints/auth'
import studentApi from '@/api/endpoints/student'
import teacherApi from '@/api/endpoints/teacherPortal'
import adminApi from '@/api/endpoints/admin'

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
  // The user object exposes full_name (or first_name/last_name) — never `name`.
  const userName = computed(() => {
    const u = user.value
    if (!u) return ''
    return u.full_name || [u.first_name, u.last_name].filter(Boolean).join(' ') || ''
  })
  
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
      
      // Fetch full profile based on role
      try {
        let profileData = null

        if (data.user?.role === 'student') {
          console.log('👨‍🎓 User is student, fetching full profile...')
          const response = await studentApi.getProfile()
          profileData = response.data || response
        } else if (data.user?.role === 'teacher') {
          console.log('👨‍🏫 User is teacher, fetching full profile...')
          const response = await teacherApi.getProfile()
          profileData = response.data || response
        } else if (data.user?.role === 'administration') {
          console.log('👨‍💼 User is admin, fetching full profile...')
          const response = await adminApi.getProfile()
          profileData = response.data || response
        }

        if (profileData) {
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
        }
      } catch (err) {
        console.error('Failed to fetch profile:', err)
        // Don't fail login if profile fetch fails - user is still authenticated
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
    if (!token.value || !user.value) return
    try {
      let data

      console.log('📡 Fetching user profile for role:', user.value?.role)

      // Call the appropriate API based on user role
      if (user.value?.role === 'student') {
        console.log('👨‍🎓 Fetching student profile...')
        data = await studentApi.getProfile()
      } else if (user.value?.role === 'teacher') {
        console.log('👨‍🏫 Fetching teacher profile...')
        data = await teacherApi.getProfile()
      } else if (user.value?.role === 'administration') {
        console.log('👨‍💼 Fetching admin profile...')
        data = await adminApi.getProfile()
      } else {
        console.warn('⚠️ Unknown user role:', user.value?.role)
        return
      }

      // data can be { data: {...} } or plain object; support both
      const profileData = data.data || data

      // Merge with existing user data while preserving role and token
      user.value = {
        ...user.value,
        ...profileData,
        role: user.value.role // Preserve the role!
      }

      console.log('✅ User profile updated:', user.value)

      // Update storage with new profile data
      const remember = localStorage.getItem('token')
      if (remember) {
        localStorage.setItem('user', JSON.stringify(user.value))
      } else {
        sessionStorage.setItem('user', JSON.stringify(user.value))
      }
    } catch (err) {
      console.error('Fetch user error:', err)
      if (err?.response?.status === 401) {
        console.log('🔑 Token expired or invalid, logging out...')
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
