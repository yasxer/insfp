<template>
  <div class="max-w-7xl mx-auto">
    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6 text-center">
      <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
      <h3 class="mt-4 text-lg font-medium text-red-800 dark:text-red-200">{{ error }}</h3>
      <button @click="$router.back()" class="mt-4 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
        Go Back
      </button>
    </div>

    <!-- Teacher Details -->
    <div v-else-if="teacher">
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <button @click="$router.back()" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </button>
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ teacher.full_name }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ teacher.specialization }}</p>
          </div>
        </div>
        <div class="flex items-center space-x-3">
          <button
            @click="openResetPasswordModal"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
            Reset Password
          </button>
          <span :class="[
            'px-3 py-1 rounded-full text-sm font-medium',
            teacher.is_approved 
              ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' 
              : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
          ]">
            {{ teacher.is_approved ? 'Active' : 'Pending' }}
          </span>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8 text-center">
              <div class="w-24 h-24 mx-auto rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-3xl font-bold text-indigo-600">
                {{ teacher.first_name?.charAt(0) }}{{ teacher.last_name?.charAt(0) }}
              </div>
              <h2 class="mt-4 text-xl font-semibold text-white">{{ teacher.full_name }}</h2>
              <p class="text-indigo-100">{{ teacher.specialization || 'Teacher' }}</p>
            </div>
            <div class="p-6 space-y-4">
              <div class="flex items-center text-sm">
                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span class="text-gray-600 dark:text-gray-300">{{ teacher.email || 'N/A' }}</span>
              </div>
              <div class="flex items-center text-sm">
                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <span class="text-gray-600 dark:text-gray-300">{{ teacher.phone || 'N/A' }}</span>
              </div>
              <hr class="border-gray-200 dark:border-gray-700">
              <div class="text-sm">
                <p class="text-gray-500 dark:text-gray-400 mb-1">Total Modules</p>
                <p class="font-medium text-gray-900 dark:text-white text-2xl">{{ teacher.modules?.length || 0 }}</p>
              </div>
              <div class="text-sm">
                <p class="text-gray-500 dark:text-gray-400 mb-1">Member Since</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ formatDate(teacher.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Modules & Schedule -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Modules -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Modules Taught</h3>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Module</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Specialty</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Hours/Week</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-if="!teacher.modules || teacher.modules.length === 0">
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                      No modules assigned
                    </td>
                  </tr>
                  <tr v-for="module in teacher.modules" :key="module.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ module.name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ module.code }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ module.specialty }}</td>
                    <td class="px-6 py-4 text-sm text-center text-gray-500 dark:text-gray-400">{{ module.hours_per_week }}h</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Weekly Schedule -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Weekly Schedule</h3>
            </div>
            <div class="p-6">
              <div v-if="scheduleLoading" class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
              </div>
              <div v-else-if="!schedule || schedule.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                No schedule found
              </div>
              <div v-else class="space-y-4">
                <div v-for="day in daysOfWeek" :key="day.value" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                  <div class="bg-gray-50 dark:bg-gray-900 px-4 py-2 font-semibold text-gray-700 dark:text-gray-300">
                    {{ day.label }}
                  </div>
                  <div class="p-4">
                    <div v-if="getScheduleForDay(day.value).length === 0" class="text-sm text-gray-500 dark:text-gray-400 italic">
                      No classes scheduled
                    </div>
                    <div v-else class="space-y-2">
                      <div 
                        v-for="session in getScheduleForDay(day.value)" 
                        :key="session.id"
                        class="flex items-center justify-between p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg"
                      >
                        <div>
                          <p class="font-medium text-gray-900 dark:text-white">{{ session.module_name }}</p>
                          <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ session.specialty_name }} - S{{ session.semester }} / Group {{ session.group }}
                          </p>
                          <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                            <span class="px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900 rounded text-indigo-700 dark:text-indigo-300">
                              {{ session.session_type }}
                            </span>
                          </p>
                        </div>
                        <div class="text-right">
                          <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                            {{ formatTime(session.start_time) }} - {{ formatTime(session.end_time) }}
                          </p>
                          <p class="text-sm text-gray-500 dark:text-gray-400">Room {{ session.room_number }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reset Password Modal -->
    <div v-if="showResetPasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-opacity-70 overflow-y-auto h-full w-full z-50 flex items-center justify-center" @click.self="closeResetPasswordModal">
      <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Reset Teacher Password</h3>
          <button @click="closeResetPasswordModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <div class="px-6 py-4">
          <div v-if="newPassword" class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <p class="text-sm font-medium text-green-800 dark:text-green-200 mb-2">Password Reset Successfully!</p>
            <div class="flex items-center justify-between bg-white dark:bg-gray-700 p-3 rounded border border-green-300 dark:border-green-700">
              <code class="text-lg font-mono text-gray-900 dark:text-white">{{ newPassword }}</code>
              <button @click="copyPassword" class="ml-2 p-2 text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
              </button>
            </div>
            <p class="text-xs text-green-700 dark:text-green-300 mt-2">⚠️ Make sure to save this password - it won't be shown again!</p>
          </div>
          <div v-else>
            <form @submit.prevent="handleResetPassword">
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  New Password <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="passwordForm.new_password"
                  type="text"
                  required
                  minlength="6"
                  placeholder="Enter new password (min 6 characters)"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                />
              </div>
              <div v-if="resetError" class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
                <p class="text-sm text-red-600 dark:text-red-400">{{ resetError }}</p>
              </div>
              <div class="flex justify-end space-x-3">
                <button
                  type="button"
                  @click="closeResetPasswordModal"
                  class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  :disabled="resetting"
                  class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                >
                  <svg v-if="resetting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ resetting ? 'Resetting...' : 'Reset Password' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from '@/api/axios'

const route = useRoute()

const loading = ref(true)
const scheduleLoading = ref(true)
const error = ref(null)
const teacher = ref(null)
const schedule = ref([])

const showResetPasswordModal = ref(false)
const passwordForm = ref({ new_password: '' })
const newPassword = ref('')
const resetting = ref(false)
const resetError = ref(null)

const daysOfWeek = [
  { value: 1, label: 'Monday' },
  { value: 2, label: 'Tuesday' },
  { value: 3, label: 'Wednesday' },
  { value: 4, label: 'Thursday' },
  { value: 5, label: 'Friday' },
  { value: 6, label: 'Saturday' },
  { value: 0, label: 'Sunday' }
]

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatTime = (time) => {
  if (!time) return ''
  const [hours, minutes] = time.split(':')
  return `${hours}:${minutes}`
}

const getScheduleForDay = (dayValue) => {
  return schedule.value.filter(session => session.day_of_week === dayValue)
}

const fetchTeacherDetails = async () => {
  loading.value = true
  error.value = null

  try {
    const response = await axios.get(`/api/admin/teachers/${route.params.id}`)
    teacher.value = response.data.teacher
  } catch (err) {
    console.error('Error fetching teacher details:', err)
    error.value = err.response?.data?.message || 'Failed to load teacher details'
  } finally {
    loading.value = false
  }
}

const fetchTeacherSchedule = async () => {
  scheduleLoading.value = true

  try {
    const response = await axios.get(`/api/admin/teachers/${route.params.id}/schedule`)
    schedule.value = response.data.schedule || []
  } catch (err) {
    console.error('Error fetching teacher schedule:', err)
  } finally {
    scheduleLoading.value = false
  }
}

const openResetPasswordModal = () => {
  showResetPasswordModal.value = true
  passwordForm.value = { new_password: '' }
  newPassword.value = ''
  resetError.value = null
}

const closeResetPasswordModal = () => {
  showResetPasswordModal.value = false
  passwordForm.value = { new_password: '' }
  newPassword.value = ''
  resetError.value = null
}

const handleResetPassword = async () => {
  resetError.value = null
  resetting.value = true

  try {
    const response = await axios.post(`/api/admin/teachers/${route.params.id}/reset-password`, passwordForm.value)
    newPassword.value = response.data.new_password
    passwordForm.value = { new_password: '' }
  } catch (err) {
    console.error('Error resetting password:', err)
    resetError.value = err.response?.data?.message || 'Failed to reset password'
  } finally {
    resetting.value = false
  }
}

const copyPassword = async () => {
  try {
    await navigator.clipboard.writeText(newPassword.value)
    alert('Password copied to clipboard!')
  } catch (err) {
    console.error('Failed to copy password:', err)
  }
}

onMounted(() => {
  fetchTeacherDetails()
  fetchTeacherSchedule()
})
</script>
