<template>
  <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow mb-6">
    <div class="flex flex-wrap gap-4 items-end">
      <!-- Search -->
      <div class="flex-1 min-w-[200px]">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
        <input
          v-model="filters.search"
          type="text"
          placeholder="Name, Email or Phone..."
          class="w-full p-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          @input="emitFilters"
        />
      </div>

      <!-- Specialization -->
      <div class="w-48">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Specialization</label>
        <select
          v-model="filters.specialization"
          class="w-full rounded-md p-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          @change="emitFilters"
        >
          <option :value="null">All Specializations</option>
          <option v-for="spec in specializations" :key="spec" :value="spec">
            {{ spec }}
          </option>
        </select>
      </div>

      <!-- Status -->
      <div class="w-32">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
        <select
          v-model="filters.approved"
          class="w-full rounded-md p-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          @change="emitFilters"
        >
          <option :value="null">All</option>
          <option :value="true">Active</option>
          <option :value="false">Pending</option>
        </select>
      </div>

      <!-- Clear Filters -->
      <button
        @click="clearFilters"
        class="px-4 py-2 bg-gray-100 p-2 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
      >
        Clear
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '@/api/axios'

const emit = defineEmits(['update:filters'])

const filters = ref({
  search: '',
  specialization: null,
  approved: null
})

const specializations = ref([])
let debounceTimeout = null

const fetchSpecializations = async () => {
  try {
    const response = await axios.get('/api/admin/teachers')
    // Extract unique specializations from teachers
    const teachers = response.data.teachers || []
    const uniqueSpecs = [...new Set(teachers.map(t => t.specialization).filter(Boolean))]
    specializations.value = uniqueSpecs.sort()
  } catch (error) {
    console.error('Failed to fetch specializations', error)
  }
}

const emitFilters = () => {
  if (debounceTimeout) clearTimeout(debounceTimeout)
  
  debounceTimeout = setTimeout(() => {
    emit('update:filters', filters.value)
  }, 300)
}

const clearFilters = () => {
  filters.value = {
    search: '',
    specialization: null,
    approved: null
  }
  emit('update:filters', filters.value)
}

onMounted(() => {
  fetchSpecializations()
})
</script>
