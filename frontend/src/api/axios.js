import axios from 'axios'
import { useLoadingStore } from '@/stores/loading'

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000',
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})

apiClient.interceptors.request.use(config => {
  useLoadingStore().start()

  // Auth is carried by the httpOnly session cookie (sent automatically thanks to
  // withCredentials) — there is no JS-readable token. State-changing requests
  // still need the CSRF header below.
  const method = config.method.toUpperCase()
  if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)) {
    const xsrfToken = document.cookie
      .split('; ')
      .find(row => row.startsWith('XSRF-TOKEN='))

    if (xsrfToken) {
      config.headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrfToken.split('=')[1])
    }
  }

  return config
})

apiClient.interceptors.response.use(
  response => {
    useLoadingStore().stop()
    return response
  },
  error => {
    useLoadingStore().stop()

    const status = error.response?.status
    const message = error.response?.data?.message || error.message

    console.error(`❌ API Error [${status}]:`, message)

    // Handle 401 Unauthorized - session expired or invalid
    if (status === 401) {
      console.warn('🔑 Session expired or invalid, clearing auth...')
      localStorage.removeItem('user')
      sessionStorage.removeItem('user')

      // Trigger logout via store if available
      try {
        import('@/stores/auth').then(({ useAuthStore }) => {
          const authStore = useAuthStore()
          authStore.logout()
        })
      } catch (e) {
        console.log('Auth store not available for logout')
      }

      // Redirect to login will be handled by router guard
      window.location.href = '/login'
    }

    // Handle 403 Forbidden - User doesn't have permission
    if (status === 403) {
      console.warn('🚫 Access Forbidden:', message)
      error.userMessage = 'Vous n\'avez pas la permission d\'accéder à cette ressource.'
    }

    // Handle 422 Unprocessable Entity - Validation errors
    if (status === 422) {
      console.warn('⚠️ Validation Error:', error.response?.data?.errors)
      error.userMessage = 'Données invalides. Veuillez vérifier vos entrées.'
    }

    // Handle 500+ Server Errors
    if (status && status >= 500) {
      console.error('💥 Server Error [' + status + ']:', message)
      error.userMessage = 'Erreur serveur. Veuillez réessayer plus tard.'
    }

    // Handle network errors (no response from server)
    if (!error.response) {
      console.error('🌐 Network Error:', error.message)
      error.userMessage = 'Erreur réseau. Vérifiez votre connexion Internet.'
    }

    return Promise.reject(error)
  }
)

export default apiClient
