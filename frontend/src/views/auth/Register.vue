<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/axios'
import { registerSchema } from '@/validations/schemas'
import { AcademicCapIcon } from '@heroicons/vue/24/outline'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  session_id: '',
  registration_number: '',
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  specialty_id: '',
  study_mode: '',
  password: '',
  password_confirmation: ''
})

const passwordStrength = computed(() => {
  const length = form.value.password.length
  if (length === 0) return 'empty'
  if (length < 8) return 'weak'
  return 'strong'
})

const passwordClass = computed(() => {
  if (form.value.password.length === 0) return ''
  return form.value.password.length >= 8
    ? 'border-green-500 focus:ring-green-500'
    : 'border-red-500 focus:ring-red-500'
})

const sessions = ref([])
const loading = ref(false)
const loadingSessions = ref(false)
const error = ref(null)
const successMessage = ref(null)
const fieldErrors = ref({})

// Registration number lookup state
const lookupLoading = ref(false)
const lookupError = ref(null)
const lookupData = ref(null) // { session, specialty, study_modes }
let lookupTimer = null

// Type mapping: Backend Type -> Frontend Value
const typeMapping = {
  presential: 'initial',
  apprentissage: 'alternance',
  cours_soir: 'continue'
}

// Reverse mapping for finding specialties
const reverseTypeMapping = {
  initial: 'presential',
  alternance: 'apprentissage',
  continue: 'cours_soir'
}

const fetchSessions = async () => {
  loadingSessions.value = true
  try {
    const response = await apiClient.get('/api/sessions')
    sessions.value = response.data.data || []
  } catch (err) {
    console.error('Failed to fetch sessions:', err)
    error.value = 'Failed to load sessions. Please refresh the page.'
  } finally {
    loadingSessions.value = false
  }
}

const selectedSession = computed(() => {
  if (!form.value.session_id) return null
  return sessions.value.find(s => s.id === form.value.session_id)
})

// Display labels for the auto-filled (read-only) fields
const sessionLabel = computed(() =>
  selectedSession.value?.name || lookupData.value?.session?.name || ''
)

const studyModeLabel = computed(() => {
  if (!form.value.study_mode) return ''
  const match = (lookupData.value?.study_modes || []).find(m => m.value === form.value.study_mode)
  return match?.label || ''
})

const availableSpecialties = computed(() => {
  if (!selectedSession.value || !form.value.study_mode) return []

  const backendType = reverseTypeMapping[form.value.study_mode]
  const group = selectedSession.value.specialties_by_type.find(g => g.type === backendType)

  return group ? group.specialties : []
})

// Reset everything that depends on the registration number
const clearLookup = () => {
  lookupData.value = null
  lookupError.value = null
  form.value.session_id = ''
  form.value.study_mode = ''
  form.value.specialty_id = ''
}

// Look up the registration number and auto-fill session + study mode
const lookupRegistration = async (number) => {
  lookupLoading.value = true
  lookupError.value = null
  try {
    const response = await apiClient.post('/api/lookup-registration', {
      registration_number: number
    })
    const data = response.data.data
    lookupData.value = data

    // Auto-fill session + study mode (read-only for the student)
    form.value.session_id = data.session?.id || ''
    form.value.study_mode = data.study_modes?.[0]?.value || ''
    form.value.specialty_id = '' // student chooses the specialty
  } catch (err) {
    lookupData.value = null
    form.value.session_id = ''
    form.value.study_mode = ''
    form.value.specialty_id = ''
    lookupError.value = err.response?.data?.message || 'Numéro d\'inscription invalide.'
  } finally {
    lookupLoading.value = false
  }
}

// Debounced lookup whenever the registration number changes
watch(() => form.value.registration_number, (number) => {
  clearTimeout(lookupTimer)
  const trimmed = (number || '').trim()
  if (!trimmed) {
    clearLookup()
    return
  }
  lookupTimer = setTimeout(() => lookupRegistration(trimmed), 500)
})

// Clear specialty if study mode changes (e.g. after a new lookup)
watch(() => form.value.study_mode, () => {
  form.value.specialty_id = ''
})

const handleRegister = async () => {
  error.value = null
  fieldErrors.value = {}
  successMessage.value = null

  try {
    await registerSchema.validate(form.value, { abortEarly: false })
  } catch (validationError) {
    const errors = {}
    for (const issue of validationError.inner) {
      if (!errors[issue.path]) errors[issue.path] = [issue.message]
    }
    fieldErrors.value = errors
    error.value = 'Please correct the errors below.'
    return
  }

  loading.value = true

  try {
    await apiClient.post('/api/register', form.value)

    successMessage.value = 'Registration successful! Please wait for admin approval before logging in.'

    // Clear form on success
    const currentSession = form.value.session_id
    form.value = {
      session_id: currentSession, // Keep session selected
      registration_number: '',
      first_name: '',
      last_name: '',
      email: '',
      phone: '',
      specialty_id: '',
      study_mode: '',
      password: '',
      password_confirmation: ''
    }

    setTimeout(() => {
      router.push('/login')
    }, 3000)
  } catch (err) {
    if (err.response?.status === 422) {
      fieldErrors.value = err.response.data.errors
      error.value = 'Please correct the errors below.'
    } else {
      error.value = err.response?.data?.message || 'Registration failed. Please try again.'
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchSessions()
})
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 px-4 py-8">
    <div class="max-w-2xl w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
      <!-- Header -->
      <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 mb-3">
          <AcademicCapIcon class="w-6 h-6 text-blue-600 dark:text-blue-300" />
        </div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create Account</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Join the INSFP Student Portal</p>
      </div>

      <!-- Success Message -->
      <div v-if="successMessage" class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
        <p class="text-green-600 dark:text-green-400 text-sm font-medium text-center">{{ successMessage }}</p>
        <p class="text-green-500 dark:text-green-500 text-xs text-center mt-1">Redirecting to login...</p>
      </div>

      <!-- Form -->
      <form v-else @submit.prevent="handleRegister" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Registration Number (drives session + study mode) -->
          <div class="md:col-span-2">
            <label for="registration_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Registration Number</label>
            <div class="relative">
              <input
                id="registration_number"
                v-model="form.registration_number"
                type="text"
                required
                class="w-full px-3 py-2 pr-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors font-mono"
                :class="{'border-red-500': fieldErrors.registration_number || lookupError, 'border-green-500': lookupData}"
                placeholder="0001125P1647"
                autocomplete="off"
              />
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3">
                <svg v-if="lookupLoading" class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <svg v-else-if="lookupData" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
            </div>
            <p v-if="lookupError" class="mt-1 text-xs text-red-600">{{ lookupError }}</p>
            <p v-else-if="!form.registration_number" class="mt-1 text-xs text-gray-500 dark:text-gray-400">Entrez votre numéro d'inscription pour remplir automatiquement la session et le mode d'étude.</p>
            <p v-if="fieldErrors.registration_number" class="mt-1 text-xs text-red-600">{{ fieldErrors.registration_number[0] }}</p>
          </div>

          <!-- Session (auto-filled, read-only) -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Session <span class="text-gray-400 font-normal">(automatique)</span></label>
            <input
              type="text"
              readonly
              :value="sessionLabel"
              placeholder="—"
              class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 cursor-not-allowed"
            />
          </div>

          <!-- First Name -->
          <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name</label>
            <input
              id="first_name"
              v-model="form.first_name"
              type="text"
              required
              class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              :class="{'border-red-500': fieldErrors.first_name}"
              placeholder="John"
            />
            <p v-if="fieldErrors.first_name" class="mt-1 text-xs text-red-600">{{ fieldErrors.first_name[0] }}</p>
          </div>

          <!-- Last Name -->
          <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name</label>
            <input
              id="last_name"
              v-model="form.last_name"
              type="text"
              required
              class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              :class="{'border-red-500': fieldErrors.last_name}"
              placeholder="Doe"
            />
            <p v-if="fieldErrors.last_name" class="mt-1 text-xs text-red-600">{{ fieldErrors.last_name[0] }}</p>
          </div>

          <!-- Study Mode (auto-filled, read-only) -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mode d'étude <span class="text-gray-400 font-normal">(automatique)</span></label>
            <input
              type="text"
              readonly
              :value="studyModeLabel"
              placeholder="—"
              class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 cursor-not-allowed"
              :class="{'border-red-500': fieldErrors.study_mode}"
            />
            <p v-if="fieldErrors.study_mode" class="mt-1 text-xs text-red-600">{{ fieldErrors.study_mode[0] }}</p>
          </div>

          <!-- Specialty -->
          <div class="md:col-span-2">
            <label for="specialty" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Specialty</label>
            <div class="relative">
              <select
                id="specialty"
                v-model="form.specialty_id"
                required
                :disabled="!form.study_mode"
                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors appearance-none disabled:opacity-50 disabled:cursor-not-allowed"
                :class="{'border-red-500': fieldErrors.specialty_id}"
              >
                <option value="" disabled>Select Specialty</option>
                <option v-for="specialty in availableSpecialties" :key="specialty.specialty_id" :value="specialty.specialty_id">
                  {{ specialty.specialty_name }} ({{ specialty.specialty_code }})
                </option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                 <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                  <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                </svg>
              </div>
            </div>
            <p v-if="fieldErrors.specialty_id" class="mt-1 text-xs text-red-600">{{ fieldErrors.specialty_id[0] }}</p>
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              :class="{'border-red-500': fieldErrors.email}"
              placeholder="student@insfp.dz"
            />
            <p v-if="fieldErrors.email" class="mt-1 text-xs text-red-600">{{ fieldErrors.email[0] }}</p>
          </div>

          <!-- Phone -->
          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone <span class="text-gray-400 font-normal">(Optional)</span></label>
            <input
              id="phone"
              v-model="form.phone"
              type="tel"
              pattern="0[5-7][0-9]{8}"
              class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              :class="{'border-red-500': fieldErrors.phone}"
              placeholder="0612345678"
            />
            <p v-if="fieldErrors.phone" class="mt-1 text-xs text-red-600">{{ fieldErrors.phone[0] }}</p>
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              minlength="8"
              :class="[
                'w-full px-3 py-2 rounded-lg border bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent transition-colors',
                passwordClass,
                {'border-red-500': fieldErrors.password}
              ]"
              placeholder="••••••••"
            />
            <p v-if="form.password.length > 0"
               :class="form.password.length >= 8 ? 'text-green-600' : 'text-red-600'"
               class="mt-1 text-xs">
              {{ form.password.length }}/8 characters minimum
            </p>
            <p v-if="fieldErrors.password" class="mt-1 text-xs text-red-600">{{ fieldErrors.password[0] }}</p>
          </div>

          <!-- Confirm Password -->
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              required
              class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              placeholder="••••••••"
            />
          </div>
        </div>

        <div v-if="error" class="p-3 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-sm">
          {{ error }}
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ loading ? 'Creating Account...' : 'Create Account' }}
        </button>
      </form>

      <!-- Footer -->
      <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
          Already have an account?
          <router-link to="/login" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 hover:underline">
            Sign in
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>
