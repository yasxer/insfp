<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from '@/api/axios'

const sessions = ref([])
const selectedSession = ref('')
const specialties = ref([])
const loading = ref(false)
const generatedNumber = ref(null)
const error = ref(null)

// Tabs control
const activeTab = ref('generator') // 'generator' or 'list'

// List Variables
const registrationList = ref([])
const listLoading = ref(false)
const listPagination = ref({})
const listFilters = ref({
  session_id: '',
  specialty_id: '',
  status: 'all',
  search: ''
})

// Fetch sessions when component mounts
onMounted(async () => {

  try {
    const response = await axios.get('/api/admin/registration/sessions')
    sessions.value = response.data
  } catch (err) {
    console.error('Error fetching sessions:', err)
    error.value = 'Failed to load sessions.'
  }
})

// When admin selects a session, fetch its specialties
watch(selectedSession, async (newSessionId) => {
  if (!newSessionId) {
    specialties.value = []
    return
  }
  
  loading.value = true
  error.value = null
  generatedNumber.value = null
  
  try {
    const response = await axios.get(`/api/admin/registration/sessions/${newSessionId}/specialties`)
    specialties.value = response.data
  } catch (err) {
    console.error('Error fetching specialties:', err)
    error.value = 'Failed to load specialties.'
  } finally {
    loading.value = false
  }
})

// Function to generate the registration number
const generateNumber = async (specialtyId, specialtyName) => {
  if (!selectedSession.value) {
    alert('Please select a session first.')
    return
  }
  
  if(!confirm(`Are you sure you want to generate a new registration number for the specialty: ${specialtyName}?`)) return;

  try {
    const response = await axios.post('/api/admin/generate-registration', {
      specialty_id: specialtyId,
      session_id: selectedSession.value
    })
    
    // Show the result
    generatedNumber.value = {
      number: response.data.registration_number,
      specialty: response.data.specialty,
      session: response.data.session,
      year: response.data.academic_year
    }
    
    // Refresh list if active
    if(activeTab.value === 'list') fetchRegistrationList()
    
  } catch (err) {
    console.error(err)
    alert('An error occurred while generating the number.')
  }
}

// Fetch registration list
const fetchRegistrationList = async (page = 1) => {
  listLoading.value = true
  try {
    const params = {
      page,
      ...listFilters.value
    }
    const response = await axios.get('/api/admin/registration/list', { params })
    registrationList.value = response.data.data
    listPagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        total: response.data.total
    }
  } catch (err) {
    console.error('Error fetching list:', err)
  } finally {
    listLoading.value = false
  }
}

// Watch filters to refresh list
watch(() => [activeTab.value], () => {
    if(activeTab.value === 'list') fetchRegistrationList()
})
</script>

<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Registration Numbers</h1>
      
      <!-- Tabs Navigation -->
      <div class="flex space-x-2 bg-gray-100 dark:bg-gray-700 p-1 rounded-lg">
        <button 
          @click="activeTab = 'generator'"
          :class="['px-4 py-2 text-sm font-medium rounded-md transition-colors', activeTab === 'generator' ? 'bg-white text-blue-600 shadow-sm dark:bg-gray-800 dark:text-blue-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white']"
        >
          Generator
        </button>
        <button 
          @click="activeTab = 'list'"
          :class="['px-4 py-2 text-sm font-medium rounded-md transition-colors', activeTab === 'list' ? 'bg-white text-blue-600 shadow-sm dark:bg-gray-800 dark:text-blue-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white']"
        >
          All Numbers List
        </button>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
      <strong class="font-bold">Error!</strong>
      <span class="block sm:inline"> {{ error }}</span>
    </div>

    <!-- TAB: GENERATOR -->
    <div v-show="activeTab === 'generator'">
      <!-- Session Selector -->
      <div class="mb-8 max-w-xl">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Session to Generate For</label>
        <select 
            v-model="selectedSession"
            class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border dark:bg-gray-700 dark:text-white"
        >
            <option value="" disabled>-- Select a Session --</option>
            <option v-for="session in sessions" :key="session.id" :value="session.id">
            {{ session.name }} (Start: {{ session.start_date }})
            </option>
        </select>
      </div>

      <!-- Generated Number Display -->
      <div v-if="generatedNumber" class="mb-8 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 flex justify-between items-center shadow-lg transform transition-all duration-300 ease-in-out">
        <div>
          <p class="text-green-800 dark:text-green-300 font-semibold text-lg">Registration Number Generated Successfully:</p>
          <p class="text-4xl font-mono font-bold text-green-700 dark:text-green-400 mt-2 tracking-widest bg-white dark:bg-gray-800 inline-block px-4 py-2 rounded border border-green-100 dark:border-green-900 shadow-sm">{{ generatedNumber.number }}</p>
          <div class="text-sm text-green-700 dark:text-green-400 mt-3 space-y-1">
            <p><span class="font-medium">Specialty:</span> {{ generatedNumber.specialty }}</p>
            <p><span class="font-medium">Session:</span> {{ generatedNumber.session }}</p>
          </div>
        </div>
        <button @click="generatedNumber = null" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 focus:outline-none p-2 rounded-full hover:bg-green-100 dark:hover:bg-green-900/50 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Specialties Table -->
      <div v-if="selectedSession" class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div v-if="loading" class="p-8 text-center text-gray-500 dark:text-gray-400">
          Loading specialties...
        </div>
        
        <div v-else-if="specialties.length > 0">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Specialty</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Code</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Study Mode</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="spec in specialties" :key="spec.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ spec.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 font-mono">{{ spec.code }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    {{ spec.study_type }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button 
                    @click="generateNumber(spec.id, spec.name)"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition"
                    >
                    Generate Number
                    </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="text-center py-10 text-gray-500 dark:text-gray-400">
          No specialties found available for this session.
        </div>
      </div>
      
      <div v-else class="text-center py-20 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-dashed border-gray-300 dark:border-gray-700">
        <p class="text-gray-500 dark:text-gray-400">Please select a session above to start generating numbers.</p>
      </div>
    </div>

    <!-- TAB: LIST -->
    <div v-show="activeTab === 'list'" class="space-y-4">
        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow border border-gray-200 dark:border-gray-700 flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Number</label>
                <input v-model="listFilters.search" type="text" placeholder="Search..." class="w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border dark:bg-gray-700 dark:text-white">
            </div>
            
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select v-model="listFilters.status" class="w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border dark:bg-gray-700 dark:text-white">
                    <option value="all">All Status</option>
                    <option value="available">Available</option>
                    <option value="used">Used</option>
                </select>
            </div>

             <div class="w-64">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Session</label>
                <select v-model="listFilters.session_id" class="w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border dark:bg-gray-700 dark:text-white">
                    <option value="">All Sessions</option>
                    <option v-for="session in sessions" :key="session.id" :value="session.id">
                        {{ session.name }}
                    </option>
                </select>
            </div>

            <button @click="fetchRegistrationList(1)" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <div v-if="listLoading" class="p-8 text-center text-gray-500 dark:text-gray-400">
                Loading list...
            </div>
            <div v-else>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Specialty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Session</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Used By</th>
                             <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="item in registrationList" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 whitespace-nowrap font-mono font-medium text-blue-600 dark:text-blue-400">{{ item.number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ item.specialty }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ item.session }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', item.status === 'Used' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200']">
                                    {{ item.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ item.used_by || '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ item.created_at }}</td>
                        </tr>
                        <tr v-if="registrationList.length === 0">
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No records found.</td>
                        </tr>
                    </tbody>
                </table>
                
                 <!-- Pagination -->
                <div v-if="listPagination.last_page > 1" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center bg-white dark:bg-gray-800">
                    <button 
                        @click="fetchRegistrationList(listPagination.current_page - 1)"
                        :disabled="listPagination.current_page <= 1"
                        class="px-3 py-1 border rounded disabled:opacity-50 dark:border-gray-600 dark:text-white dark:hover:bg-gray-700"
                    >
                        Previous
                    </button>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Page {{ listPagination.current_page }} of {{ listPagination.last_page }}</span>
                    <button 
                         @click="fetchRegistrationList(listPagination.current_page + 1)"
                         :disabled="listPagination.current_page >= listPagination.last_page"
                         class="px-3 py-1 border rounded disabled:opacity-50 dark:border-gray-600 dark:text-white dark:hover:bg-gray-700"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>

  </div>
</template>
