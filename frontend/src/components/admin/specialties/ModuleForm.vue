<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="close"></div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                Add New Module
              </h3>
              <div class="mt-4 space-y-4">
                <!-- Name -->
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                  <input 
                    type="text" 
                    v-model="form.name" 
                    id="name" 
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" 
                    placeholder="Database Management"
                  >
                  <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                </div>

                <!-- Code -->
                <div>
                  <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Code</label>
                  <input 
                    type="text" 
                    v-model="form.code" 
                    id="code" 
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" 
                    placeholder="DB101"
                  >
                  <p v-if="errors.code" class="mt-1 text-sm text-red-600">{{ errors.code }}</p>
                </div>

                <!-- Description -->
                <div>
                  <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                  <textarea 
                    v-model="form.description" 
                    id="description" 
                    rows="3" 
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                  ></textarea>
                </div>

                <!-- Semester -->
                <div>
                  <label for="semester" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Semester</label>
                  <select 
                    v-model="form.semester" 
                    id="semester" 
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option :value="1">Semester 1</option>
                    <option :value="2">Semester 2</option>
                    <option :value="3">Semester 3</option>
                    <option :value="4">Semester 4</option>
                    <option :value="5">Semester 5</option>
                    <option :value="6">Semester 6</option>
                  </select>
                  <p v-if="errors.semester" class="mt-1 text-sm text-red-600">{{ errors.semester }}</p>
                </div>

                <!-- Coefficient -->
                <div>
                  <label for="coefficient" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Coefficient</label>
                  <input 
                    type="number" 
                    step="0.5" 
                    min="0.5" 
                    max="10"
                    v-model="form.coefficient" 
                    id="coefficient" 
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                  >
                  <p v-if="errors.coefficient" class="mt-1 text-sm text-red-600">{{ errors.coefficient }}</p>
                </div>

                <!-- Hours per week -->
                <div>
                  <label for="hours" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hours per Week</label>
                  <input 
                    type="number" 
                    min="1" 
                    max="40"
                    v-model="form.hours_per_week" 
                    id="hours" 
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                  >
                  <p v-if="errors.hours_per_week" class="mt-1 text-sm text-red-600">{{ errors.hours_per_week }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button 
            type="button" 
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed" 
            @click="submit" 
            :disabled="loading"
          >
            {{ loading ? 'Saving...' : 'Save' }}
          </button>
          <button 
            type="button" 
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-500 shadow-sm px-4 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" 
            @click="close"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'

const props = defineProps({
  isOpen: Boolean,
  module: Object,
  specialtyId: {
    type: Number,
    required: true
  }
})

const emit = defineEmits(['close', 'save'])

const isEditing = ref(false)
const loading = ref(false)
const errors = ref({})

const form = reactive({
  name: '',
  code: '',
  description: '',
  specialty_id: props.specialtyId,
  semester: 1,
  coefficient: 1,
  hours_per_week: 3
})

const resetForm = () => {
  form.name = ''
  form.code = ''
  form.description = ''
  form.specialty_id = props.specialtyId
  form.semester = 1
  form.coefficient = 1
  form.hours_per_week = 3
  errors.value = {}
}

watch(() => props.module, (newVal) => {
  if (newVal) {
    isEditing.value = true
    form.name = newVal.name
    form.code = newVal.code
    form.description = newVal.description || ''
    form.specialty_id = newVal.specialty_id || props.specialtyId
    form.semester = newVal.semester
    form.coefficient = newVal.coefficient
    form.hours_per_week = newVal.hours_per_week
  } else {
    isEditing.value = false
    resetForm()
  }
}, { immediate: true })

watch(() => props.specialtyId, (newVal) => {
  form.specialty_id = newVal
})

const validate = () => {
  errors.value = {}
  
  if (!form.name) {
    errors.value.name = 'Name is required'
  }
  
  if (!form.code) {
    errors.value.code = 'Code is required'
  }
  
  if (!form.semester || form.semester < 1 || form.semester > 6) {
    errors.value.semester = 'Semester must be between 1 and 6'
  }
  
  if (!form.coefficient || form.coefficient < 0.5) {
    errors.value.coefficient = 'Coefficient must be at least 0.5'
  }
  
  if (!form.hours_per_week || form.hours_per_week < 1) {
    errors.value.hours_per_week = 'Hours per week must be at least 1'
  }
  
  return Object.keys(errors.value).length === 0
}

const close = () => {
  emit('close')
  resetForm()
}

const submit = async () => {
  if (!validate()) {
    return
  }
  
  loading.value = true
  try {
    const data = { ...form }
    // Convert to numbers
    data.semester = parseInt(data.semester)
    data.coefficient = parseFloat(data.coefficient)
    data.hours_per_week = parseInt(data.hours_per_week)
    data.specialty_id = parseInt(data.specialty_id)
    
    emit('save', data)
  } finally {
    loading.value = false
  }
}
</script>
