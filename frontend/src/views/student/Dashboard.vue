<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import studentApi from '@/api/endpoints/student'
import Card from '@/components/common/Card.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { 
  BookOpenIcon, 
  ChartBarIcon, 
  ClockIcon, 
  ClipboardDocumentListIcon 
} from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const loading = ref(true)
const error = ref(null)
const stats = ref({
  modulesCount: 0,
  attendanceRate: 0,
  gradesCount: 0,
  upcomingExamsCount: 0
})
const recentGrades = ref([])
const upcomingExams = ref([])

onMounted(async () => {
  try {
    loading.value = true
    console.log('Loading dashboard...')
    const data = await studentApi.getDashboard()
    console.log('Dashboard response:', data)
    
    // Update stats with API response
    stats.value = {
      modulesCount: data.statistics?.modules_count || 0,
      attendanceRate: data.statistics?.attendance?.rate || 0,
      gradesCount: data.statistics?.grades_count || 0,
      upcomingExamsCount: data.statistics?.upcoming_exams || 0
    }
    recentGrades.value = data.recent_grades || []
    upcomingExams.value = data.upcoming_exams || []
    
    console.log('Stats:', stats.value)
    console.log('Recent grades:', recentGrades.value.length)
    console.log('Upcoming exams:', upcomingExams.value.length)
    
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
        <!-- Enrolled Modules -->
        <Card no-padding>
          <div class="p-6 flex items-center">
            <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mr-4">
              <BookOpenIcon class="w-6 h-6" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Enrolled Modules</p>
              <div class="flex items-center mt-1">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mr-2">{{ stats.modulesCount }}</h3>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                  Active
                </span>
              </div>
            </div>
          </div>
        </Card>

        <!-- Attendance Rate -->
        <Card no-padding>
          <div class="p-6 flex items-center">
            <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 mr-4">
              <ChartBarIcon class="w-6 h-6" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Attendance Rate</p>
              <div class="flex items-center mt-1">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mr-2">{{ stats.attendanceRate }}%</h3>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                  Current
                </span>
              </div>
            </div>
          </div>
        </Card>

        <!-- Grades Count -->
        <Card no-padding>
          <div class="p-6 flex items-center">
            <div class="p-3 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 mr-4">
              <ClockIcon class="w-6 h-6" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Grades Recorded</p>
              <div class="flex items-center mt-1">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mr-2">{{ stats.gradesCount }}</h3>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                  Total
                </span>
              </div>
            </div>
          </div>
        </Card>

        <!-- Upcoming Exams -->
        <Card no-padding>
          <div class="p-6 flex items-center">
            <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 mr-4">
              <ClipboardDocumentListIcon class="w-6 h-6" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Upcoming Exams</p>
              <div class="flex items-center mt-1">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mr-2">{{ stats.upcomingExamsCount }}</h3>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                  Scheduled
                </span>
              </div>
            </div>
          </div>
        </Card>
      </div>

      <!-- Content Sections -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Grades -->
        <Card title="Recent Grades">
          <div v-if="recentGrades.length > 0" class="space-y-4">
            <div v-for="(grade, index) in recentGrades" :key="index" class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700 last:border-0 last:pb-0">
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ grade.module?.name || 'Unknown Module' }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ grade.date ? new Date(grade.date).toLocaleDateString() : 'N/A' }}</p>
              </div>
              <div class="text-right">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="grade.grade >= 10 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'">
                  {{ grade.grade }}/{{ grade.max_grade || 20 }}
                </span>
              </div>
            </div>
          </div>
          <div v-else class="h-32 flex items-center justify-center text-gray-500 dark:text-gray-400">
            No recent grades found
          </div>
        </Card>

        <!-- Upcoming Exams List -->
        <Card title="Upcoming Exams">
          <div v-if="upcomingExams.length > 0" class="space-y-4">
            <div v-for="(exam, index) in upcomingExams" :key="index" class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700 last:border-0 last:pb-0">
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ exam.module?.name || 'Unknown Module' }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ exam.date }} at {{ exam.start_time }}</p>
              </div>
              <div class="text-right">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                  {{ exam.type }}
                </span>
              </div>
            </div>
          </div>
          <div v-else class="h-32 flex items-center justify-center text-gray-500 dark:text-gray-400">
            No upcoming exams
          </div>
        </Card>
      </div>
    </div>
  </div>
</template>
