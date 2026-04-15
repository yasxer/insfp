<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import teacherApi from '@/api/endpoints/teacherPortal'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { 
  AcademicCapIcon, 
  PencilSquareIcon, 
  CheckBadgeIcon, 
  ClipboardDocumentCheckIcon,
  PlusIcon,
  XMarkIcon,
  PaperAirplaneIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const loading = ref(true)
const exams = ref([])
const modules = ref([])
const meta = ref({})
const searchQuery = ref('')
const selectedType = ref('')

// Modal state
const isModalOpen = ref(false)
const selectedSpecialty = ref('')
const selectedSemester = ref('')

const uniqueSpecialties = computed(() => {
  const specs = []
  const specMap = new Map()
  modules.value.forEach(m => {
    if (m.specialty_id && !specMap.has(m.specialty_id)) {
      specMap.set(m.specialty_id, true)
      specs.push({ id: m.specialty_id, name: m.specialty_name || 'Spécialité inconnue' })
    }
  })
  return specs
})

const availableSemesters = computed(() => {
  if (!selectedSpecialty.value) return []
  const sems = new Set()
  modules.value.forEach(m => {
    if (m.specialty_id === selectedSpecialty.value && m.semester) {
      sems.add(m.semester)
    }
  })
  return Array.from(sems).sort()
})

const filteredModules = computed(() => {
  if (!selectedSemester.value) return []
  return modules.value.filter(m => m.specialty_id === selectedSpecialty.value && m.semester === selectedSemester.value)
})

watch(selectedSpecialty, () => {
  selectedSemester.value = ''
  form.value.module_id = ''
})

watch(selectedSemester, () => {
  form.value.module_id = ''
})

const isSubmitting = ref(false)
const form = ref({
  title: '',
  exam_type: 'midterm',
  module_id: '',
  group: '',
  exam_date: '',
  duration_minutes: 90
})

const fetchExams = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      search: searchQuery.value,
      type: selectedType.value
    }
    const response = await teacherApi.getExams(params)
    if (response) {
      exams.value = response.data || []
      meta.value = response.meta || {}
    }
  } catch (error) {
    console.error('Failed to load exams:', error)
  } finally {
    loading.value = false
  }
}

const fetchModules = async () => {
  try {
    const response = await teacherApi.getModules()
    modules.value = response.data || response || []
  } catch (error) {
    console.error('Failed to load modules:', error)
  }
}

onMounted(() => {
  fetchExams()
  fetchModules()
})

const handleSearch = () => {
  fetchExams(1)
}

const goToGrading = (examId) => {
  router.push({ name: 'TeacherGrading', params: { id: examId } })
}

const formatType = (type) => {
  const types = {
    'midterm': 'Examen partiel',
    'final': 'Examen final',
    'rattrapage': 'Rattrapage',
    'exam': 'Examen',
    'control': 'Contrôle',
    'test': 'Contrôle',
    'project': 'Projet',
    'assignment': 'Devoir',
    'presentation': 'Présentation'
  }
  return types[type] || type
}

const getStatusConfig = (status) => {
  const configs = {
    'draft': { label: 'Brouillon', class: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' },
    'submitted': { label: 'Soumis', class: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' },
    'modified': { label: 'Modifié', class: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300' }
  }
  return configs[status] || { label: status || 'N/A', class: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }
}

const openModal = () => {
  form.value = {
    title: '',
    exam_type: 'exam',
    module_id: '',
    group: '',
    exam_date: '',
    duration_minutes: 90
  }
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

const submitExam = async () => {
  isSubmitting.value = true
  try {
    await teacherApi.storeExam(form.value)
    closeModal()
    fetchExams()
  } catch (error) {
    console.error('Failed to create exam:', error)
    alert('Erreur lors de la création de l\'examen')
  } finally {
    isSubmitting.value = false
  }
}

const updateExamStatus = async (examId, newStatus) => {
  try {
    await teacherApi.updateExamStatus(examId, newStatus)
    fetchExams()
  } catch (error) {
    console.error('Failed to update status:', error)
    alert('Erreur lors de la soumission de l\'examen')
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Examens & Notes</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Gérez les évaluations de vos modules
        </p>
      </div>
      <div>
        <button
          @click="openModal"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm"
        >
          <PlusIcon class="w-5 h-5 mr-2" />
          Créer Examen
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row gap-4">
      <div class="flex-1">
        <label for="search" class="sr-only">Rechercher</label>
        <input
          id="search"
          v-model="searchQuery"
          @keyup.enter="handleSearch"
          type="text"
          placeholder="Rechercher par titre ou module..."
          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
        >
      </div>
      <div class="sm:w-48">
        <select
          v-model="selectedType"
          @change="handleSearch"
          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Tous les types</option>
          <option value="exam">Examen</option>
          <option value="control">Contrôle</option>
          <option value="project">Projet</option>
          <option value="assignment">Devoir</option>
        </select>
      </div>
      <button
        @click="handleSearch"
        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
      >
        Filtrer
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-12">
      <LoadingSpinner size="lg" />
    </div>

    <!-- Content -->
    <template v-else>
      <div v-if="exams.length === 0" class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <AcademicCapIcon class="w-12 h-12 mx-auto text-gray-400 mb-4" />        
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Aucun examen trouvé</h3>
        <p class="mt-1 text-gray-500">Vous n'avez pas d'évaluations planifiées pour vos modules.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"> 
        <!-- Exam Card -->
        <div
          v-for="exam in exams"
          :key="exam.id"
          class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow relative overflow-hidden group flex flex-col"
        >
          <div class="h-1.5 w-full bg-indigo-500 absolute top-0 left-0"></div>  

          <div class="p-6 flex-1">
            <div class="flex justify-between items-start mb-3">
              <span class="inline-block px-2.5 py-1 bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300 text-xs font-bold rounded-full capitalize">
                {{ formatType(exam.exam_type || exam.type) }}
              </span>
              <span 
                v-if="exam.status"
                class="inline-block px-2.5 py-1 text-xs font-bold rounded-full capitalize"
                :class="getStatusConfig(exam.status).class"
              >
                {{ getStatusConfig(exam.status).label }}
              </span>
            </div>

            <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight mb-2 truncate" :title="exam.title">
              {{ exam.title }}
            </h3>

            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg mb-4 text-sm">
              <p class="font-medium text-gray-900 dark:text-white truncate">{{ exam.module?.name || 'Module inconnu' }}</p>
              <div class="flex justify-between items-center mt-1">
                <span class="text-gray-500 dark:text-gray-400">Groupe: {{ exam.group || 'N/A' }}</span>
                <span class="text-gray-500 dark:text-gray-400">{{ exam.duration_minutes || 90 }} min</span>
              </div>
            </div>

            <div class="flex items-center justify-between text-sm">
              <div class="text-gray-600 dark:text-gray-400">
                <span class="font-medium">Date:</span> {{ exam.exam_date ? new Date(exam.exam_date).toLocaleDateString('fr-FR') : (exam.date ? new Date(exam.date).toLocaleDateString('fr-FR') : 'N/A') }}
              </div>
            </div>
          </div>

          <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 space-y-2">
            <button
              @click="goToGrading(exam.id)"
              class="w-full flex items-center justify-center gap-2 text-sm font-medium transition-colors rounded-lg py-2"
              :class="'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600'"
            >
              <PencilSquareIcon class="w-4 h-4" />
              Saisir / Modifier les notes
            </button>
            <button
              v-if="exam.status === 'draft' || exam.status === 'modified'"
              @click="updateExamStatus(exam.id, 'submitted')"
              class="w-full flex items-center justify-center gap-2 text-sm font-medium transition-colors rounded-lg py-2 text-white bg-green-600 hover:bg-green-700"
            >
              <PaperAirplaneIcon class="w-4 h-4" />
              Soumettre à l'administration
            </button>
          </div>
        </div>
      </div>
    </template>

    <!-- Create Exam Modal -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" @click="closeModal"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div class="absolute top-0 right-0 pt-4 pr-4">
            <button @click="closeModal" type="button" class="text-gray-400 bg-white dark:bg-gray-800 rounded-md hover:text-gray-500 focus:outline-none">
              <span class="sr-only">Close</span>
              <XMarkIcon class="w-6 h-6" aria-hidden="true" />
            </button>
          </div>
          <div class="sm:flex sm:items-start">
            <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
              <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white" id="modal-title">
                Créer un Examen
              </h3>
              <div class="mt-6">
                <form @submit.prevent="submitExam" class="space-y-4">
                  <!-- Title -->
                  <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Titre</label>
                    <input type="text" id="title" v-model="form.title" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  </div>
                  
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Spécialité -->
                    <div>
                      <label for="specialty" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Spécialité</label>
                      <select id="specialty" v-model="selectedSpecialty" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="" disabled>Sélectionner une spécialité</option>
                        <option v-for="spec in uniqueSpecialties" :key="spec.id" :value="spec.id">{{ spec.name }}</option>
                      </select>
                    </div>

                    <!-- Semestre -->
                    <div>
                      <label for="semester" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Semestre</label>
                      <select id="semester" v-model="selectedSemester" :disabled="!selectedSpecialty" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="" disabled>Sélectionner un semestre</option>
                        <option v-for="sem in availableSemesters" :key="sem" :value="sem">Semestre {{ sem }}</option>
                      </select>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Module -->
                    <div>
                      <label for="module_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Module</label>
                      <select id="module_id" v-model="form.module_id" :disabled="!selectedSemester" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="" disabled>Sélectionner un module</option>
                        <option v-for="mod in filteredModules" :key="mod.id" :value="mod.id">{{ mod.name }}</option>
                      </select>
                    </div>
                    
                    <!-- Type -->
                    <div>
                      <label for="exam_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type d'examen</label>
                      <select id="exam_type" v-model="form.exam_type" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="midterm">Partiel (Midterm)</option>
                        <option value="final">Final</option>
                        <option value="rattrapage">Rattrapage</option>
                      </select>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Group -->
                    <div>
                      <label for="group" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Groupe</label>
                      <input type="text" id="group" v-model="form.group" required placeholder="Ex: G1" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <!-- Duration -->
                    <div>
                      <label for="duration_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Durée (minutes)</label>
                      <input type="number" id="duration_minutes" v-model="form.duration_minutes" required min="1" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                  </div>

                  <!-- Date -->
                  <div>
                    <label for="exam_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de l'examen</label>
                    <input type="date" id="exam_date" v-model="form.exam_date" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  </div>

                  <!-- Actions -->
                  <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="submit" :disabled="isSubmitting" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                      {{ isSubmitting ? 'Création...' : 'Créer' }}
                    </button>
                    <button type="button" @click="closeModal" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                      Annuler
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

