<script setup>
import { ref, computed, onMounted } from 'vue'
import Card from '@/components/common/Card.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import studentApi from '@/api/endpoints/student'
import { 
  ChartBarIcon, 
  ClipboardDocumentListIcon, 
  CheckCircleIcon, 
  XCircleIcon,
  CalendarIcon,
  ClockIcon,
  MapPinIcon,
  TagIcon
} from '@heroicons/vue/24/outline'

const loading = ref(true)
const examResults = ref([])
const upcomingExams = ref([])
const error = ref(null)
const activeTab = ref('results') // 'results' or 'upcoming'

const averageGrade = computed(() => {
  if (examResults.value.length === 0) return 0
  const sum = examResults.value.reduce((acc, exam) => acc + exam.grade, 0)
  return (sum / examResults.value.length).toFixed(2)
})

const passedExams = computed(() => 
  examResults.value.filter(exam => exam.grade >= 10).length
)

const failedExams = computed(() => 
  examResults.value.filter(exam => exam.grade < 10).length
)

const totalExams = computed(() => examResults.value.length)

const getGradeClass = (grade) => {
  if (grade >= 16) return 'text-green-600 dark:text-green-400 font-bold'
  if (grade >= 14) return 'text-blue-600 dark:text-blue-400 font-bold'
  if (grade >= 10) return 'text-yellow-600 dark:text-yellow-400 font-bold'
  return 'text-red-600 dark:text-red-400 font-bold'
}

const getGradeBadge = (grade) => {
  if (grade >= 16) return { text: 'Excellent', class: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' }
  if (grade >= 14) return { text: 'Very Good', class: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' }
  if (grade >= 12) return { text: 'Good', class: 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/20 dark:text-cyan-400' }
  if (grade >= 10) return { text: 'Pass', class: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' }
  return { text: 'Failed', class: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }
}

const isExamSoon = (dateString) => {
  const examDate = new Date(dateString)
  const today = new Date()
  const diffTime = examDate - today
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  return diffDays <= 7 && diffDays >= 0
}

onMounted(async () => {
  try {
    loading.value = true
    const [resultsData, upcomingData] = await Promise.all([
      studentApi.getExamResults(),
      studentApi.getUpcomingExams()
    ])
    examResults.value = resultsData.results || []
    upcomingExams.value = upcomingData.exams || []
  } catch (err) {
    console.error('Failed to fetch exams:', err)
    error.value = 'Failed to load exam data'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Exams & Results</h1>
      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        View your exam results and upcoming exams
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
        <!-- Average Grade -->
        <Card no-padding>
          <div class="p-6 flex items-center">
            <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 mr-4">
              <ChartBarIcon class="w-6 h-6" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Average Grade</p>
              <h3 class="text-2xl font-bold mt-1" :class="getGradeClass(averageGrade)">
                {{ averageGrade }} / 20
              </h3>
            </div>
          </div>
        </Card>

        <!-- Total Exams -->
        <Card no-padding>
          <div class="p-6 flex items-center">
            <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mr-4">
              <ClipboardDocumentListIcon class="w-6 h-6" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Exams</p>
              <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ totalExams }}</h3>
            </div>
          </div>
        </Card>

        <!-- Passed -->
        <Card no-padding>
          <div class="p-6 flex items-center">
            <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 mr-4">
              <CheckCircleIcon class="w-6 h-6" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Passed</p>
              <h3 class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ passedExams }}</h3>
            </div>
          </div>
        </Card>

        <!-- Failed -->
        <Card no-padding>
          <div class="p-6 flex items-center">
            <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 mr-4">
              <XCircleIcon class="w-6 h-6" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Failed</p>
              <h3 class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ failedExams }}</h3>
            </div>
          </div>
        </Card>
      </div>

      <!-- Tabs -->
      <div class="flex gap-2 mb-6 border-b border-gray-200 dark:border-gray-700">
        <button @click="activeTab = 'results'"
                :class="[
                  'px-6 py-3 font-medium text-sm transition-colors border-b-2',
                  activeTab === 'results' 
                    ? 'border-blue-500 text-blue-600 dark:text-blue-400' 
                    : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                ]">
          Exam Results
        </button>
        <button @click="activeTab = 'upcoming'"
                :class="[
                  'px-6 py-3 font-medium text-sm transition-colors border-b-2',
                  activeTab === 'upcoming' 
                    ? 'border-blue-500 text-blue-600 dark:text-blue-400' 
                    : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                ]">
          Upcoming Exams
          <span v-if="upcomingExams.length > 0" class="ml-2 px-2 py-0.5 bg-blue-500 text-white text-xs rounded-full">
            {{ upcomingExams.length }}
          </span>
        </button>
      </div>

      <!-- Results Tab -->
      <div v-if="activeTab === 'results'">
        <Card title="Exam Results">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                  <th class="text-left py-3 px-4 font-semibold text-sm text-gray-700 dark:text-gray-300">Subject</th>
                  <th class="text-left py-3 px-4 font-semibold text-sm text-gray-700 dark:text-gray-300">Exam Type</th>
                  <th class="text-left py-3 px-4 font-semibold text-sm text-gray-700 dark:text-gray-300">Date</th>
                  <th class="text-center py-3 px-4 font-semibold text-sm text-gray-700 dark:text-gray-300">Grade</th>
                  <th class="text-center py-3 px-4 font-semibold text-sm text-gray-700 dark:text-gray-300">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="examResults.length === 0">
                  <td colspan="5" class="text-center py-8 text-gray-500 dark:text-gray-400">
                    No exam results available yet
                  </td>
                </tr>
                <tr v-for="exam in examResults" :key="exam.id" 
                    class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                  <td class="py-3 px-4 text-sm font-medium text-gray-900 dark:text-white">{{ exam.subject }}</td>
                  <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ exam.type }}</td>
                  <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ exam.date }}</td>
                  <td class="py-3 px-4 text-center">
                    <span :class="getGradeClass(exam.grade)" class="text-lg">
                      {{ exam.grade }} / 20
                    </span>
                  </td>
                  <td class="py-3 px-4 text-center">
                    <span :class="getGradeBadge(exam.grade).class" 
                          class="inline-block px-3 py-1 rounded-full text-xs font-medium">
                      {{ getGradeBadge(exam.grade).text }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </Card>
      </div>

      <!-- Upcoming Tab -->
      <div v-if="activeTab === 'upcoming'">
        <div v-if="upcomingExams.length === 0">
          <Card>
            <p class="text-center py-8 text-gray-500 dark:text-gray-400">
              No upcoming exams scheduled
            </p>
          </Card>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <Card v-for="exam in upcomingExams" :key="exam.id">
            <div class="flex items-start justify-between mb-3">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ exam.subject }}</h3>
              <span v-if="isExamSoon(exam.date)" 
                    class="px-2 py-1 bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400 text-xs font-medium rounded">
                Soon
              </span>
            </div>
            <div class="space-y-2 text-sm">
              <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                <CalendarIcon class="w-4 h-4" />
                <span>{{ exam.date }}</span>
              </div>
              <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                <ClockIcon class="w-4 h-4" />
                <span>{{ exam.time }}</span>
              </div>
              <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                <MapPinIcon class="w-4 h-4" />
                <span>{{ exam.room }}</span>
              </div>
              <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                <TagIcon class="w-4 h-4" />
                <span>{{ exam.type }}</span>
              </div>
              <div v-if="exam.duration" class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                <ClockIcon class="w-4 h-4" />
                <span>Duration: {{ exam.duration }}</span>
              </div>
            </div>
          </Card>
        </div>
      </div>
    </div>
  </div>
</template>
