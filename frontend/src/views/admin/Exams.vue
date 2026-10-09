<template>
  <div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center py-6 gap-4">
      <div>
        <h1 class="text-3xl font-extrabold text-blue-900 tracking-tight dark:text-blue-300">{{ t('admin.exams.examens') }}</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
          {{ t('admin.exams.gerez_ensemble_examens_suivez_leur') }}
        </p>
      </div>
    </div>

    <!-- Error Alert -->
    <div v-if="error" class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm dark:bg-red-900/30">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-red-700 dark:text-red-300">{{ error }}</p>
        </div>
      </div>
    </div>

    <!-- Exams List -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>

      <div v-else-if="!loading && (!exams || exams.data.length === 0)" class="text-center py-24 bg-gray-50 bg-opacity-50 dark:bg-gray-800/60">
        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">{{ t('admin.exams.aucun_examen_trouve') }}</h3>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
          {{ t('admin.exams.enseignants_n_ont_pas_encore') }}
        </p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-800/60">
            <tr>
              <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                {{ t('common.exam') }}
              </th>
              <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                {{ t('admin.exams.date_heure') }}
              </th>
              <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                {{ t('common.teacher') }}
              </th>
              <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                {{ t('admin.exams.module_groupe') }}
              </th>
              <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                {{ t('common.status') }}
              </th>
              <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                {{ t('common.actions') }}
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-800">
            <tr v-for="exam in exams.data" :key="exam.id" class="hover:bg-gray-50 transition-colors duration-200 dark:hover:bg-gray-800">
              <td class="px-6 py-4">
                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ exam.title }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ getExamLabel(exam.exam_type || exam.type) }}</div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-gray-900 font-medium dark:text-white">{{ formatDate(exam.exam_date) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  <span dir="ltr">{{ timeRange(exam) }}</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-gray-900 dark:text-white" v-if="exam.teacher">
                  {{ exam.teacher.last_name }} {{ exam.teacher.first_name }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400" v-else>
                  -
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900 dark:text-white" v-if="exam.module">{{ exam.module.name }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400" v-else>-</div>
                <div class="text-sm font-medium text-indigo-600 mt-1 dark:text-indigo-300">
                 {{ exam.group_name }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                      :class="getStatusBadgeClass(exam.status)">
                  {{ getStatusLabel(exam.status) }}
                </span>
                <div class="text-xs text-gray-500 mt-1 pl-1 dark:text-gray-400" v-if="exam.grades_count !== undefined">
                  {{ t('admin.exams.notes', { p0: exam.grades_count }) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <router-link :to="{ name: 'AdminExamGrades', params: { id: exam.id } }" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md transition-colors dark:text-blue-300 dark:bg-blue-900/30">
                  {{ t('admin.exams.voir_resultats') }}
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>

         <!-- Pagination Controls -->
         <div v-if="exams.last_page > 1" class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 dark:bg-gray-800/60 dark:border-gray-700">
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700 dark:text-gray-200">
                {{ t('admin.exams.affichage') }} <span class="font-medium">{{ exams.from || 0 }}</span> à <span class="font-medium">{{ exams.to || 0 }}</span> {{ t('admin.exams.sur') }} <span class="font-medium">{{ exams.total }}</span> {{ t('admin.exams.resultats') }}
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" :aria-label="t('admin.exams.pagination')">
                <button 
                  @click="fetchExams(exams.current_page - 1)" 
                  :disabled="exams.current_page === 1"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:border-gray-600 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800">
                  <span class="sr-only">{{ t('common.previous') }}</span>
                  <!-- Heroicon name: solid/chevron-left -->
                  <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>
                
                <template v-for="page in getPageNumbers(exams.current_page, exams.last_page)" :key="page">
                  <button v-if="page !== '...'"
                    @click="fetchExams(page)"
                    :class="[
                      page === exams.current_page 
                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300' 
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-800',
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                    ]">
                    {{ page }}
                  </button>
                  <span v-else class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200">
                    ...
                  </span>
                </template>
                
                <button 
                  @click="fetchExams(exams.current_page + 1)" 
                  :disabled="exams.current_page === exams.last_page"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:border-gray-600 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800">
                  <span class="sr-only">{{ t('common.next') }}</span>
                  <!-- Heroicon name: solid/chevron-right -->
                  <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>
              </nav>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
import { ref, onMounted } from 'vue'
import adminApi from '../../api/endpoints/admin'
import { format } from 'date-fns'
import { fr } from 'date-fns/locale'

const exams = ref({ data: [], current_page: 1, last_page: 1 })
const loading = ref(true)
const error = ref('')

const fetchExams = async (page = 1) => {
  try {
    loading.value = true
    error.value = ''
    const response = await adminApi.getExams({ page })
    exams.value = response
  } catch (err) {
    console.error('Error fetching exams:', err)
    error.value = t('admin.exams.impossible_charger_liste_examens_veuille')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchExams()
})

// Utilities
const formatDate = (dateString) => {
  if (!dateString) return '-'
  return format(new Date(dateString), 'dd MMMM yyyy', { locale: fr })
}

// exam_date holds the start; the end follows from the duration
const timeRange = (exam) => {
  if (!exam.exam_date) return ''
  const start = new Date(exam.exam_date)
  const end = new Date(start.getTime() + (exam.duration_minutes || 0) * 60000)
  const hm = (d) => format(d, 'HH:mm')
  return exam.duration_minutes ? `${hm(start)} - ${hm(end)}` : hm(start)
}

const getExamLabel = (type) => {
  const types = {
    'controle': 'Contrôle',
    'examen': 'Examen',
    'rattrapage': 'Rattrapage'
  }
  return types[type] || type
}

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'draft': return 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
    case 'submitted': return 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300'
    case 'modified': return 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300'
    case 'scheduled': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'
    case 'in_progress': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'
    case 'completed': return 'bg-cyan-100 text-cyan-800'
    case 'grading': return 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300'
    case 'graded': return 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300'
    case 'published': return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
    case 'cancelled': return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'
    default: return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100'
  }
}

const getStatusLabel = (status) => {
  const known = ['draft', 'submitted', 'modified', 'scheduled', 'in_progress', 'completed', 'grading', 'graded', 'published', 'cancelled']
  return known.includes(status) ? t('labels.examStatus.' + status) : status
}

const getPageNumbers = (current, last) => {
  // Simple pagination logic for generating page numbers
  if (last <= 7) {
    return Array.from({ length: last }, (_, i) => i + 1)
  }
  
  if (current <= 3) {
    return [1, 2, 3, 4, '...', last]
  }
  
  if (current >= last - 2) {
    return [1, '...', last - 3, last - 2, last - 1, last]
  }
  
  return [1, '...', current - 1, current, current + 1, '...', last]
}
</script>