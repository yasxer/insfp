<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Deliberations</h1>
    </div>

    <!-- Error Alert -->
    <div v-if="error" class="bg-red-50 dark:bg-red-900/50 p-4 rounded-lg flex items-start">
      <p class="text-sm text-red-800 dark:text-red-200">{{ error }}</p>
    </div>

    <div v-if="loading" class="flex justify-center flex-col items-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else>
      <div v-if="deliberations.length === 0" class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No Result Available</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">The administration has not successfully posted your final deliberation yet.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div v-for="deliberation in deliberations" :key="deliberation.id" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Semester {{ deliberation.semester }}</h3>
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ deliberation.academic_year }}</span>
          </div>
          
          <div class="p-6 space-y-4">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
              <span class="text-gray-500 dark:text-gray-400">Final Average</span>
              <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ deliberation.average }} <span class="text-sm font-normal text-gray-500">/ 20</span></span>
            </div>
            
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
              <span class="text-gray-500 dark:text-gray-400">Result Status</span>
              <span :class="['px-3 py-1 rounded-full text-sm font-semibold', deliberation.result === 'passed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400']">
                {{ deliberation.result === 'passed' ? 'Admis' : 'Ajourné' }}
              </span>
            </div>
            
            <div v-if="deliberation.observations" class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
              <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-1">Administration Note:</h4>
              <p class="text-sm text-blue-600 dark:text-blue-400">{{ deliberation.observations }}</p>
            </div>
            
            <div class="text-xs text-gray-400 text-right mt-4">
              Deliberation published on: {{ deliberation.deliberation_date }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import studentApi from '@/api/endpoints/student'

const loading = ref(true)
const error = ref(null)
const deliberations = ref([])

onMounted(async () => {
  try {
    const response = await studentApi.getDeliberations()
    deliberations.value = response.deliberations || []
  } catch (err) {
    console.error('Failed to load deliberations:', err)
    error.value = 'Failed to load your deliberation results.'
  } finally {
    loading.value = false
  }
})
</script>
