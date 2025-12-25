<script setup>
import { ref, onMounted } from 'vue'
import Card from '@/components/common/Card.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import studentApi from '@/api/endpoints/student'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'

const authStore = useAuthStore()
const themeStore = useThemeStore()

const loading = ref(true)
const saving = ref(false)
const error = ref(null)
const successMessage = ref(null)
const editMode = ref(false)
const activeTab = ref('info') // 'info' or 'settings'

const profile = ref({
  id: null,
  registration_number: '',
  first_name: '',
  last_name: '',
  date_of_birth: '',
  address: '',
  email: '',
  phone: '',
  specialty: {},
  current_semester: null,
  study_mode: '',
  years_enrolled: null,
  is_graduated: false,
  created_at: ''
})

const editableProfile = ref({})

const passwordForm = ref({
  current_password: '',
  new_password: '',
  confirm_password: ''
})

// Load profile data
const loadProfile = async () => {
  try {
    loading.value = true
    error.value = null
    
    const data = await studentApi.getProfile()
    
    console.log('📥 Profile API Response:', data)
    
    // Map backend response directly
    profile.value = {
      id: data.id,
      registration_number: data.registration_number,
      first_name: data.first_name,
      last_name: data.last_name,
      date_of_birth: data.date_of_birth,
      address: data.address,
      email: data.email,
      phone: data.phone,
      specialty: data.specialty,
      current_semester: data.current_semester,
      study_mode: data.study_mode,
      years_enrolled: data.years_enrolled,
      is_graduated: data.is_graduated,
      created_at: data.created_at
    }
    
    console.log('✅ Profile Data Set:', profile.value)
    
    editableProfile.value = { 
      phone: data.phone || '',
      address: data.address || '',
      date_of_birth: data.date_of_birth || ''
    }
  } catch (err) {
    console.error('❌ Failed to fetch profile:', err)
    console.error('Error Response:', err.response?.data)
    error.value = 'Failed to load profile data. Please try again.'
  } finally {
    loading.value = false
  }
}

// Enable edit mode
const enableEdit = () => {
  editableProfile.value = { 
    phone: profile.value.phone,
    address: profile.value.address,
    date_of_birth: profile.value.date_of_birth
  }
  editMode.value = true
}

// Cancel edit
const cancelEdit = () => {
  editMode.value = false
  error.value = null
}

// Save profile changes
const saveProfile = async () => {
  try {
    saving.value = true
    error.value = null
    successMessage.value = null
    
    const response = await studentApi.updateProfile(editableProfile.value)
    
    // Update local profile with full response from backend
    profile.value = {
      id: response.id,
      registration_number: response.registration_number,
      first_name: response.first_name,
      last_name: response.last_name,
      date_of_birth: response.date_of_birth,
      address: response.address,
      email: response.email,
      phone: response.phone,
      specialty: response.specialty,
      current_semester: response.current_semester,
      study_mode: response.study_mode,
      years_enrolled: response.years_enrolled,
      is_graduated: response.is_graduated,
      created_at: response.created_at
    }
    
    successMessage.value = 'Profile updated successfully!'
    editMode.value = false
    
    setTimeout(() => {
      successMessage.value = null
    }, 3000)
  } catch (err) {
    console.error('Failed to save profile:', err)
    error.value = err.response?.data?.message || 'Failed to update profile. Please try again.'
  } finally {
    saving.value = false
  }
}

// Change password
const changePassword = async () => {
  // Validate
  if (passwordForm.value.new_password !== passwordForm.value.confirm_password) {
    error.value = 'Passwords do not match'
    return
  }
  
  if (passwordForm.value.new_password.length < 8) {
    error.value = 'Password must be at least 8 characters'
    return
  }
  
  try {
    saving.value = true
    error.value = null
    
    await studentApi.updatePassword({
      current_password: passwordForm.value.current_password,
      new_password: passwordForm.value.new_password
    })
    
    successMessage.value = 'Password changed successfully!'
    
    // Clear form
    passwordForm.value = {
      current_password: '',
      new_password: '',
      confirm_password: ''
    }
    
    setTimeout(() => {
      successMessage.value = null
    }, 3000)
  } catch (err) {
    console.error('Failed to change password:', err)
    error.value = err.response?.data?.message || 'Failed to change password'
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadProfile()
})
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Profile</h1>
      <p class="text-gray-600 dark:text-gray-400 mt-2">Manage your personal information and settings</p>
    </div>

    <div v-if="loading" class="flex justify-center items-center h-64">
      <LoadingSpinner size="large" />
    </div>

    <div v-else>
      <!-- Success Message -->
      <div v-if="successMessage" class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
        <p class="text-green-600 dark:text-green-400 text-sm">✓ {{ successMessage }}</p>
      </div>

      <!-- Error Alert -->
      <div v-if="error" class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
        <p class="text-red-600 dark:text-red-400 text-sm">{{ error }}</p>
      </div>

      <!-- Tabs -->
      <div class="flex gap-2 mb-6 border-b border-gray-200 dark:border-gray-700">
        <button @click="activeTab = 'info'; error = null"
                :class="[
                  'px-6 py-3 font-medium text-sm transition-colors border-b-2',
                  activeTab === 'info' 
                    ? 'border-blue-500 text-blue-600 dark:text-blue-400' 
                    : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                ]">
          Personal Information
        </button>
        <button @click="activeTab = 'settings'; error = null"
                :class="[
                  'px-6 py-3 font-medium text-sm transition-colors border-b-2',
                  activeTab === 'settings' 
                    ? 'border-blue-500 text-blue-600 dark:text-blue-400' 
                    : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                ]">
          Settings
        </button>
      </div>

      <!-- Personal Information Tab -->
      <div v-show="activeTab === 'info'">
        <Card title="Profile Information">
          <div class="space-y-6">
            <!-- Avatar + Name -->
            <div class="flex items-center gap-6 pb-6 border-b border-gray-200 dark:border-gray-700">
              <div class="w-24 h-24 rounded-full bg-blue-500 flex items-center justify-center text-white text-3xl font-bold">
                {{ profile.first_name?.charAt(0)?.toUpperCase() || 'S' }}
              </div>
              <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ profile.first_name }} {{ profile.last_name }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Registration Number: {{ profile.registration_number }}</p>
              </div>
            </div>
            
            <!-- Profile fields grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- First Name -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
                <p class="px-4 py-2 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 rounded-lg">{{ profile.first_name || 'N/A' }}</p>
              </div>

              <!-- Last Name -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name</label>
                <p class="px-4 py-2 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 rounded-lg">{{ profile.last_name || 'N/A' }}</p>
              </div>
              
              <!-- Email -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                <p class="px-4 py-2 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 rounded-lg">{{ profile.email || 'N/A' }}</p>
              </div>
              
              <!-- Phone -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                <input v-if="editMode" v-model="editableProfile.phone" type="tel" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p v-else class="px-4 py-2 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 rounded-lg">{{ profile.phone || 'N/A' }}</p>
              </div>
              
              <!-- Date of Birth -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                <input v-if="editMode" v-model="editableProfile.date_of_birth" type="date" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p v-else class="px-4 py-2 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 rounded-lg">{{ profile.date_of_birth || 'N/A' }}</p>
              </div>

              <!-- Specialty -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Specialty</label>
                <p class="px-4 py-2 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 rounded-lg">{{ profile.specialty?.name || 'N/A' }}</p>
              </div>

              <!-- Current Semester -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Semester</label>
                <p class="px-4 py-2 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 rounded-lg">{{ profile.current_semester || 'N/A' }}</p>
              </div>

              <!-- Study Mode -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Study Mode</label>
                <p class="px-4 py-2 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 rounded-lg">{{ profile.study_mode || 'N/A' }}</p>
              </div>
              
              <!-- Address (full width) -->
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                <textarea v-if="editMode" v-model="editableProfile.address" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                <p v-else class="px-4 py-2 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 rounded-lg min-h-[80px]">{{ profile.address || 'N/A' }}</p>
              </div>
              
              <!-- Enrollment Date (read-only) -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Enrollment Date</label>
                <p class="px-4 py-2 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 rounded-lg">{{ profile.created_at ? new Date(profile.created_at).toLocaleDateString() : 'N/A' }}</p>
              </div>
            </div>
            
            <!-- Action buttons -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
              <button v-if="!editMode" @click="enableEdit" class="px-6 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-medium">Edit Profile</button>
              <template v-else>
                <button @click="cancelEdit" class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors font-medium">Cancel</button>
                <button @click="saveProfile" :disabled="saving" class="px-6 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">{{ saving ? 'Saving...' : 'Save Changes' }}</button>
              </template>
            </div>
          </div>
        </Card>
      </div>

      <!-- Settings Tab -->
      <div v-show="activeTab === 'settings'" class="space-y-6">
        <!-- Theme Settings -->
        <Card title="Appearance">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-base font-medium text-gray-900 dark:text-white">Dark Mode</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Toggle between light and dark theme</p>
            </div>
            <button @click="themeStore.toggleTheme"
                    type="button"
                    class="relative inline-flex h-8 w-14 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    :class="themeStore.isDark ? 'bg-blue-500' : 'bg-gray-300'">
              <span class="pointer-events-none inline-block h-7 w-7 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                    :class="themeStore.isDark ? 'translate-x-6' : 'translate-x-0'">
              </span>
            </button>
          </div>
        </Card>
        
        <!-- Change Password -->
        <Card title="Change Password">
          <form @submit.prevent="changePassword" class="space-y-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Current Password
              </label>
              <input v-model="passwordForm.current_password"
                     type="password"
                     required
                     class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                New Password
              </label>
              <input v-model="passwordForm.new_password"
                     type="password"
                     required
                     minlength="8"
                     class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">Must be at least 8 characters long</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Confirm New Password
              </label>
              <input v-model="passwordForm.confirm_password"
                     type="password"
                     required
                     class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
              <button type="submit"
                      :disabled="saving"
                      class="px-6 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                {{ saving ? 'Updating...' : 'Update Password' }}
              </button>
            </div>
          </form>
        </Card>
      </div>
    </div>
  </div>
</template>
