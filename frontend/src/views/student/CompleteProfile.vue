<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import studentApi from '@/api/endpoints/student'
import { 
  UserCircleIcon, 
  CheckCircleIcon 
} from '@heroicons/vue/24/outline'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  date_of_birth: '',
  address: '',
  phone: ''
})

const loading = ref(false)
const initialLoading = ref(true)
const error = ref(null)
const fieldErrors = ref({})

// Load existing profile data on mount
onMounted(async () => {
  try {
    initialLoading.value = true
    const data = await studentApi.getProfile()
    
    // Pre-fill phone if exists
    form.value.phone = data.phone || ''
    form.value.date_of_birth = data.date_of_birth || ''
    form.value.address = data.address || ''
  } catch (err) {
    console.error('Failed to load profile:', err)
  } finally {
    initialLoading.value = false
  }
})

const handleSubmit = async () => {
  try {
    loading.value = true
    error.value = null
    fieldErrors.value = {}
    
    console.log('📤 Submitting profile data:', form.value)
    
    const response = await studentApi.completeProfile(form.value)
    
    console.log('✅ Profile completion response:', response)
    
    authStore.setProfileComplete(true)
    
    // Show success and redirect
    setTimeout(() => {
      router.push('/student/dashboard')
    }, 1500)
  } catch (err) {
    console.error('❌ Profile completion error:', err)
    console.error('Error response:', err.response?.data)
    
    if (err.response?.status === 422) {
      fieldErrors.value = err.response.data.errors
      error.value = 'Please correct the errors below.'
    } else {
      error.value = err.response?.data?.message || 'Failed to complete profile'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 flex items-center justify-center px-4 py-12">
    <div class="max-w-2xl w-full">
      <!-- Loading State -->
      <div v-if="initialLoading" class="text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900 mb-4">
          <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>
        <p class="text-gray-600 dark:text-gray-400">Loading your profile...</p>
      </div>

      <!-- Form -->
      <div v-else class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900 mb-4">
            <UserCircleIcon class="w-12 h-12 text-blue-600 dark:text-blue-300" />
          </div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            Complete Your Profile
          </h1>
          <p class="text-gray-600 dark:text-gray-400">
            We need a few more details to set up your account
          </p>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Date of Birth -->
          <div>
            <label for="date_of_birth" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
              Date of Birth <span class="text-red-500">*</span>
            </label>
            <input
              id="date_of_birth"
              v-model="form.date_of_birth"
              type="date"
              required
              max="2010-12-31"
              class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
              :class="{'border-red-500': fieldErrors.date_of_birth}"
            />
            <p v-if="fieldErrors.date_of_birth" class="mt-2 text-sm text-red-600">
              {{ fieldErrors.date_of_birth[0] }}
            </p>
          </div>

          <!-- Address -->
          <div>
            <label for="address" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
              Address <span class="text-red-500">*</span>
            </label>
            <textarea
              id="address"
              v-model="form.address"
              required
              rows="4"
              placeholder="Enter your full address (e.g., Street, City, Wilaya)"
              class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
              :class="{'border-red-500': fieldErrors.address}"
            ></textarea>
            <p v-if="fieldErrors.address" class="mt-2 text-sm text-red-600">
              {{ fieldErrors.address[0] }}
            </p>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
              Minimum 10 characters required
            </p>
          </div>

          <!-- Phone (Optional) -->
          <div>
            <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
              Phone Number <span class="text-gray-400 font-normal">(Optional)</span>
            </label>
            <input
              id="phone"
              v-model="form.phone"
              type="tel"
              pattern="0[5-7][0-9]{8}"
              placeholder="0612345678"
              class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
              :class="{'border-red-500': fieldErrors.phone}"
            />
            <p v-if="fieldErrors.phone" class="mt-2 text-sm text-red-600">
              {{ fieldErrors.phone[0] }}
            </p>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800">
            <p class="text-red-600 dark:text-red-400 text-sm font-medium">{{ error }}</p>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full flex items-center justify-center gap-3 py-4 px-6 rounded-xl text-base font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg hover:shadow-xl"
          >
            <CheckCircleIcon v-if="!loading" class="w-6 h-6" />
            <svg v-else class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loading ? 'Completing Profile...' : 'Complete Profile & Continue' }}
          </button>
        </form>

        <!-- Info -->
        <div class="mt-6 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
          <p class="text-sm text-blue-700 dark:text-blue-300">
            <strong>Note:</strong> You need to complete your profile to access the dashboard and other features.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>