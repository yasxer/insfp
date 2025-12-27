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

    <!-- Student Details -->
    <div v-else-if="student">
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <button @click="$router.back()" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </button>
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ student.full_name }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ student.registration_number }}</p>
          </div>
        </div>
        <span :class="[
          'px-3 py-1 rounded-full text-sm font-medium',
          student.is_graduated 
            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' 
            : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
        ]">
          {{ student.is_graduated ? 'Graduated' : 'Enrolled' }}
        </span>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8 text-center">
              <div class="w-24 h-24 mx-auto rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-3xl font-bold text-indigo-600">
                {{ student.first_name?.charAt(0) }}{{ student.last_name?.charAt(0) }}
              </div>
              <h2 class="mt-4 text-xl font-semibold text-white">{{ student.full_name }}</h2>
              <p class="text-indigo-100">{{ student.specialty?.name || 'No Specialty' }}</p>
            </div>
            <div class="p-6 space-y-4">
              <div class="flex items-center text-sm">
                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span class="text-gray-600 dark:text-gray-300">{{ student.email || 'N/A' }}</span>
              </div>
              <div class="flex items-center text-sm">
                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <span class="text-gray-600 dark:text-gray-300">{{ student.phone || 'N/A' }}</span>
              </div>
              <div class="flex items-center text-sm">
                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-gray-600 dark:text-gray-300">{{ formatDate(student.date_of_birth) || 'N/A' }}</span>
              </div>
              <div class="flex items-center text-sm">
                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-gray-600 dark:text-gray-300">{{ student.address || 'N/A' }}</span>
              </div>
              <hr class="border-gray-200 dark:border-gray-700">
              <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                  <p class="text-gray-500 dark:text-gray-400">Study Mode</p>
                  <p class="font-medium text-gray-900 dark:text-white capitalize">{{ student.study_mode || 'N/A' }}</p>
                </div>
                <div>
                  <p class="text-gray-500 dark:text-gray-400">Group</p>
                  <p class="font-medium text-gray-900 dark:text-white">{{ student.group || 'N/A' }}</p>
                </div>
                <div>
                  <p class="text-gray-500 dark:text-gray-400">Current Semester</p>
                  <p class="font-medium text-gray-900 dark:text-white">S{{ student.current_semester }}</p>
                </div>
                <div>
                  <p class="text-gray-500 dark:text-gray-400">Years Enrolled</p>
                  <p class="font-medium text-gray-900 dark:text-white">{{ student.years_enrolled || 1 }}</p>
                </div>
              </div>
              <div v-if="student.is_graduated" class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <p class="text-sm text-green-800 dark:text-green-200">
                  <strong>Graduated:</strong> {{ student.graduation_year }} (S{{ student.graduation_semester }})
                </p>
                <p class="text-sm text-green-800 dark:text-green-200">
                  <strong>Final GPA:</strong> {{ student.final_gpa }}/20
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Grades & Averages -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Current Semester Grades -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Current Semester Grades (S{{ student.current_semester }})
              </h3>
              <div v-if="currentSemesterAverage !== null" class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 rounded-full">
                <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">
                  Average: {{ currentSemesterAverage }}/20
                </span>
              </div>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Module</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Code</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Coefficient</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Grade</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-if="currentSemesterGrades.length === 0">
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                      No modules found for this semester
                    </td>
                  </tr>
                  <tr v-for="grade in currentSemesterGrades" :key="grade.module_id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ grade.module_name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ grade.module_code }}</td>
                    <td class="px-6 py-4 text-sm text-center text-gray-500 dark:text-gray-400">{{ grade.coefficient }}</td>
                    <td class="px-6 py-4 text-center">
                      <span v-if="grade.has_grade" :class="[
                        'font-semibold',
                        grade.grade >= 10 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
                      ]">
                        {{ Number(grade.grade).toFixed(2) }}/20
                      </span>
                      <span v-else class="text-gray-400">—</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                      <span v-if="grade.has_grade" :class="[
                        'px-2 py-1 text-xs font-medium rounded-full',
                        grade.grade >= 10 
                          ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' 
                          : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                      ]">
                        {{ grade.grade >= 10 ? 'Pass' : 'Fail' }}
                      </span>
                      <span v-else class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                        Pending
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Previous Semester Averages -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Previous Semesters</h3>
            </div>
            <div class="p-6">
              <div v-if="semesterAverages.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                No previous semester records found
              </div>
              <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div 
                  v-for="sem in semesterAverages" 
                  :key="sem.semester"
                  :class="[
                    'p-4 rounded-lg border-2',
                    sem.result === 'passed' 
                      ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20' 
                      : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20'
                  ]"
                >
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Semester {{ sem.semester }}</span>
                    <span :class="[
                      'px-2 py-0.5 text-xs font-medium rounded-full',
                      sem.result === 'passed' 
                        ? 'bg-green-200 text-green-800 dark:bg-green-800 dark:text-green-200' 
                        : 'bg-red-200 text-red-800 dark:bg-red-800 dark:text-red-200'
                    ]">
                      {{ sem.result === 'passed' ? 'Passed' : 'Failed' }}
                    </span>
                  </div>
                  <div class="text-2xl font-bold" :class="[
                    sem.result === 'passed' ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300'
                  ]">
                    {{ Number(sem.average).toFixed(2) }}/20
                  </div>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ sem.academic_year }}</p>
                  <p v-if="sem.observations" class="text-xs text-gray-600 dark:text-gray-400 mt-2 italic">{{ sem.observations }}</p>
                </div>
              </div>
            </div>
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
const error = ref(null)
const student = ref(null)
const currentSemesterGrades = ref([])
const currentSemesterAverage = ref(null)
const semesterAverages = ref([])

const formatDate = (date) => {
  if (!date) return null
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const fetchStudentDetails = async () => {
  loading.value = true
  error.value = null

  try {
    const response = await axios.get(`/api/admin/students/${route.params.id}`)
    student.value = response.data.student
    currentSemesterGrades.value = response.data.current_semester_grades || []
    currentSemesterAverage.value = response.data.current_semester_average
    semesterAverages.value = response.data.semester_averages || []
  } catch (err) {
    console.error('Error fetching student details:', err)
    error.value = err.response?.data?.message || 'Failed to load student details'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchStudentDetails()
})
</script>
