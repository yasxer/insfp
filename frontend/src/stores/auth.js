import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import apiClient from '@/api/axios'
import authApi from '@/api/endpoints/auth'
import studentApi from '@/api/endpoints/student'
import teacherApi from '@/api/endpoints/teacherPortal'
import adminApi from '@/api/endpoints/admin'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user') || sessionStorage.getItem('user')) || null)
  const loading = ref(false)
  const error = ref(null)

  // Auth is carried by a server-side httpOnly session cookie the browser JS can
  // never read. "Logged in" is tracked here only by the presence of cached user
  // data (for routing/UX); if the session has actually expired the next API call
  // returns 401 and the interceptor/logout clears this state.
  const isAuthenticated = computed(() => !!user.value)
  const userRole = computed(() => user.value?.role || null)
  const isStudent = computed(() => user.value?.role === 'student')
  const isTeacher = computed(() => user.value?.role === 'teacher')
  const isAdmin = computed(() => user.value?.role === 'administration')

  // The user object exposes full_name (or first_name/last_name) — never `name`.
  const userName = computed(() => {
    const u = user.value
    if (!u) return ''
    // Older accounts keep the name on the teacher/student row only, and the merged
    // profile responses nest it (user.name, teacher.full_name…): try them in order.
    return (u.full_name || '').trim()
      || [u.first_name, u.last_name].filter(Boolean).join(' ')
      || u.user?.name
      || u.teacher?.full_name
      || u.student?.full_name
      || u.administration?.full_name
      || u.name
      || ''
  })

  // Check if student profile is actually complete (has date_of_birth and address)
  const isProfileComplete = computed(() => {
    if (!isStudent.value) return true
    if (!user.value) return false

    const hasDateOfBirth = user.value.date_of_birth && user.value.date_of_birth !== null
    const hasAddress = user.value.address && user.value.address !== null && user.value.address.length >= 10

    return hasDateOfBirth && hasAddress
  })

  // Persist non-sensitive user data for page reloads. "remember" decides between
  // localStorage (survives browser restart) and sessionStorage (tab lifetime).
  function persistUser(remember) {
    const primary = remember ? localStorage : sessionStorage
    const secondary = remember ? sessionStorage : localStorage
    primary.setItem('user', JSON.stringify(user.value))
    secondary.removeItem('user')
    if (remember) localStorage.setItem('remember', '1')
    else localStorage.removeItem('remember')
  }

  function clearStorage() {
    user.value = null
    localStorage.removeItem('user')
    sessionStorage.removeItem('user')
    localStorage.removeItem('remember')
  }

  // Prime the XSRF-TOKEN cookie required before any auth POST.
  async function csrf() {
    await apiClient.get('/sanctum/csrf-cookie')
  }

  async function login(credentials, remember = false) {
    loading.value = true
    error.value = null
    try {
      await csrf()
      const data = await authApi.login({ ...credentials, remember })

      user.value = data.user
      persistUser(remember)

      // Fetch full profile based on role
      try {
        let profileData = null

        if (data.user?.role === 'student') {
          const response = await studentApi.getProfile()
          profileData = response.data || response
        } else if (data.user?.role === 'teacher') {
          const response = await teacherApi.getProfile()
          profileData = response.data || response
        } else if (data.user?.role === 'administration') {
          const response = await adminApi.getProfile()
          profileData = response.data || response
        }

        if (profileData) {
          // Merge profile data with existing user data (preserve role!)
          user.value = {
            ...user.value,
            ...profileData,
            role: data.user.role
          }
          persistUser(remember)
        }
      } catch (err) {
        // Don't fail login if profile fetch fails - user is still authenticated
      }

      return { success: true, profileComplete: isProfileComplete.value }
    } catch (err) {
      error.value =
        err?.response?.data?.message ||
        err?.message ||
        'Connexion impossible. Vérifiez vos identifiants.'
      return { success: false }
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      await authApi.logout()
    } catch (err) {
      // Even if the server call fails, drop local state below.
    } finally {
      clearStorage()
    }
  }

  async function fetchUser() {
    if (!user.value) return
    try {
      let data

      if (user.value?.role === 'student') {
        data = await studentApi.getProfile()
      } else if (user.value?.role === 'teacher') {
        data = await teacherApi.getProfile()
      } else if (user.value?.role === 'administration') {
        data = await adminApi.getProfile()
      } else {
        return
      }

      const profileData = data.data || data

      user.value = {
        ...user.value,
        ...profileData,
        role: user.value.role
      }

      persistUser(localStorage.getItem('remember') === '1')
    } catch (err) {
      if (err?.response?.status === 401) {
        clearStorage()
      }
    }
  }

  return {
    user,
    profileComplete: isProfileComplete,
    loading,
    error,
    isAuthenticated,
    userRole,
    isStudent,
    isTeacher,
    isAdmin,
    userName,
    csrf,
    login,
    logout,
    fetchUser
  }
})
