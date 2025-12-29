<template>
  <div v-if="loading" class="flex justify-center py-12">
    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
  </div>
  
  <div v-else-if="specialty" class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="flex flex-col md:flex-row">
        <div class="flex-1">
          <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ specialty.name }} ({{ specialty.code }})
              </h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">
                {{ specialty.description }}
              </p>
            </div>
            <div class="flex space-x-3">
              <button @click="openEditModal" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Edit
              </button>
              <button @click="deleteSpecialty" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Delete
              </button>
            </div>
          </div>
          <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
              <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Duration</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ specialty.duration_years }} Years</dd>
              </div>
              <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Current Semester</dt>
                <dd class="mt-1 text-sm text-gray-900">Semester {{ specialty.current_semester }}</dd>
              </div>
              <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Program PDF</dt>
                <dd class="mt-1 text-sm text-gray-900">
                  <div v-if="specialty.program_url" class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                    </svg>
                    <a :href="specialty.program_url" target="_blank" class="font-medium text-indigo-600 hover:text-indigo-500">
                      Download Program PDF
                    </a>
                  </div>
                  <span v-else class="text-gray-500 italic">No PDF uploaded</span>
                </dd>
              </div>
            </dl>
          </div>
        </div>
        <div v-if="specialty.cover_image_url" class="w-full md:w-1/3 bg-gray-100 flex items-center justify-center overflow-hidden border-l border-gray-200">
            <img :src="specialty.cover_image_url" alt="Cover Image" class="object-cover w-full h-full min-h-[200px] max-h-[400px]" />
        </div>
      </div>
    </div>

    <!-- Modules -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Modules</h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">Modules taught in this specialty.</p>
      </div>
      <div class="border-t border-gray-200 p-6">
        <div v-if="modules.length > 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <div v-for="module in modules" :key="module.id" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex flex-col space-y-3 hover:border-indigo-500 hover:shadow-md transition-all">
            <div class="flex-1">
              <div class="flex items-center justify-between space-x-3">
                <h3 class="text-gray-900 text-sm font-medium truncate" :title="module.name">{{ module.name }}</h3>
                <span class="flex-shrink-0 inline-block px-2 py-0.5 text-green-800 text-xs font-medium bg-green-100 rounded-full">
                  S{{ module.semester }}
                </span>
              </div>
              <p class="mt-1 text-gray-500 text-sm truncate">{{ module.code }}</p>
            </div>
            <div class="border-t border-gray-100 pt-2 flex justify-between items-center">
               <span class="text-xs text-gray-500 font-medium">Coefficient: {{ module.coefficient }}</span>
            </div>
          </div>
        </div>
        <div v-else class="text-center text-gray-500 py-4">
          No modules found.
        </div>
      </div>
    </div>

    <!-- Teachers -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Teachers</h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">Teachers assigned to this specialty.</p>
      </div>
      <div class="border-t border-gray-200">
        <ul role="list" class="divide-y divide-gray-200">
          <li v-for="teacher in teachers" :key="teacher.id" class="px-4 py-4 sm:px-6">
            <div class="flex items-center space-x-4">
              <div class="flex-shrink-0">
                <span class="inline-block h-10 w-10 rounded-full overflow-hidden bg-gray-100">
                  <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                </span>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900">{{ teacher.full_name }}</p>
                <p class="text-sm text-gray-500">{{ teacher.specialization }}</p>
              </div>
            </div>
          </li>
          <li v-if="teachers.length === 0" class="px-4 py-4 sm:px-6 text-center text-gray-500">
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
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSpecialtiesStore } from '@/stores/specialties'
import SpecialtyForm from '@/components/admin/specialties/SpecialtyForm.vue'

const route = useRoute()
const router = useRouter()
const store = useSpecialtiesStore()

const isModalOpen = ref(false)

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
</script>
