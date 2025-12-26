<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { AcademicCapIcon } from '@heroicons/vue/24/outline'

const router = useRouter()
const authStore = useAuthStore()

const credentials = ref({
  registration_number: '',
  password: ''
})
const rememberMe = ref(false)
const loading = ref(false)

const handleLogin = async () => {
  if (!credentials.value.registration_number || !credentials.value.password) return

  loading.value = true
  console.log('🔐 Starting login with:', credentials.value.registration_number)
  
  try {
    const result = await authStore.login(credentials.value, rememberMe.value)
    console.log('✅ Login result:', result)
    
    if (result.success) {
      console.log('✅ Login successful!')
      console.log('👤 User role:', authStore.userRole)
      console.log('📋 Profile complete:', result.profileComplete)
      
      if (!result.profileComplete) {
        console.log('➡️ Redirecting to complete profile')
        router.push('/complete-profile')
      } else {
        console.log('➡️ Redirecting to dashboard')
        // Redirect to dashboard based on role
        if (authStore.isStudent) {
          console.log('👨‍🎓 Redirecting student to /student/dashboard')
          router.push('/student/dashboard')
        } else if (authStore.isTeacher) {
          console.log('👨‍🏫 Redirecting teacher to /teacher/dashboard')
          router.push('/teacher/dashboard')
        } else if (authStore.isAdmin) {
          console.log('👨‍💼 Redirecting admin to /admin/dashboard')
          router.push('/admin/dashboard')
        } else {
          console.log('➡️ Redirecting to /dashboard')
          router.push('/dashboard')
        }
      }
    } else {
      console.error('❌ Login failed:', authStore.error)
    }
  } catch (error) {
    console.error('❌ Login exception:', error)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 px-4">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900 mb-4">
          <AcademicCapIcon class="w-8 h-8 text-blue-600 dark:text-blue-300" />
        </div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Student Login</h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Welcome back to INSFP Portal</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <label for="registration_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Email or Registration Number
          </label>
          <input
            id="registration_number"
            v-model="credentials.registration_number"
            type="text"
            required
            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
            placeholder="student@insfp.dz or 2025001"
          />
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
          <input
            id="password"
            v-model="credentials.password"
            type="password"
            required
            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
            placeholder="••••••••"
          />
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              id="remember-me"
              v-model="rememberMe"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label for="remember-me" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
              Remember me
            </label>
          </div>
          <div class="text-sm">
            <a href="#" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">
              Forgot password?
            </a>
          </div>
        </div>

        <div v-if="authStore.error" class="p-3 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-sm">
          {{ authStore.error }}
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ loading ? 'Logging in...' : 'Sign In' }}
        </button>
      </form>

      <!-- Footer -->
      <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
          Don't have an account?
          <router-link to="/register" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 hover:underline">
            Register now
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>