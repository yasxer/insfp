<template>
  <div v-if="loading" class="flex justify-center py-12">
    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
  </div>
  
  <div v-else-if="specialty" class="space-y-6">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
      <div class="flex flex-col md:flex-row">
        <div class="flex-1">
          <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                {{ specialty.name }} ({{ specialty.code }})
              </h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                {{ specialty.description }}
              </p>
            </div>
            <div class="flex space-x-3">
              <button @click="openEditModal" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Edit
              </button>
              <button @click="deleteSpecialty" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Delete
              </button>
            </div>
          </div>
          <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-5 sm:px-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
              <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ specialty.duration_years }} Years</dd>
              </div>
              <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Semester</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">Semester {{ specialty.current_semester }}</dd>
              </div>
              <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Program PDF</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  <div v-if="specialty.program_url" class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-400 dark:text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                    </svg>
                    <a :href="specialty.program_url" target="_blank" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                      Download Program PDF
                    </a>
                  </div>
                  <span v-else class="text-gray-500 dark:text-gray-400 italic">No PDF uploaded</span>
                </dd>
              </div>
            </dl>
          </div>
        </div>
        <div v-if="specialty.cover_image_url" class="w-full md:w-1/3 bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border-l border-gray-200 dark:border-gray-700">
            <img :src="specialty.cover_image_url" alt="Cover Image" class="object-cover w-full h-full min-h-[200px] max-h-[400px]" />
        </div>
      </div>
    </div>

    <!-- Modules -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
      <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
          <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Modules</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Modules taught in this specialty.</p>
        </div>
        <button 
          @click="openAddModuleModal" 
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Add Module
        </button>
      </div>
      <div class="border-t border-gray-200 dark:border-gray-700 p-6">
        <div v-if="modules.length > 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <div v-for="module in modules" :key="module.id" class="relative rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-6 py-5 shadow-sm flex flex-col space-y-3 hover:border-indigo-500 hover:shadow-md transition-all group">
            <div class="flex-1">
              <div class="flex items-center justify-between space-x-3">
                <h3 class="text-gray-900 dark:text-white text-sm font-medium truncate" :title="module.name">{{ module.name }}</h3>
                <span class="flex-shrink-0 inline-block px-2 py-0.5 text-green-800 dark:text-green-200 text-xs font-medium bg-green-100 dark:bg-green-900 rounded-full">
                  S{{ module.semester }}
                </span>
              </div>
              <p class="mt-1 text-gray-500 dark:text-gray-300 text-sm truncate">{{ module.code }}</p>
            </div>
            <div class="border-t border-gray-100 dark:border-gray-600 pt-2 flex justify-between items-center">
               <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Coefficient: {{ module.coefficient }}</span>
               <button 
                 @click="deleteModule(module)" 
                 class="opacity-0 group-hover:opacity-100 transition-opacity text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                 title="Delete"
               >
                 <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                   <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                 </svg>
               </button>
            </div>
          </div>
        </div>
        <div v-else class="text-center text-gray-500 dark:text-gray-400 py-4">
          No modules found.
        </div>
      </div>
    </div>

    <!-- Teachers -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
      <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Teachers</h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Teachers assigned to this specialty.</p>
      </div>
      <div class="border-t border-gray-200 dark:border-gray-700">
        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
          <li v-for="teacher in teachers" :key="teacher.id" class="px-4 py-4 sm:px-6">
            <div class="flex items-center space-x-4">
              <div class="flex-shrink-0">
                <span class="inline-block h-10 w-10 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700">
                  <svg class="h-full w-full text-gray-300 dark:text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                </span>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ teacher.full_name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ teacher.specialization }}</p>
              </div>
            </div>
          </li>
          <li v-if="teachers.length === 0" class="px-4 py-4 sm:px-6 text-center text-gray-500 dark:text-gray-400">
            No teachers found.
          </li>
        </ul>
      </div>
    </div>

    <SpecialtyForm 
      :is-open="isModalOpen" 
      :specialty="specialty"
      @close="closeModal"
      @save="handleSave"
    />

    <ModuleForm 
      :is-open="isModuleModalOpen" 
      :module="selectedModule"
      :specialty-id="parseInt(route.params.id)"
      @close="closeModuleModal"
      @save="handleModuleSave"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSpecialtiesStore } from '@/stores/specialties'
import SpecialtyForm from '@/components/admin/specialties/SpecialtyForm.vue'
import ModuleForm from '@/components/admin/specialties/ModuleForm.vue'
import modulesApi from '@/api/endpoints/modules'

const route = useRoute()
const router = useRouter()
const store = useSpecialtiesStore()

const isModalOpen = ref(false)
const isModuleModalOpen = ref(false)
const selectedModule = ref(null)

const specialty = computed(() => store.currentSpecialty)
const loading = computed(() => store.loading)
// Note: The API response structure for 'show' returns { specialty: { ..., modules: [], teachers: [] } }
// So modules and teachers are inside specialty object.
const modules = computed(() => store.currentSpecialty?.modules || [])
const teachers = computed(() => store.currentSpecialty?.teachers || [])

onMounted(() => {
  store.fetchSpecialty(route.params.id)
})

const openEditModal = () => {
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

const handleSave = async (formData) => {
  try {
    await store.updateSpecialty(specialty.value.id, formData)
    closeModal()
  } catch (error) {
    console.error('Failed to update specialty:', error)
  }
}

const deleteSpecialty = async () => {
  if (confirm('Are you sure you want to delete this specialty? This action cannot be undone.')) {
    try {
      await store.deleteSpecialty(specialty.value.id)
      router.push('/admin/specialties')
    } catch (error) {
      console.error('Failed to delete specialty:', error)
    }
  }
}

// Module Functions
const openAddModuleModal = () => {
  selectedModule.value = null
  isModuleModalOpen.value = true
}

const closeModuleModal = () => {
  isModuleModalOpen.value = false
  selectedModule.value = null
}

const handleModuleSave = async (moduleData) => {
  try {
    // Create new module only
    await modulesApi.createModule(moduleData)
    
    // Refresh specialty data to get updated modules
    await store.fetchSpecialty(route.params.id)
    closeModuleModal()
  } catch (error) {
    console.error('Failed to save module:', error)
    alert(error.response?.data?.message || 'Failed to save module')
  }
}

const deleteModule = async (module) => {
  if (confirm(`Are you sure you want to delete ${module.name}?`)) {
    try {
      await modulesApi.deleteModule(module.id)
      // Refresh specialty data
      await store.fetchSpecialty(route.params.id)
    } catch (error) {
      console.error('Failed to delete module:', error)
      alert(error.response?.data?.message || 'Failed to delete module')
    }
  }
}
</script>
