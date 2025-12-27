<template>
  <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow mb-6">
    <div class="flex flex-wrap gap-4 items-end">
      <!-- Search -->
      <div class="flex-1 min-w-[200px]">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
        <input
          v-model="filters.search"
          type="text"
          placeholder="Name, ID or Email..."
          class="w-full p-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          @input="emitFilters"
        />
      </div>

      <!-- Specialty -->
      <div class="w-48">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Specialty</label>
        <select
          v-model="filters.specialty_id"
          class="w-full rounded-md p-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          @change="emitFilters"
        >
          <option :value="null">All Specialties</option>
          <option v-for="specialty in specialties" :key="specialty.id" :value="specialty.id">
            {{ specialty.name }}
          </option>
        </select>
      </div>

      <!-- Active Students Filters -->
      <template v-if="tab === 'active'">
        <!-- Year/Semester -->
        <div class="w-32">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Semester</label>
          <select
            v-model="filters.semester"
            class="w-full rounded-md p-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            @change="emitFilters"
          >
            <option :value="null">All</option>
            <option v-for="n in 6" :key="n" :value="n">S{{ n }}</option>
          </select>
        </div>

        <!-- Group -->
        <div class="w-32">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Group</label>
          <select
            v-model="filters.group"
            class="w-full rounded-md p-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            @change="emitFilters"
          >
            <option :value="null">All</option>
            <option value="A">Group A</option>
            <option value="B">Group B</option>
            <option value="C">Group C</option>
          </select>
        </div>
      </template>

      <!-- Graduated Students Filters -->
      <template v-else>
        <!-- Graduation Year -->
        <div class="w-32">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Graduation Year</label>
          <input
            v-model="filters.graduation_year"
            type="number"
            placeholder="Year"
            class="w-full rounded-md p-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            @input="emitFilters"
          />
        </div>
      </template>

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
import { ref, onMounted, watch } from 'vue'
import axios from '@/api/axios'

const props = defineProps({
  tab: {
    type: String,
    default: 'active'
  }
})

const emit = defineEmits(['update:filters'])

const filters = ref({
  search: '',
  specialty_id: null,
  semester: null,
  group: null,
  graduation_year: null
})

const specialties = ref([])
let debounceTimeout = null

const fetchSpecialties = async () => {
  try {
    const response = await axios.get('/api/specialties')
    console.log('Specialties response:', response.data)
    specialties.value = response.data.specialties || response.data
  } catch (error) {
    console.error('Failed to fetch specialties', error)
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
    specialty_id: null,
    semester: null,
    group: null,
    graduation_year: null
  }
  emit('update:filters', filters.value)
}

onMounted(() => {
  fetchSpecialties()
})

// Reset filters when tab changes
watch(() => props.tab, () => {
  clearFilters()
})
</script>
