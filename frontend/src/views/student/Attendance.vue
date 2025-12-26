<script setup>
import { ref, computed, onMounted } from 'vue'
import Card from '@/components/common/Card.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import studentApi from '@/api/endpoints/student'
import { 
  BookOpenIcon, 
  CheckCircleIcon, 
  XCircleIcon, 
  ChartPieIcon 
} from '@heroicons/vue/24/outline'

const loading = ref(true)
const attendanceRecords = ref([])
const error = ref(null)

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
  try {
    loading.value = true
    const response = await studentApi.getAttendance()
    console.log('Attendance response:', response)
    attendanceRecords.value = response.data || []
    console.log('Attendance records loaded:', attendanceRecords.value.length)
  } catch (err) {
    console.error('Failed to fetch attendance:', err)
    error.value = 'Failed to load attendance records'
  } finally {
    loading.value = false
  }
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
        <span class="text-xl mr-3">⚠️</span>
        <p class="text-yellow-800 dark:text-yellow-400 text-sm font-medium pt-0.5">
          Warning: Your attendance rate is below 75%. Please attend classes regularly to avoid academic penalties.
        </p>
      </div>
    </div>
  </div>
</template>
