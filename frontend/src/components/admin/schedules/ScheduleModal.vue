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
                Add Schedule
              </h3>
              <div class="mt-4 space-y-4">
                <!-- Study Mode Selection -->
                <div>
                  <label for="study_mode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Study Mode</label>
                  <select 
                    v-model="form.study_mode" 
                    @change="onStudyModeChange"
                    id="study_mode" 
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option value="">Select Study Mode</option>
                    <option value="presencial">Presencial (Initial)</option>
                    <option value="apprentissage">Apprentissage (Alternance)</option>
                    <option value="cours_de_soir">Cours de Soir (Continue)</option>
                  </select>
                  <p v-if="errors.study_mode" class="mt-1 text-sm text-red-600">{{ errors.study_mode }}</p>
                </div>

                <!-- Specialty Selection -->
                <div v-if="form.study_mode">
                  <label for="specialty" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Specialty</label>
                  <select 
                    v-model="form.specialty_id" 
                    @change="onSpecialtyChange"
                    id="specialty" 
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option value="">Select Specialty</option>
                    <option v-for="specialty in specialties" :key="specialty.id" :value="specialty.id">
                      {{ specialty.name }}
                    </option>
                  </select>
                  <p v-if="errors.specialty_id" class="mt-1 text-sm text-red-600">{{ errors.specialty_id }}</p>
                </div>

                <!-- Group Selection -->
                <div v-if="form.specialty_id">
                  <label for="group" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Group</label>
                  <select 
                    v-model="form.group" 
                    id="group" 
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option value="">All Specialty</option>
                    <option v-for="group in availableGroups" :key="group" :value="group">
                      Group {{ group }}
                    </option>
                  </select>
                </div>

                <!-- Module Selection -->
                <div v-if="form.specialty_id">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Module</label>
                  
                  <!-- Grouped by Semester -->
                  <div v-for="(modules, semester) in groupedModules" :key="semester" class="mb-4">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">Semester {{ semester }}</p>
                    <div class="space-y-2">
                      <label 
                        v-for="module in modules" 
                        :key="module.id"
                        class="flex items-center p-3 border rounded-md cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700"
                        :class="form.module_id === module.id ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900' : 'border-gray-300 dark:border-gray-600'"
                      >
                        <input
                          type="radio"
                          :value="module.id"
                          v-model="form.module_id"
                          @change="onModuleChange"
                          class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                        >
                        <span class="ml-3 text-sm text-gray-900 dark:text-white">
                          {{ module.name }} <span class="text-gray-500">({{ module.code }})</span>
                        </span>
                      </label>
                    </div>
                  </div>
                  
                  <p v-if="Object.keys(groupedModules).length === 0" class="text-sm text-gray-500 dark:text-gray-400 italic">
                    No modules found for this specialty
                  </p>
                  <p v-if="errors.module_id" class="mt-1 text-sm text-red-600">{{ errors.module_id }}</p>
                </div>

                <!-- Teacher Selection -->
                <div v-if="form.module_id">
                  <label for="teacher" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teacher</label>
                  <select 
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

                <!-- Time Display -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                  <p class="text-sm text-gray-700 dark:text-gray-300">
                    <strong>Day:</strong> {{ dayName }}<br/>
                    <strong>Time:</strong> {{ startTime }} - {{ endTime }}
                  </p>
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
import { ref, reactive, watch, computed } from 'vue'
import axios from '@/api/axios'
import schedulesApi from '@/api/endpoints/schedules'

const props = defineProps({
  isOpen: Boolean,
  day: String,
  startTime: String,
  endTime: String,
  academicYear: String
})

const emit = defineEmits(['close', 'save'])

const loading = ref(false)
const errors = ref({})
const specialties = ref([])
const specialtyModules = ref([])
const moduleTeachers = ref([])
const availableGroups = ref([])

const form = reactive({
  study_mode: '',
  specialty_id: '',
  module_id: '',
  teacher_id: '',
  group: '',
  day: props.day,
  start_time: props.startTime,
  end_time: props.endTime,
  academic_year: props.academicYear
})

// Group modules by semester
const groupedModules = computed(() => {
  const grouped = {}
  specialtyModules.value.forEach(module => {
    const semester = module.semester || 1
    if (!grouped[semester]) {
      grouped[semester] = []
    }
    grouped[semester].push(module)
  })
  return grouped
})

const form = reactive({
  study_mode: '',
  specialty_id: '',
  module_id: '',
  teacher_id: '',
  group: '',
  day: props.day,
  start_time: props.startTime,
  end_time: props.endTime,
  academic_year: props.academicYear
})

// Group modules by semester
const groupedModules = computed(() => {
  const grouped = {}
  specialtyModules.value.forEach(module => {
    const semester = module.semester || 1
    if (!grouped[semester]) {
      grouped[semester] = []
    }
    grouped[semester].push(module)
  })
  return grouped
})

const dayNames = {
  'saturday': 'Saturday',
  'sunday': 'Sunday',
  'monday': 'Monday',
  'tuesday': 'Tuesday',
  'wednesday': 'Wednesday',
  'thursday': 'Thursday'
}

const dayName = computed(() => dayNames[props.day] || '')

watch(() => props.isOpen, async (newVal) => {
  if (newVal) {
    form.day = props.day
    form.start_time = props.startTime
    form.end_time = props.endTime
    form.academic_year = props.academicYear
    await fetchSpecialties()
  }
})

const fetchSpecialties = async () => {
  try {
    const response = await axios.get('/api/admin/specialties')
    specialties.value = response.data.specialties
  } catch (error) {
    console.error('Failed to fetch specialties:', error)
  }
}

const onStudyModeChange = () => {
  form.specialty_id = ''
  form.module_id = ''
  form.teacher_id = ''
  form.group = ''
  specialtyModules.value = []
  moduleTeachers.value = []
  availableGroups.value = []
}

const onSpecialtyChange = async () => {
  form.module_id = ''
  form.teacher_id = ''
  form.group = ''
  specialtyModules.value = []
  moduleTeachers.value = []
  
  if (form.specialty_id) {
    try {
      // Fetch modules for specialty
      const modulesResponse = await axios.get('/api/admin/modules', {
        params: { specialty_id: form.specialty_id }
      })
      specialtyModules.value = modulesResponse.data.modules
      
      // Fetch available groups
      const groupsResponse = await schedulesApi.getGroups(form.specialty_id)
      availableGroups.value = groupsResponse.data.groups
    } catch (error) {
      console.error('Failed to fetch specialty data:', error)
    }
  }
}

const onModuleChange = async () => {
  form.teacher_id = ''
  moduleTeachers.value = []
  
  if (form.module_id) {
    try {
      const response = await axios.get(`/api/admin/modules/${form.module_id}`)
      moduleTeachers.value = response.data.module.teachers || []
    } catch (error) {
      console.error('Failed to fetch module teachers:', error)
    }
  }
}

const validate = () => {
  errors.value = {}
  
  if (!form.study_mode) {
    errors.value.study_mode = 'Study mode is required'
  }
  
  if (!form.specialty_id) {
    errors.value.specialty_id = 'Specialty is required'
  }
  
  if (!form.module_id) {
    errors.value.module_id = 'Module is required'
  }
  
  if (!form.teacher_id) {
    errors.value.teacher_id = 'Teacher is required'
  }
  
  return Object.keys(errors.value).length === 0
}

const close = () => {
  emit('close')
  resetForm()
}

const resetForm = () => {
  form.study_mode = ''
  form.specialty_id = ''
  form.module_id = ''
  form.teacher_id = ''
  form.group = ''
  errors.value = {}
  specialtyModules.value = []
  moduleTeachers.value = []
  availableGroups.value = []
}

const submit = async () => {
  if (!validate()) {
    return
  }
  
  loading.value = true
  try {
    const data = {
      ...form,
      specialty_id: parseInt(form.specialty_id),
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
