<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="close"></div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                Add Schedule
              </h3>
              
              <!-- Time Display -->
              <div class="mt-3 bg-indigo-50 dark:bg-indigo-900 rounded-md p-3">
                <p class="text-sm text-indigo-700 dark:text-indigo-300">
                  <strong>Day:</strong> {{ dayName }} &nbsp;|&nbsp;
                  <strong>Time:</strong> {{ startTime }} - {{ endTime }}
                </p>
              </div>

              <!-- Step 1: Select Specialty-Semester -->
              <div v-if="!selectedSpecialtySemester" class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Select Specialty & Semester
                </label>
                
                <!-- Loading -->
                <div v-if="loadingSpecialties" class="text-center py-4">
                  <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
                  <p class="text-sm text-gray-500 mt-2">Loading...</p>
                </div>

                <!-- Empty State -->
                <div v-else-if="specialtySemesters.length === 0" class="text-center py-8 bg-gray-50 dark:bg-gray-700 rounded-lg">
                  <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No specialties available</p>
                </div>

                <!-- Specialty-Semester Table -->
                <div v-else class="overflow-x-auto max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg">
                  <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                      <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                          Specialty
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                          Mode
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                          Semester
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                          Students
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                          Groups
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                      <tr 
                        v-for="item in specialtySemesters" 
                        :key="item.id"
                        @click="selectSpecialtySemester(item)"
                        class="hover:bg-indigo-50 dark:hover:bg-indigo-900 cursor-pointer transition-colors"
                      >
                        <td class="px-4 py-3 whitespace-nowrap">
                          <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ item.specialty_name }}
                          </div>
                          <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ item.specialty_code }}
                          </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                          <span :class="getStudyModeBadgeClass(item.study_mode)" class="px-2 py-1 text-xs font-medium rounded-full">
                            {{ getStudyModeLabel(item.study_mode) }}
                          </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                          <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-200 font-bold text-sm">
                            S{{ item.semester }}
                          </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                          <span class="text-sm text-gray-900 dark:text-white font-medium">
                            {{ item.students_count }}
                          </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                          <span class="text-sm text-gray-600 dark:text-gray-300">
                            {{ item.groups_count || '-' }}
                          </span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Step 2: Select Module & Teacher -->
              <div v-else class="mt-4 space-y-4">
                <!-- Selected Specialty Info -->
                <div class="bg-green-50 dark:bg-green-900 rounded-md p-3 flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">
                      {{ selectedSpecialtySemester.specialty_name }} - Semester {{ selectedSpecialtySemester.semester }}
                    </p>
                    <p class="text-xs text-green-600 dark:text-green-400">
                      {{ getStudyModeLabel(selectedSpecialtySemester.study_mode) }} | {{ selectedSpecialtySemester.students_count }} students
                    </p>
                  </div>
                  <button 
                    @click="clearSelection" 
                    class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>

                <!-- Module Selection -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Select Module
                  </label>
                  
                  <div v-if="loadingModules" class="text-center py-4">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600 mx-auto"></div>
                  </div>
                  
                  <div v-else-if="modules.length === 0" class="text-sm text-gray-500 dark:text-gray-400 italic p-3 bg-gray-50 dark:bg-gray-700 rounded">
                    No modules available for this semester
                  </div>

                  <div v-else class="space-y-2 max-h-40 overflow-y-auto">
                    <label 
                      v-for="module in modules" 
                      :key="module.id"
                      class="flex items-center p-3 border rounded-md cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                      :class="form.module_id === module.id ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900' : 'border-gray-300 dark:border-gray-600'"
                    >
                      <input
                        type="radio"
                        :value="module.id"
                        v-model="form.module_id"
                        @change="onModuleChange"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      >
                      <span class="mr-3 text-sm text-gray-900 dark:text-white">
                        {{ module.name }} 
                        <span class="text-gray-500">({{ module.code }})</span>
                      </span>
                    </label>
                  </div>
                  <p v-if="errors.module_id" class="mt-1 text-sm text-red-600">{{ errors.module_id }}</p>
                </div>

                <!-- Group Selection -->
                <div v-if="form.module_id">
                  <label for="group" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Group (Optional)
                  </label>
                  <select 
                    v-model="form.group" 
                    id="group" 
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option value="">All Groups</option>
                    <option v-for="group in availableGroups" :key="group" :value="group">
                      Group {{ group }}
                    </option>
                  </select>
                </div>

                <!-- Teacher Selection -->
                <div v-if="form.module_id">
                  <label for="teacher" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Teacher
                  </label>
                  
                  <div v-if="loadingTeachers" class="text-center py-2">
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-indigo-600 mx-auto"></div>
                  </div>
                  
                  <select 
                    v-else
                    v-model="form.teacher_id" 
                    id="teacher" 
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option value="">Select Teacher</option>
                    <option v-for="teacher in moduleTeachers" :key="teacher.id" :value="teacher.id">
                      {{ teacher.full_name }}
                    </option>
                  </select>
                  <p v-if="errors.teacher_id" class="mt-1 text-sm text-red-600">{{ errors.teacher_id }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button 
            v-if="selectedSpecialtySemester"
            type="button" 
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed" 
            @click="submit" 
            :disabled="loading || !canSubmit"
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
import { ref, reactive, watch, computed } from 'vue'
import axios from '@/api/axios'
import schedulesApi from '@/api/endpoints/schedules'

// Props
const props = defineProps({
  isOpen: Boolean,
  day: String,
  startTime: String,
  endTime: String,
  academicYear: String
})

// Emits
const emit = defineEmits(['close', 'save'])

// State
const loading = ref(false)
const loadingSpecialties = ref(false)
const loadingModules = ref(false)
const loadingTeachers = ref(false)
const errors = ref({})

// Data arrays
const specialtySemesters = ref([])
const selectedSpecialtySemester = ref(null)
const modules = ref([])
const moduleTeachers = ref([])
const availableGroups = ref([])

// Form data
const form = reactive({
  specialty_id: '',
  study_mode: '',
  semester: '',
  module_id: '',
  teacher_id: '',
  group: '',
  day: props.day,
  start_time: props.startTime,
  end_time: props.endTime,
  academic_year: props.academicYear
})

// Computed: Can submit form
const canSubmit = computed(() => {
  return form.module_id && form.teacher_id
})

// Computed: Day name
const dayNames = {
  'saturday': 'Saturday',
  'sunday': 'Sunday',
  'monday': 'Monday',
  'tuesday': 'Tuesday',
  'wednesday': 'Wednesday',
  'thursday': 'Thursday'
}

const dayName = computed(() => dayNames[props.day] || props.day)

// Study mode helpers
const getStudyModeLabel = (mode) => {
  const labels = {
    'initial': 'Initial',
    'alternance': 'Alternance',
    'continue': 'Evening'
  }
  return labels[mode] || mode
}

const getStudyModeBadgeClass = (mode) => {
  const classes = {
    'initial': 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
    'alternance': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
    'continue': 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100'
  }
  return classes[mode] || 'bg-gray-100 text-gray-800'
}

// Watch modal open
watch(() => props.isOpen, async (newVal) => {
  if (newVal) {
    resetForm()
    form.day = props.day
    form.start_time = props.startTime
    form.end_time = props.endTime
    form.academic_year = props.academicYear
    await fetchSpecialtySemesters()
  }
})

// Fetch specialty-semester combinations
const fetchSpecialtySemesters = async () => {
  loadingSpecialties.value = true
  try {
    const response = await schedulesApi.getSpecialtySemesters(props.academicYear)
    specialtySemesters.value = response.data.specialty_semesters || []
  } catch (error) {
    console.error('Failed to fetch specialty semesters:', error)
  } finally {
    loadingSpecialties.value = false
  }
}

// Select specialty-semester from table
const selectSpecialtySemester = async (item) => {
  selectedSpecialtySemester.value = item
  form.specialty_id = item.specialty_id
  form.study_mode = item.study_mode
  form.semester = item.semester
  availableGroups.value = item.groups || []
  
  // Fetch modules for this specialty and semester
  await fetchModules()
}

// Clear selection and go back to table
const clearSelection = () => {
  selectedSpecialtySemester.value = null
  form.specialty_id = ''
  form.study_mode = ''
  form.semester = ''
  form.module_id = ''
  form.teacher_id = ''
  form.group = ''
  modules.value = []
  moduleTeachers.value = []
  availableGroups.value = []
}

// Fetch modules for selected specialty and semester
const fetchModules = async () => {
  loadingModules.value = true
  try {
    const response = await axios.get('/api/admin/modules', {
      params: { 
        specialty_id: form.specialty_id,
        semester: form.semester
      }
    })
    modules.value = (response.data.modules || []).filter(m => m.semester === form.semester)
  } catch (error) {
    console.error('Failed to fetch modules:', error)
  } finally {
    loadingModules.value = false
  }
}

// Event: Module changed
const onModuleChange = async () => {
  form.teacher_id = ''
  moduleTeachers.value = []
  
  if (!form.module_id) return
  
  loadingTeachers.value = true
  try {
    const response = await axios.get(`/api/admin/modules/${form.module_id}`)
    moduleTeachers.value = response.data.module.teachers || []
  } catch (error) {
    console.error('Failed to fetch module teachers:', error)
  } finally {
    loadingTeachers.value = false
  }
}

// Validate form
const validate = () => {
  errors.value = {}
  
  if (!form.module_id) {
    errors.value.module_id = 'Module is required'
  }
  
  if (!form.teacher_id) {
    errors.value.teacher_id = 'Teacher is required'
  }
  
  return Object.keys(errors.value).length === 0
}

// Close modal
const close = () => {
  emit('close')
  resetForm()
}

// Reset form
const resetForm = () => {
  selectedSpecialtySemester.value = null
  form.specialty_id = ''
  form.study_mode = ''
  form.semester = ''
  form.module_id = ''
  form.teacher_id = ''
  form.group = ''
  errors.value = {}
  modules.value = []
  moduleTeachers.value = []
  availableGroups.value = []
}

// Submit form
const submit = async () => {
  if (!validate()) return
  
  loading.value = true
  try {
    const data = {
      ...form,
      specialty_id: parseInt(form.specialty_id),
      semester: parseInt(form.semester),
      module_id: parseInt(form.module_id),
      teacher_id: parseInt(form.teacher_id),
      group: form.group || null
    }
    
    emit('save', data)
  } finally {
    loading.value = false
  }
}
</script>
