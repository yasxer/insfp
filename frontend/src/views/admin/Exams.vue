<template>
  <div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center py-6 gap-4">
      <div>
        <h1 class="text-3xl font-extrabold text-blue-900 tracking-tight">Examens</h1>
        <p class="mt-2 text-sm text-gray-600">
          Gérez l'ensemble des examens et suivez leur statut
        </p>
      </div>
    </div>

    <!-- Error Alert -->
    <div v-if="error" class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-red-700">{{ error }}</p>
        </div>
      </div>
    </div>

    <!-- Exams List -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>

      <div v-else-if="!loading && (!exams || exams.data.length === 0)" class="text-center py-24 bg-gray-50 bg-opacity-50">
        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun examen trouvé</h3>
        <p class="mt-2 text-sm text-gray-500">
          Les enseignants n'ont pas encore commencé la procédure d'examen.
        </p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Examen
              </th>
              <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Date & Heure
              </th>
              <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Enseignant
              </th>
              <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Module & Groupe
              </th>
              <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Statut
              </th>
              <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="exam in exams.data" :key="exam.id" class="hover:bg-gray-50 transition-colors duration-200">
              <td class="px-6 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ exam.title }}</div>
                <div class="text-sm text-gray-500">{{ getExamLabel(exam.type) }}</div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-gray-900 font-medium">{{ formatDate(exam.exam_date) }}</div>
                <div class="text-sm text-gray-500">
                  {{ formatTime(exam.start_time) }} - {{ formatTime(exam.end_time) }}
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-gray-900" v-if="exam.teacher">
                  {{ exam.teacher.last_name }} {{ exam.teacher.first_name }}
                </div>
                <div class="text-sm text-gray-500" v-else>
                  -
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900" v-if="exam.module">{{ exam.module.name }}</div>
                <div class="text-sm text-gray-500" v-else>-</div>
                <div class="text-sm font-medium text-indigo-600 mt-1">
                 {{ exam.group_name }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                      :class="getStatusBadgeClass(exam.status)">
                  {{ getStatusLabel(exam.status) }}
                </span>
                <div class="text-xs text-gray-500 mt-1 pl-1" v-if="exam.grades_count !== undefined">
                  {{ exam.grades_count }} notes
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <router-link :to="{ name: 'AdminExamGrades', params: { id: exam.id } }" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md transition-colors">
                  Voir résultats
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>

         <!-- Pagination Controls -->
         <div v-if="exams.last_page > 1" class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Affichage de <span class="font-medium">{{ exams.from || 0 }}</span> à <span class="font-medium">{{ exams.to || 0 }}</span> sur <span class="font-medium">{{ exams.total }}</span> résultats
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <button 
                  @click="fetchExams(exams.current_page - 1)" 
                  :disabled="exams.current_page === 1"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                  <span class="sr-only">Précédent</span>
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
                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' 
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                    ]">
                    {{ page }}
                  </button>
                  <span v-else class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                    ...
                  </span>
                </template>
                
                <button 
                  @click="fetchExams(exams.current_page + 1)" 
                  :disabled="exams.current_page === exams.last_page"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                  <span class="sr-only">Suivant</span>
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
    error.value = 'Impossible de charger la liste des examens. Veuillez réessayer.'
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

const formatTime = (timeString) => {
  if (!timeString) return ''
  // Handle "HH:mm:ss" format
  return timeString.substring(0, 5)
}

const getExamLabel = (type) => {
  const types = {
    'continuous': 'Contrôle continu',
    'midterm': 'Examen partiel',
    'final': 'Examen final',
    'makeup': 'Rattrapage',
    'practical': 'Examen pratique'
  }
  return types[type] || type
}

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'scheduled': return 'bg-yellow-100 text-yellow-800'
    case 'in_progress': return 'bg-blue-100 text-blue-800'
    case 'completed': return 'bg-cyan-100 text-cyan-800'
    case 'grading': return 'bg-purple-100 text-purple-800'
    case 'graded': return 'bg-indigo-100 text-indigo-800'
    case 'published': return 'bg-green-100 text-green-800'
    case 'cancelled': return 'bg-red-100 text-red-800'
    default: return 'bg-gray-100 text-gray-800'
  }
}

const getStatusLabel = (status) => {
  switch (status) {
    case 'scheduled': return 'Programmé'
    case 'in_progress': return 'En cours'
    case 'completed': return 'Terminé'
    case 'grading': return 'En correction'
    case 'graded': return 'Corrigé'
    case 'published': return 'Publié'
    case 'cancelled': return 'Annulé'
    default: return status
  }
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