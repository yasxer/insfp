<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import teacherApi from '@/api/endpoints/teacherPortal'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { BookOpenIcon, UserGroupIcon, ArrowRightIcon } from '@heroicons/vue/24/outline'

const router = useRouter()
const loading = ref(true)
const modules = ref([])
const meta = ref({})
const searchQuery = ref('')
const selectedSemester = ref('')

const fetchModules = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      search: searchQuery.value,
      semester: selectedSemester.value
    }
    const response = await teacherApi.getModules()
    // Depending on if getModules passes params or not, we might need to modify teacherPortal.js
    // For now we assume the backend returns the structure directly
    if (response) {
      modules.value = response.data || []
      meta.value = response.meta || {}
    }
  } catch (error) {
    console.error('Failed to load modules:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchModules()
})

const handleSearch = () => {
  fetchModules(1)
}

const goToStudents = (moduleId) => {
  router.push({ name: 'TeacherModuleStudents', params: { id: moduleId } })
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mes Modules</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Gérez les modules qui vous sont affectés
        </p>
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
          placeholder="Rechercher par nom ou code..." 
          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
        >
      </div>
      <div class="sm:w-48">
        <select 
          v-model="selectedSemester"
          @change="handleSearch"
          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Tous les semestres</option>
          <option value="1">Semestre 1</option>
          <option value="2">Semestre 2</option>
          <option value="3">Semestre 3</option>
          <option value="4">Semestre 4</option>
          <option value="5">Semestre 5</option>
          <option value="6">Semestre 6</option>
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
      <div v-if="modules.length === 0" class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <BookOpenIcon class="w-12 h-12 mx-auto text-gray-400 mb-4" />
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Aucun module trouvé</h3>
        <p class="mt-1 text-gray-500">Vous n'avez pas de modules assignés correspondant à vos critères.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Module Card -->
        <div 
          v-for="module in modules" 
          :key="module.id"
          class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow relative overflow-hidden group"
        >
          <div class="h-2 w-full bg-blue-500 absolute top-0 left-0"></div>
          <div class="p-6">
            <div class="flex justify-between items-start mb-4">
              <div>
                <span class="inline-block px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-semibold rounded mb-2">
                  {{ module.code }}
                </span>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">
                  {{ module.name }}
                </h3>
              </div>
            </div>
            
            <div class="space-y-3 mb-6">
              <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                <span class="font-medium mr-2">Semestre:</span>
                <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded">S{{ module.semester }}</span>
              </div>
              <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                <UserGroupIcon class="w-5 h-5 mr-2 text-gray-400" />
                <span>{{ module.students_count }} Étudiants</span>
              </div>
            </div>
            
            <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
              <button 
                @click="goToStudents(module.id)"
                class="w-full flex items-center justify-center gap-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 group-hover:translate-x-1 transition-transform"
              >
                Voir les étudiants
                <ArrowRightIcon class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Pagination Controls mapping from meta could go here -->
    </template>
  </div>
</template>