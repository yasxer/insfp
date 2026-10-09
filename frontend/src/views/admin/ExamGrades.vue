<template>
  <div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header with Back Button -->
    <div class="flex items-center gap-4 py-6 border-b border-gray-200 dark:border-gray-700">
      <router-link :to="{ name: 'AdminExams' }" class="text-gray-500 hover:text-blue-600 transition-colors group dark:text-gray-400">
        <svg class="rtl:rotate-180 w-6 h-6 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </router-link>
      <div>
        <h1 class="text-2xl font-extrabold text-blue-900 tracking-tight dark:text-blue-300">{{ t('admin.exam_grades.resultats_examen') }}</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
          {{ t('admin.exam_grades.consultez_toutes_notes_soumises_cet') }}
        </p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm dark:bg-red-900/30">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3"><p class="text-sm text-red-700 dark:text-red-300">{{ error }}</p></div>
      </div>
    </div>

    <template v-else-if="examData">
      <!-- Exam Info Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 dark:bg-gray-900 dark:border-gray-800">
          <div class="bg-blue-50 p-3 rounded-lg text-blue-600 dark:bg-blue-900/30 dark:text-blue-300">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
          </div>
          <div>
            <p class="text-xs text-gray-500 uppercase font-medium dark:text-gray-400">{{ t('common.title') }}</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ examData.exam.title }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ getExamLabel(examData.exam.type) }}</p>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 dark:bg-gray-900 dark:border-gray-800">
          <div class="bg-purple-50 p-3 rounded-lg text-purple-600 dark:bg-purple-900/30 dark:text-purple-300">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
          </div>
          <div>
            <p class="text-xs text-gray-500 uppercase font-medium dark:text-gray-400">{{ t('common.module') }}</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ examData.exam.module }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ examData.exam.group || '-' }}</p>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 dark:bg-gray-900 dark:border-gray-800">
          <div class="bg-green-50 p-3 rounded-lg text-green-600 dark:bg-green-900/30 dark:text-green-300">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
          </div>
          <div>
            <p class="text-xs text-gray-500 uppercase font-medium dark:text-gray-400">{{ t('admin.exam_grades.enseignant') }}</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ examData.exam.teacher }}</p>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 dark:bg-gray-900 dark:border-gray-800">
          <div class="bg-orange-50 p-3 rounded-lg text-orange-600 dark:bg-orange-900/30 dark:text-orange-300">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
          </div>
          <div>
            <p class="text-xs text-gray-500 uppercase font-medium dark:text-gray-400">{{ t('common.date') }}</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ formatDate(examData.exam.date) }}</p>
          </div>
        </div>
      </div>

      <!-- Grades List -->
      <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 mt-8 dark:bg-gray-900 dark:border-gray-800">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center dark:border-gray-800 dark:bg-gray-800/60">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ t('admin.exam_grades.liste_notes_etudiants', { p0: examData.grades.length }) }}</h3>
          <!-- stats summary optionally -->
        </div>

        <div v-if="examData.grades.length === 0" class="text-center py-16 bg-white dark:bg-gray-900">
          <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
          <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ t('admin.exam_grades.aucune_note_n_encore_ete') }}</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/60">
              <tr>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ t('admin.exam_grades.matricule') }}</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ t('admin.exam_grades.etudiant') }}</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ t('admin.exam_grades.note_20') }}</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ t('admin.exam_grades.appreciation') }}</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-800">
              <tr v-for="grade in examData.grades" :key="grade.id" class="hover:bg-gray-50 transition-colors dark:hover:bg-gray-800">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium dark:text-gray-300">
                  {{ grade.student.registration_number }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900 dark:text-white">{{ grade.student.last_name }} {{ grade.student.first_name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold"
                        :class="getGradeColor(grade.mark)">
                    {{ grade.mark.toFixed(2) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm" :class="getGradeColor(grade.mark, true)">
                  {{ getAppreciation(grade.mark) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import { dateLocale } from '@/i18n'
const { t } = useI18n()
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import adminApi from '@/api/endpoints/admin'

const route = useRoute()
const loading = ref(true)
const error = ref('')
const examData = ref(null)

const fetchGrades = async () => {
  loading.value = true
  error.value = ''
  try {
    const data = await adminApi.getExamGrades(route.params.id)
    examData.value = data
  } catch (err) {
    console.error("Error fetching grades:", err)
    error.value = err.response?.data?.message || 'Erreur lors du chargement des notes. Veuillez réessayer.'
  } finally {
    loading.value = false
  }
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString(dateLocale(), {
    weekday: 'short', year: 'numeric', month: 'long', day: 'numeric'
  })
}

const getExamLabel = (type) => {
  const types = {
    'controle': 'Contrôle',
    'examen': 'Examen',
    'rattrapage': 'Rattrapage'
  }
  return types[type] || type
}

const getGradeColor = (mark, textOnly = false) => {
  if (mark >= 15) return textOnly ? 'text-green-600 dark:text-green-300' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
  if (mark >= 10) return textOnly ? 'text-blue-600 dark:text-blue-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'
  if (mark >= 8) return textOnly ? 'text-orange-600 dark:text-orange-300' : 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300'
  return textOnly ? 'text-red-600 dark:text-red-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'
}

const getAppreciation = (mark) => {
  if (mark >= 16) return 'Très Bien'
  if (mark >= 14) return 'Bien'
  if (mark >= 12) return 'Assez Bien'
  if (mark >= 10) return 'Passable'
  if (mark >= 8) return 'Insuffisant'
  return 'Faible'
}

onMounted(() => {
  fetchGrades()
})
</script>
