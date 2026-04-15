<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import teacherApi from '@/api/endpoints/teacherPortal'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import {
  ArrowLeftIcon,
  CheckCircleIcon,
  UserCircleIcon,
  BookOpenIcon,
  CalendarIcon
} from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const examId = route.params.id

const loading = ref(true)
const saving = ref(false)
const examData = ref(null)
const students = ref([])
const error = ref(null)
const successMsg = ref(null)

onMounted(async () => {
  fetchExamData()
})

const fetchExamData = async () => {
  loading.value = true
  try {
    const data = await teacherApi.getExamStudents(examId)
    examData.value = data.exam
    // Make sure we have a reactivity-friendly list with mutable properties
    students.value = data.students.map(s => ({
      ...s,
      inputMark: s.mark !== null ? s.mark.toString() : '',
      inputNote: s.note || ''
    }))
  } catch (err) {
    console.error('Failed to fetch exam grading data:', err)
    error.value = "Erreur lors du chargement des données."
  } finally {
    loading.value = false
  }
}

const goBack = () => {
  router.push({ name: 'TeacherExams' })
}

const saveGrades = async () => {
  saving.value = true
  error.value = null
  successMsg.value = null
  
  try {
    const payload = {
      results: students.value
        .filter(s => s.inputMark !== '') // Only submit non-empty marks
        .map(s => ({
          student_id: s.id,
          mark: parseFloat(s.inputMark),
          note: s.inputNote || ''
        }))
    }
    
    // Simple validation
    const hasInvalidMarks = payload.results.some(r => isNaN(r.mark) || r.mark < 0 || r.mark > (examData.value?.max_mark || 20))
    if (hasInvalidMarks) {
      error.value = `Toutes les notes doivent être comprises entre 0 et ${examData.value?.max_mark || 20}.`
      saving.value = false
      return
    }

    if (payload.results.length === 0) {
      error.value = "Veuillez saisir au moins une note."
      saving.value = false
      return
    }
    
    await teacherApi.storeExamResults(examId, payload)
    successMsg.value = "Les notes ont été enregistrées avec succès."
    setTimeout(() => {
      successMsg.value = null
    }, 3000)
    
    // Refresh to get potentially computed letter grades
    await fetchExamData()
  } catch (err) {
    console.error('Failed to save grades:', err)
    error.value = err.response?.data?.message || "Erreur lors de l'enregistrement des notes."
  } finally {
    saving.value = false
  }
}

const formatType = (type) => {
  const types = {
    'exam': 'Examen',
    'test': 'Contrôle',
    'project': 'Projet',
    'assignment': 'Devoir',
    'presentation': 'Présentation'
  }
  return types[type] || type
}

const getGradeColor = (mark) => {
  if (!mark && mark !== 0) return 'text-gray-500'
  const val = parseFloat(mark)
  const max = examData.value?.max_mark || 20
  const ratio = val / max
  if (ratio >= 0.5) return 'text-green-600 dark:text-green-400 font-medium'
  return 'text-red-600 dark:text-red-400 font-medium'
}
</script>

<template>
  <div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <button 
          @click="goBack"
          class="p-2 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors"
        >
          <ArrowLeftIcon class="w-5 h-5" />
        </button>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Saisie des notes</h1>
      </div>
    </div>

    <!-- Error/Success Indicators -->
    <div v-if="error" class="p-4 bg-red-50 text-red-700 border border-red-200 rounded-lg">
      {{ error }}
    </div>
    <div v-if="successMsg" class="p-4 bg-green-50 text-green-700 border border-green-200 rounded-lg flex items-center">
      <CheckCircleIcon class="w-5 h-5 mr-2" /> {{ successMsg }}
    </div>

    <div v-if="loading" class="flex justify-center py-12">
      <LoadingSpinner size="lg" />
    </div>

    <template v-else>
      <!-- Exam Summary Card -->
      <div v-if="examData" class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300 text-xs font-bold rounded-full mb-2 capitalize">
            {{ formatType(examData.type) }}
          </span>
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">
            {{ examData.title }}
          </h2>
          <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mt-2">
            <span class="flex items-center"><CalendarIcon class="w-4 h-4 mr-1"/> {{ new Date(examData.date).toLocaleDateString('fr-FR') }}</span>
            <span class="flex items-center"><BookOpenIcon class="w-4 h-4 mr-1"/> {{ examData.module?.name }}</span>
            <span class="font-medium bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded text-gray-800 dark:text-gray-200">
              Sur {{ examData.max_mark }}
            </span>
          </div>
        </div>
      </div>

      <!-- Action Required Empty state -->
      <div v-if="students.length === 0" class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Aucun étudiant</h3>
        <p class="mt-1 text-gray-500">Aucun étudiant n'est inscrit dans le module pour cet examen.</p>
      </div>

      <!-- Grading List -->
      <div v-else class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Étudiant</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  <div class="flex items-center gap-1">
                    Note <span class="text-gray-400">(/{{ examData.max_mark }})</span>
                  </div>
                </th>
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Lettre</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/3">Observations (optionnel)</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="student in students" :key="student.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <UserCircleIcon class="w-8 h-8 text-gray-400 mr-3" />
                    <div>
                      <div class="text-sm font-medium text-gray-900 dark:text-white">{{ student.full_name }}</div>
                      <div class="text-sm text-gray-500">{{ student.registration_number }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="relative w-24">
                    <input 
                      v-model="student.inputMark"
                      type="number" 
                      step="0.1"
                      min="0"
                      :max="examData.max_mark"
                      placeholder="--" 
                      class="w-full text-sm font-medium px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                      :class="getGradeColor(student.inputMark)"
                    />
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span v-if="student.grade_letter" class="inline-block px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs font-bold rounded">
                    {{ student.grade_letter }}
                  </span>
                  <span v-else class="text-gray-400">-</span>
                </td>
                <td class="px-6 py-4">
                  <input 
                    v-model="student.inputNote"
                    type="text" 
                    placeholder="Remarque..." 
                    class="w-full text-sm px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Action footer -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end">
          <button 
            @click="saveGrades"
            :disabled="saving"
            class="flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <LoadingSpinner v-if="saving" size="sm" class="mr-2" color="text-white" />
            Enregistrer les notes
          </button>
        </div>
      </div>
    </template>
  </div>
</template>