<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import studentApi from '@/api/endpoints/student'
import Card from '@/components/common/Card.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { 
  BookOpenIcon, 
  AcademicCapIcon,
  ClockIcon, 
  ClipboardDocumentListIcon,
  CalendarIcon
} from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const loading = ref(true)
const error = ref(null)
const stats = ref({
  modulesCount: 0,
  currentGPA: 0,
  classesThisWeek: 0,
  pendingTasks: 0
})
const attendanceData = ref({
  total: 0,
  present: 0,
  late: 0,
  absent: 0,
  excused: 0
})
const weeklySchedule = ref([])
const modules = ref([])

onMounted(async () => {
  try {
    loading.value = true
    console.log('Loading dashboard...')
    const data = await studentApi.getDashboard()
    console.log('Dashboard response:', data)
    
    // Update stats with API response
    stats.value = {
      modulesCount: data.statistics?.modules_count || 0,
      currentGPA: data.statistics?.gpa || 0,
      classesThisWeek: data.statistics?.classes_this_week || 0,
      pendingTasks: data.statistics?.pending_tasks || 0
    }
    
    attendanceData.value = {
      total: data.statistics?.attendance?.total || 0,
      present: data.statistics?.attendance?.present || 0,
      late: data.statistics?.attendance?.late || 0,
      absent: data.statistics?.attendance?.absent || 0,
      excused: data.statistics?.attendance?.excused || 0
    }
    
    weeklySchedule.value = data.weekly_schedule || []
    modules.value = data.modules || []
    
    console.log('Stats:', stats.value)
    console.log('Attendance:', attendanceData.value)
    console.log('Weekly schedule:', weeklySchedule.value)
    
    // Update auth store user info if needed
    if (data.student) {
      authStore.user = { ...authStore.user, ...data.student }
    }
    
  } catch (err) {
    console.error('Failed to fetch dashboard stats:', err)
    error.value = 'Failed to load dashboard data'
  } finally {
    loading.value = false
  }
})

const maxAttendanceValue = computed(() => {
  return Math.max(attendanceData.value.total, attendanceData.value.present, attendanceData.value.late, attendanceData.value.absent, attendanceData.value.excused, 1)
})

const getBarHeight = (value) => {
  return `${(value / maxAttendanceValue.value) * 100}%`
}

const weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']
const timeSlots = ['08:00 - 10:30', '11:00 - 12:30']

const getClassForSlot = (day, time) => {
  return weeklySchedule.value.find(item => 
    item.day === day && item.time === time
  )
}
</script>

<template>
  <div>
    <div v-if="loading" class="flex justify-center items-center h-64">
      <LoadingSpinner size="large" />
    </div>

    <div v-else>
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Student Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Welcome back, {{ authStore.user?.full_name || authStore.user?.name || 'Student' }}
        </p>
        <div v-if="authStore.user?.specialty" class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
          {{ authStore.user.specialty.name }} - Semester {{ authStore.user.current_semester }}
        </div>
      </div>

      <div v-if="error" class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
        <p class="text-red-600 dark:text-red-400 text-sm">{{ error }}</p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Enrolled Courses -->
        <Card no-padding>
          <div class="p-6">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Enrolled Courses</p>
              <BookOpenIcon class="w-5 h-5 text-blue-500" />
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.modulesCount }}</h3>
            <div class="mt-2 flex items-center text-xs text-green-600 dark:text-green-400">
              <span class="mr-1">●</span>
              <span>Active</span>
            </div>
          </div>
        </Card>

        <!-- Current GPA -->
        <Card no-padding>
          <div class="p-6">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Current GPA</p>
              <AcademicCapIcon class="w-5 h-5 text-green-500" />
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.currentGPA.toFixed(1) }}</h3>
            <div class="mt-2 flex items-center text-xs text-green-600 dark:text-green-400">
              <span>▲ Top 10%</span>
            </div>
          </div>
        </Card>

        <!-- Classes This Week -->
        <Card no-padding>
          <div class="p-6">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Classes This Week</p>
              <ClockIcon class="w-5 h-5 text-orange-500" />
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.classesThisWeek }}</h3>
            <div class="mt-2 flex items-center text-xs text-gray-600 dark:text-gray-400">
              <span>Same load</span>
            </div>
          </div>
        </Card>

        <!-- Pending Tasks -->
        <Card no-padding>
          <div class="p-6">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Tasks</p>
              <ClipboardDocumentListIcon class="w-5 h-5 text-red-500" />
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.pendingTasks }}</h3>
            <div class="mt-2 flex items-center text-xs text-red-600 dark:text-red-400">
              <span>▲ Due soon</span>
            </div>
          </div>
        </Card>
      </div>

      <!-- Attendance Overview Chart -->
      <Card title="Student Attendance Overview" class="mb-8">
        <div v-if="attendanceData.total > 0" class="relative py-8">
          <!-- Y-axis labels -->
          <div class="absolute left-0 top-8 h-80 flex flex-col justify-between text-xs text-gray-500 dark:text-gray-400 pr-2 text-right">
            <span>{{ maxAttendanceValue }}</span>
            <span>{{ Math.floor(maxAttendanceValue * 0.75) }}</span>
            <span>{{ Math.floor(maxAttendanceValue * 0.5) }}</span>
            <span>{{ Math.floor(maxAttendanceValue * 0.25) }}</span>
            <span>0</span>
          </div>

          <!-- Chart container -->
          <div class="ml-12 flex items-end justify-center space-x-12 h-80 border-l-2 border-b-2 border-gray-300 dark:border-gray-600 pb-4">
            <!-- Total Sessions Bar -->
            <div class="flex flex-col items-center justify-end h-full group">
              <div class="w-32 bg-blue-400 rounded-t-xl flex items-center justify-center text-white font-bold text-lg shadow-lg hover:shadow-2xl hover:shadow-blue-400/50 transition-all duration-300"
                   :style="{ height: `${(attendanceData.total / maxAttendanceValue * 100)}%`, minHeight: '60px' }">
                {{ attendanceData.total }}
              </div>
              <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Total Sessions</p>
            </div>

            <!-- Present Bar -->
            <div class="flex flex-col items-center justify-end h-full group">
              <div class="w-32 bg-cyan-400 rounded-t-xl flex items-center justify-center text-white font-bold text-lg shadow-lg hover:shadow-2xl hover:shadow-cyan-400/50 transition-all duration-300"
                   :style="{ height: `${(attendanceData.present / maxAttendanceValue * 100)}%`, minHeight: '60px' }">
                {{ attendanceData.present }}
              </div>
              <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Present</p>
            </div>

            <!-- Late Bar -->
            <div class="flex flex-col items-center justify-end h-full group">
              <div v-if="attendanceData.late > 0" class="w-32 bg-amber-300 rounded-t-xl flex items-center justify-center text-gray-800 font-bold text-lg shadow-lg hover:shadow-2xl hover:shadow-amber-300/50 transition-all duration-300"
                   :style="{ height: `${(attendanceData.late / maxAttendanceValue * 100)}%`, minHeight: '60px' }">
                {{ attendanceData.late }}
              </div>
              <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Late</p>
            </div>

            <!-- Absent Bar -->
            <div class="flex flex-col items-center justify-end h-full group">
              <div v-if="attendanceData.absent > 0" class="w-32 bg-red-400 rounded-t-xl flex items-center justify-center text-white font-bold text-lg shadow-lg hover:shadow-2xl hover:shadow-red-400/50 transition-all duration-300"
                   :style="{ height: `${(attendanceData.absent / maxAttendanceValue * 100)}%`, minHeight: '60px' }">
                {{ attendanceData.absent }}
              </div>
              <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Absent</p>
            </div>

            <!-- Excused Bar -->
            <div class="flex flex-col items-center justify-end h-full group">
              <div v-if="attendanceData.excused > 0" class="w-32 bg-purple-400 rounded-t-xl flex items-center justify-center text-white font-bold text-lg shadow-lg hover:shadow-2xl hover:shadow-purple-400/50 transition-all duration-300"
                   :style="{ height: `${(attendanceData.excused / maxAttendanceValue * 100)}%`, minHeight: '60px' }">
                {{ attendanceData.excused }}
              </div>
              <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Excused</p>
            </div>
          </div>
        </div>
        <div v-else class="h-80 flex items-center justify-center text-gray-500 dark:text-gray-400">
          No attendance data available
        </div>

        <!-- Legend -->
        <div class="flex items-center justify-center flex-wrap gap-6 mt-8">
          <div class="flex items-center">
            <div class="w-5 h-5 bg-blue-400 rounded mr-2"></div>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Total Sessions</span>
          </div>
          <div class="flex items-center">
            <div class="w-5 h-5 bg-cyan-400 rounded mr-2"></div>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Present</span>
          </div>
          <div class="flex items-center">
            <div class="w-5 h-5 bg-amber-300 rounded mr-2"></div>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Late</span>
          </div>
          <div class="flex items-center">
            <div class="w-5 h-5 bg-red-400 rounded mr-2"></div>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Absent</span>
          </div>
          <div class="flex items-center">
            <div class="w-5 h-5 bg-purple-400 rounded mr-2"></div>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Excused</span>
          </div>
        </div>
      </Card>

      <!-- Modules List with Coefficients -->
      <Card title="Current Semester Modules" class="mb-8">
        <div v-if="modules.length > 0" class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b-2 border-gray-300 dark:border-gray-600">
                <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-800 dark:to-gray-800/50">Code</th>
                <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-800 dark:to-gray-800/50">Module Name</th>
                <th class="text-center py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-800 dark:to-gray-800/50">Coefficient</th>
                <th class="text-center py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-800 dark:to-gray-800/50">Hours/Week</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="module in modules" :key="module.id" class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                <td class="py-4 px-4 text-sm font-mono font-semibold text-blue-600 dark:text-blue-400">{{ module.code }}</td>
                <td class="py-4 px-4 text-sm font-medium text-gray-900 dark:text-white">{{ module.name }}</td>
                <td class="py-4 px-4 text-center">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-900 dark:from-blue-900/30 dark:to-indigo-900/30 dark:text-blue-100">
                    {{ module.coefficient }}
                  </span>
                </td>
                <td class="py-4 px-4 text-center text-sm text-gray-600 dark:text-gray-400">{{ module.hours_per_week }}h</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="bg-gray-50 dark:bg-gray-800/50">
                <td colspan="2" class="py-4 px-4 text-sm font-bold text-gray-900 dark:text-white">Total</td>
                <td class="py-4 px-4 text-center">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-gradient-to-r from-green-100 to-emerald-100 text-green-900 dark:from-green-900/30 dark:to-emerald-900/30 dark:text-green-100">
                    {{ modules.reduce((sum, m) => sum + (m.coefficient || 0), 0) }}
                  </span>
                </td>
                <td class="py-4 px-4 text-center text-sm font-bold text-gray-900 dark:text-white">
                  {{ modules.reduce((sum, m) => sum + (m.hours_per_week || 0), 0) }}h
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
        <div v-else class="h-32 flex items-center justify-center text-gray-500 dark:text-gray-400">
          No modules found for current semester
        </div>
      </Card>
    </div>
  </div>
</template>
