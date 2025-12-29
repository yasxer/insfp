<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Specialties Management</h1>
    </div>

    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <!-- Add New Card (Visual Placeholder if needed, but we have the button) -->
      <div @click="openCreateModal" class="col-span-1 bg-white dark:bg-gray-800 rounded-lg shadow border-2 border-dashed border-gray-300 dark:border-gray-700 p-6 flex flex-col items-center justify-center cursor-pointer hover:border-indigo-500 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors min-h-[200px]">
        <div class="h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center mb-4">
          <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Add New Specialty</h3>
      </div>

      <!-- Specialty Cards -->
      <div v-for="specialty in specialties" :key="specialty.id" @click="goToDetails(specialty.id)" class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg cursor-pointer hover:shadow-md transition-shadow relative group">
        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
           <!-- Edit Icon -->
           <button @click.stop="openEditModal(specialty)" class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
             <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
               <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
             </svg>
           </button>
        </div>
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center">
            <div v-if="!specialty.cover_image_url" class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
              <!-- Icon based on name or generic -->
              <span class="text-white font-bold text-xl">{{ specialty.code }}</span>
            </div>
            <div v-else class="flex-shrink-0 h-16 w-16 rounded-md overflow-hidden bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
              <img :src="specialty.cover_image_url" class="h-full w-full object-cover" alt="Cover" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                {{ specialty.name }}
              </dt>
              <dd class="flex items-baseline">
                <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                  {{ specialty.students_count }} Students
                </div>
              </dd>
            </div>
          </div>
          
          <div class="mt-4">
            <span :class="[specialty.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200', 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize']">
              {{ specialty.is_active ? 'Active' : 'Inactive' }}
            </span>
          </div>

          <div class="mt-4 flex justify-between text-sm text-gray-500 dark:text-gray-400">
            <div class="flex items-center">
              <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
              </svg>
              {{ specialty.classes_count }} Classes
            </div>
            <div class="flex items-center">
              <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
              </svg>
              {{ specialty.teachers_count }} Teachers
            </div>
          </div>
        </div>
      </div>
    </div>

    <SpecialtyForm 
      :is-open="isModalOpen" 
      :specialty="selectedSpecialty"
      @close="closeModal"
      @save="handleSave"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useSpecialtiesStore } from '@/stores/specialties'
import { useRouter } from 'vue-router'
import SpecialtyForm from '@/components/admin/specialties/SpecialtyForm.vue'

const store = useSpecialtiesStore()
const router = useRouter()

const isModalOpen = ref(false)
const selectedSpecialty = ref(null)

const specialties = computed(() => store.specialties)
const loading = computed(() => store.loading)

onMounted(() => {
  store.fetchSpecialties()
})

const openCreateModal = () => {
  selectedSpecialty.value = null
  isModalOpen.value = true
}

const openEditModal = (specialty) => {
  selectedSpecialty.value = specialty
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  selectedSpecialty.value = null
}

const handleSave = async (formData) => {
  try {
    if (selectedSpecialty.value) {
      await store.updateSpecialty(selectedSpecialty.value.id, formData)
    } else {
      await store.createSpecialty(formData)
    }
    closeModal()
  } catch (error) {
    console.error('Failed to save specialty:', error)
    // Handle error (show notification)
  }
}

const goToDetails = (id) => {
  router.push(`/admin/specialties/${id}`)
}
</script>
