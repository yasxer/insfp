<script setup>
import { ref, computed, onMounted } from 'vue'
import Card from '@/components/common/Card.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import studentApi from '@/api/endpoints/student'
import { 
  BookOpenIcon, 
  CheckCircleIcon, 
  XCircleIcon, 
  ChartPieIcon,
  CalendarIcon,
  AcademicCapIcon,
  FunnelIcon
} from '@heroicons/vue/24/outline'

const loading = ref(true)
const attendanceRecords = ref([])
const allRecords = ref([]) // Keep all records
const modules = ref([])
const error = ref(null)
const selectedDate = ref('')
const selectedModule = ref('')
const selectedStatus = ref('')

const loadAttendance = async () => {
  try {
    loading.value = true
    
    const response = await studentApi.getAttendance()
    console.log('Attendance response:', response)
    allRecords.value = response.data || []
    attendanceRecords.value = allRecords.value
    
    // Extract unique modules
    const uniqueModules = [...new Map(allRecords.value.map(r => 
      [r.module?.id, r.module]
    ).filter(([id]) => id)).values()]
    modules.value = uniqueModules
    
    console.log('Attendance records loaded:', attendanceRecords.value.length)
  } catch (err) {
    console.error('Failed to fetch attendance:', err)
    error.value = 'Failed to load attendance records'
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  let filtered = allRecords.value
  
  if (selectedDate.value) {
    filtered = filtered.filter(record => record.date === selectedDate.value)
  }
  
  if (selectedModule.value) {
    filtered = filtered.filter(record => record.module?.id === parseInt(selectedModule.value))
  }
  
  if (selectedStatus.value) {
    filtered = filtered.filter(record => record.status === selectedStatus.value)
  }
  
  attendanceRecords.value = filtered
}

const clearFilter = () => {
  selectedDate.value = ''
  selectedModule.value = ''
  selectedStatus.value = ''
  attendanceRecords.value = allRecords.value
}

const totalSessions = computed(() => attendanceRecords.value.length)

const attendedSessions = computed(() => 
  attendanceRecords.value.filter(r => r.status === 'present').length
)

const absentSessions = computed(() => 
  attendanceRecords.value.filter(r => r.status === 'absent').length
)

const lateSessions = computed(() => 
  attendanceRecords.value.filter(r => r.status === 'late').length
)

const excusedSessions = computed(() => 
  attendanceRecords.value.filter(r => r.status === 'excused').length
)

const attendanceRate = computed(() => {
  if (totalSessions.value === 0) return 0
  // Count present, late, and excused as "attended" or at least not "absent"
  // Usually late counts as present, excused counts as present for rate
  const effectivePresent = attendedSessions.value + lateSessions.value + excusedSessions.value
  return ((effectivePresent / totalSessions.value) * 100).toFixed(1)
})

const getStatusClass = (status) => {
  switch(status) {
    case 'present':
      return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
    case 'absent':
      return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
    case 'late':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
    case 'excused':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400'
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
  }
}

const getStatusText = (status) => {
  switch(status) {
    case 'present': return 'Present'
    case 'absent': return 'Absent'
    case 'late': return 'Late'
    case 'excused': return 'Excused'
    default: return status
  }
}

const getRateColorClass = (rate) => {
  if (rate >= 90) return 'text-green-600 dark:text-green-400'
  if (rate >= 75) return 'text-yellow-600 dark:text-yellow-400'
  return 'text-red-600 dark:text-red-400'
}

onMounted(async () => {
  loadAttendance()
})
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Attendance Records</h1>
      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        Track your class attendance and punctuality
      </p>
    </div>

    <div v-if="loading" class="flex justify-center items-center h-64">
      <LoadingSpinner size="large" />
    </div>

    <div v-else>
      <!-- Error Alert -->
      <div v-if="error" class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
        <p class="text-red-600 dark:text-red-400 text-sm">{{ error }}</p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Sessions -->
        <Card no-padding>
          <div class="p-6 flex items-center">
            <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mr-4">
              <BookOpenIcon class="w-6 h-6" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Sessions</p>
              <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ totalSessions }}</h3>
            </div>
          </div>
        </Card>

        <!-- Present -->
        <Card no-padding>
          <div class="p-6 flex items-center">
            <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 mr-4">
              <CheckCircleIcon class="w-6 h-6" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Present</p>
              <h3 class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ attendedSessions }}</h3>
            </div>
          </div>
        </Card>

        <!-- Absent -->
        <Card no-padding>
          <div class="p-6 flex items-center">
            <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 mr-4">
              <XCircleIcon class="w-6 h-6" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Absent</p>
              <h3 class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ absentSessions }}</h3>
            </div>
          </div>
        </Card>

        <!-- Attendance Rate -->
        <Card no-padding>
          <div class="p-6 flex items-center">
            <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 mr-4">
              <ChartPieIcon class="w-6 h-6" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Attendance Rate</p>
              <h3 class="text-2xl font-bold mt-1" :class="getRateColorClass(attendanceRate)">
                {{ attendanceRate }}%
              </h3>
            </div>
          </div>
        </Card>
      </div>

      <!-- Date Filter (Below Stats) -->
      <Card class="mb-6">
        <div class="flex flex-wrap items-end gap-4">
          <div class="flex-1 min-w-[180px]">
            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              <CalendarIcon class="w-5 h-5" />
              Filter by Date
            </label>
            <input 
              v-model="selectedDate"
              @change="applyFilters"
              type="date" 
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Select a date"
            />
          </div>
          <div class="flex-1 min-w-[180px]">
            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              <AcademicCapIcon class="w-5 h-5" />
              Filter by Module
            </label>
            <select 
              v-model="selectedModule"
              @change="applyFilters"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">All Modules</option>
              <option v-for="module in modules" :key="module.id" :value="module.id">
                {{ module.name }}
              </option>
            </select>
          </div>
          <div class="flex-1 min-w-[180px]">
            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              <FunnelIcon class="w-5 h-5" />
              Filter by Status
            </label>
            <select 
              v-model="selectedStatus"
              @change="applyFilters"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">All Status</option>
              <option value="present">Present</option>
              <option value="absent">Absent</option>
              <option value="late">Late</option>
              <option value="excused">Excused</option>
            </select>
          </div>
          <button 
            v-if="selectedDate || selectedModule || selectedStatus"
            @click="clearFilter"
            class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors"
          >
            Clear Filters
          </button>
        </div>
        <p v-if="selectedDate || selectedModule || selectedStatus" class="mt-3 text-sm text-blue-600 dark:text-blue-400">
          <span v-if="selectedDate">Date: <strong>{{ selectedDate }}</strong></span>
          <span v-if="selectedDate && (selectedModule || selectedStatus)"> • </span>
          <span v-if="selectedModule">Module: <strong>{{ modules.find(m => m.id === parseInt(selectedModule))?.name }}</strong></span>
          <span v-if="selectedModule && selectedStatus"> • </span>
          <span v-if="selectedStatus">Status: <strong>{{ getStatusText(selectedStatus) }}</strong></span>
        </p>
      </Card>

      <!-- Attendance History -->
      <Card title="Attendance History">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-200 dark:border-gray-700">
                <th class="text-left py-3 px-4 font-semibold text-sm text-gray-700 dark:text-gray-300">Date</th>
                <th class="text-left py-3 px-4 font-semibold text-sm text-gray-700 dark:text-gray-300">Subject</th>
                <th class="text-left py-3 px-4 font-semibold text-sm text-gray-700 dark:text-gray-300">Time</th>
                <th class="text-left py-3 px-4 font-semibold text-sm text-gray-700 dark:text-gray-300">Instructor</th>
                <th class="text-center py-3 px-4 font-semibold text-sm text-gray-700 dark:text-gray-300">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="attendanceRecords.length === 0">
                <td colspan="5" class="text-center py-8 text-gray-500 dark:text-gray-400">
                  No attendance records found
                </td>
              </tr>
              <tr v-for="record in attendanceRecords" :key="record.id" 
                  class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">{{ record.date }}</td>
                <td class="py-3 px-4 text-sm font-medium text-gray-900 dark:text-white">{{ record.module?.name || 'N/A' }}</td>
                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ record.start_time }} - {{ record.end_time }}</td>
                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ record.instructor }}</td>
                <td class="py-3 px-4 text-center">
                  <span :class="getStatusClass(record.status)" 
                        class="inline-block px-3 py-1 rounded-full text-xs font-medium">
                    {{ getStatusText(record.status) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </Card>

      <!-- Warning Message -->
      <div v-if="attendanceRate < 75 && totalSessions > 0" 
           class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg flex items-start">
        <XCircleIcon class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3 flex-shrink-0 mt-0.5" />
        <p class="text-yellow-800 dark:text-yellow-400 text-sm font-medium pt-0.5">
          Warning: Your attendance rate is below 75%. Please attend classes regularly to avoid academic penalties.
        </p>
      </div>
    </div>
  </div>
</template>
