<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Deliberations Management</h1>
    </div>

    <!-- Filters configuration -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
      <form @submit.prevent="fetchStudents" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Session</label>
          <select v-model="filters.session_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm" required>
            <option value="" disabled>Select Session</option>
            <option v-for="session in sessions" :key="session.id" :value="session.id">{{ session.name }}</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Specialty</label>
            <select v-model="filters.specialty_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm" required :disabled="!filters.session_id">
              <option value="" disabled>Select Specialty</option>
              <option v-for="specialty in availableSpecialties" :key="specialty.id" :value="specialty.id">{{ specialty.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Semester</label>
            <select v-model="filters.semester" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm" :disabled="!filters.specialty_id">
              <option value="">Auto (Current Student Semester)</option>
            <option value="3">Semester 3</option>
            <option value="4">Semester 4</option>
            <option value="5">Semester 5</option>
            <option value="6">Semester 6</option>
          </select>
        </div>

        <div>
          <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" :disabled="loading">
            Search Students
          </button>
        </div>
      </form>
    </div>

    <!-- Student List / Results Table -->
    <div v-if="students.length > 0" class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Avg / Result</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Observations</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-for="student in students" :key="student.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900 dark:text-white">{{ student.name }}</div>
              <div class="text-sm text-gray-500 dark:text-gray-400">{{ student.registration_number }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span v-if="student.deliberation" :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', student.deliberation.result === 'passed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                {{ student.deliberation.average }} / 20 - {{ student.deliberation.result }}
              </span>
                <span v-else class="text-sm text-gray-500">
                  Not recorded
                  <span v-if="student.calculated_average" class="text-xs text-indigo-600 block">(Auto: {{ student.calculated_average }} / 20)</span>
                </span>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-[200px]" :title="student.deliberation?.observations">
                {{ student.deliberation?.observations || '--' }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
              <button v-if="!student.deliberation && student.calculated_average !== undefined" @click="confirmDeliberation(student)" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 font-semibold" title="Confirm Auto Calculated Average">Confirm</button>
              <button @click="openModal(student)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else-if="searched && students.length === 0" class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
      <p class="text-gray-500 dark:text-gray-400">No students found for this configuration.</p>
    </div>

    <!-- Edit/Save Deliberation Modal -->
    <div v-if="isModalOpen" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="closeModal">
          <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 text-gray-900 dark:text-white">
          <div>
            <h3 class="text-lg leading-6 font-medium" id="modal-title">Record Deliberation: {{ activeStudent?.name }}</h3>
            <div class="mt-4 space-y-4">
              <div>
                <label class="block text-sm font-medium">Average (out of 20)</label>
                <input type="number" step="0.01" min="0" max="20" v-model="formData.average" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 sm:text-sm" required>
              </div>

              <div>
                <label class="block text-sm font-medium">Result</label>
                <select v-model="formData.result" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 sm:text-sm" required>
                  <option value="passed">Passed (Admis)</option>
                  <option value="failed">Failed (Ajourné)</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium">Academic Year</label>
                <input type="text" v-model="formData.academic_year" placeholder="e.g. 2025/2026" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 sm:text-sm" required>
              </div>

              <div>
                <label class="block text-sm font-medium">Deliberation Date</label>
                <input type="date" v-model="formData.deliberation_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 sm:text-sm" required>
              </div>

              <div>
                <label class="block text-sm font-medium">Observations</label>
                <textarea v-model="formData.observations" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 sm:text-sm"></textarea>
              </div>
            </div>
          </div>
          <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
            <button @click="saveDeliberation" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm" :disabled="saving">
              {{ saving ? 'Saving...' : 'Save' }}
            </button>
            <button @click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import adminApi from '@/api/endpoints/admin'
import sessionApi from '@/api/endpoints/sessions'

const filters = ref({
  session_id: '',
  specialty_id: '',
  semester: ''
})

const sessions = ref([])
const specialties = ref([])
const students = ref([])
const loading = ref(false)
const searched = ref(false)
const isModalOpen = ref(false)
const saving = ref(false)
const activeStudent = ref(null)

const availableSpecialties = computed(() => {
  if (!filters.value.session_id) return []
  const session = sessions.value.find(s => s.id === filters.value.session_id)
  if (!session || !session.session_specialties) return []
  
  const map = {}
  session.session_specialties.forEach(ss => {
    map[ss.specialty_id] = { id: ss.specialty_id, name: ss.specialty_name }
  })
  return Object.values(map)
})

watch(() => filters.value.session_id, () => {
  filters.value.specialty_id = ''
  students.value = []
  searched.value = false
})

watch(() => filters.value.specialty_id, () => {
  students.value = []
  searched.value = false
})

const formData = ref({
  average: '',
  result: 'passed',
  academic_year: '',
  deliberation_date: new Date().toISOString().slice(0, 10),
  observations: ''
})

onMounted(() => {
  fetchBasicData()
})

const fetchBasicData = async () => {
    try {
       const [sess, spec] = await Promise.all([
           sessionApi.getSessionsForDropdown(),
           sessionApi.getSpecialties()
       ])
       
       let sessionData = sess.data || sess
       if (sessionData && typeof sessionData === 'object' && sessionData.data) {
          sessionData = sessionData.data
       }
       sessions.value = Array.isArray(sessionData) ? sessionData : []

       let specData = spec.data || spec
       if (specData && typeof specData === 'object' && specData.data) {
          specData = specData.data
       }
       specialties.value = Array.isArray(specData) ? specData : []

    } catch(e) {
       console.error('Error fetching initial data:', e)
    }
}

const fetchStudents = async () => {
  loading.value = true
  try {
    const payload = { ...filters.value }
    if (!payload.semester) {
      delete payload.semester
    }
    const res = await adminApi.getDeliberations(payload)
    students.value = res.students
    searched.value = true
  } catch (error) {
    alert('Error fetching students')
  } finally {
    loading.value = false
  }
}
const openModal = (student) => {
  activeStudent.value = student

  if (student.deliberation) {
    formData.value = {
      average: student.deliberation.average,
      result: student.deliberation.result,
      academic_year: student.deliberation.academic_year,
      deliberation_date: student.deliberation.deliberation_date,
      observations: student.deliberation.observations || ''
    }
  } else {
    // defaults with pre-calculated values
    formData.value = {
      average: student.calculated_average || '',
      result: student.calculated_result || 'passed',
      academic_year: new Date().getFullYear() + '/' + (new Date().getFullYear() + 1),
      deliberation_date: new Date().toISOString().slice(0, 10),
      observations: ''
    }
  }
  isModalOpen.value = true
}
const closeModal = () => {
  isModalOpen.value = false
  activeStudent.value = null
}

const saveDeliberation = async () => {
  if (formData.value.average === '') return alert('Average is required')

  saving.value = true
  try {
    await adminApi.saveDeliberation({
      student_id: activeStudent.value.id,
      semester: filters.value.semester || activeStudent.value.auto_semester,
      ...formData.value
    })

    // Refresh student list directly
    await fetchStudents()
    closeModal()
  } catch(error) {
    alert('Error saving deliberation')
  } finally {
    saving.value = false
  }
}

const confirmDeliberation = async (student) => {
  if (!confirm(`Are you sure you want to directly confirm the auto-calculated average of ${student.calculated_average}/20 for ${student.name}?`)) return
  
  const currentYear = new Date().getFullYear();
  const academicYear = `${currentYear}/${currentYear + 1}`;

  try {
    await adminApi.saveDeliberation({
      student_id: student.id,
      semester: filters.value.semester || student.auto_semester,
      average: student.calculated_average,
      result: student.calculated_result,
      academic_year: academicYear,
      deliberation_date: new Date().toISOString().slice(0, 10),
      observations: 'Confirmed Auto-Calculation'
    })
    await fetchStudents()
  } catch(error) {
    alert('Error confirming calculation')
  }
}
</script>
