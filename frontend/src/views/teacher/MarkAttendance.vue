<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import teacherApi from '@/api/endpoints/teacherPortal'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import {
  ArrowLeftIcon,
  CheckCircleIcon,
  MapPinIcon,
  ClockIcon
} from '@heroicons/vue/24/outline'
import { UserCircleIcon } from '@heroicons/vue/24/solid'

const route = useRoute()
const router = useRouter()
const scheduleId = route.params.scheduleId
const sessionDate = route.params.date

const loading = ref(true)
const saving = ref(false)
const scheduleData = ref(null)
const students = ref([])
const error = ref(null)
const successMsg = ref(null)

onMounted(async () => {
  fetchSessionData()
})

const fetchSessionData = async () => {
  loading.value = true
  try {
    const data = await teacherApi.getSessionStudents(scheduleId, sessionDate)
    scheduleData.value = data.schedule
    
    // Assign default status of 'present' if not specified
    students.value = data.students.map(s => ({
      ...s,
      status: s.current_status || 'present',
      notes: s.note || ''
    }))
  } catch (err) {
    console.error('Failed to fetch session students:', err)
    error.value = "Erreur lors du chargement des étudiants."
  } finally {
    loading.value = false
  }
}

const goBack = () => {
  router.push({ name: 'TeacherAttendance' })
}

const setStatus = (studentId, status) => {
  const s = students.value.find(st => st.id === studentId)
  if (s) s.status = status
}

const saveAttendance = async () => {
  saving.value = true
  error.value = null
  successMsg.value = null
  try {
    const payload = {
      date: sessionDate,
      attendance: students.value.map(s => ({
        student_id: s.id,
        status: s.status,
        notes: s.notes
      }))
    }
    
    await teacherApi.markAttendance(scheduleId, payload)
    successMsg.value = "L'appel a été enregistré avec succès."
    setTimeout(() => {
      successMsg.value = null
    }, 3000)
  } catch (err) {
    console.error('Failed to save attendance:', err)
    error.value = "Erreur lors de l'enregistrement de l'appel."
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center gap-4">
      <button 
        @click="goBack"
        class="p-2 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors"
      >
        <ArrowLeftIcon class="w-5 h-5" />
      </button>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Faire l'appel</h1>
    </div>

    <!-- Error/Success Indicators -->
    <div v-if="error" class="p-4 bg-red-50 text-red-700 border border-red-200 rounded-lg">
      {{ error }}
    </div>
    <div v-if="successMsg" class="p-4 bg-green-50 text-green-700 border border-green-200 rounded-lg flex items-center">
      <CheckCircleIcon class="w-5 h-5 mr-2" /> {{ successMsg }}
    </div>

    <div v-if="loading" class="flex justify-center py-12">
      <LoadingSpinner size="lg" />
    </div>

    <template v-else>
      <!-- Session Summary Card -->
      <div v-if="scheduleData" class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 text-xs font-bold rounded-full mb-2 uppercase">
            {{ new Date(sessionDate).toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}
          </span>
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">
            {{ scheduleData.module.name }} ({{ scheduleData.module.code }})
          </h2>
          <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mt-2">
            <span class="flex items-center"><ClockIcon class="w-4 h-4 mr-1"/> {{ scheduleData.start_time }} - {{ scheduleData.end_time }}</span>
            <span class="flex items-center"><MapPinIcon class="w-4 h-4 mr-1"/> {{ scheduleData.room || 'Salle N/A' }}</span>
          </div>
        </div>
        
        <!-- Summary Stats -->
        <div class="flex gap-4">
          <div class="text-center px-4">
            <p class="text-sm font-medium text-gray-500">Inscrits</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ students.length }}</p>
          </div>
          <div class="text-center px-4 border-l border-gray-200 dark:border-gray-700">
            <p class="text-sm font-medium text-gray-500">Présents</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
              {{ students.filter(s => s.status === 'present').length }}
            </p>
          </div>
          <div class="text-center px-4 border-l border-gray-200 dark:border-gray-700">
            <p class="text-sm font-medium text-gray-500">Absents</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">
              {{ students.filter(s => s.status === 'absent').length }}
            </p>
          </div>
        </div>
      </div>

      <!-- Action Required Empty state -->
      <div v-if="students.length === 0" class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Aucun étudiant</h3>
        <p class="mt-1 text-gray-500">Aucun étudiant n'est inscrit dans ce groupe/module.</p>
      </div>

      <!-- Attendance List -->
      <div v-else class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Étudiant</th>
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Présenced</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Notes (Optionnel)</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="student in students" :key="student.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <UserCircleIcon class="w-8 h-8 text-gray-400 mr-3" />
                    <div>
                      <div class="text-sm font-medium text-gray-900 dark:text-white">{{ student.full_name }}</div>
                      <div class="text-sm text-gray-500">{{ student.registration_number }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center justify-center space-x-2">
                    <button 
                      @click="setStatus(student.id, 'present')"
                      type="button" 
                      class="px-4 py-1.5 rounded-l-md text-sm font-medium border"
                      :class="student.status === 'present' 
                        ? 'bg-green-100 border-green-500 text-green-700 dark:bg-green-900/30' 
                        : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300'"
                    >
                      Présent
                    </button>
                    <button 
                      @click="setStatus(student.id, 'late')"
                      type="button" 
                      class="px-4 py-1.5 text-sm font-medium border-y border-x"
                      :class="student.status === 'late' 
                        ? 'bg-yellow-100 border-yellow-500 text-yellow-700 dark:bg-yellow-900/30' 
                        : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300'"
                    >
                      En retard
                    </button>
                    <button 
                      @click="setStatus(student.id, 'absent')"
                      type="button" 
                      class="px-4 py-1.5 rounded-r-md text-sm font-medium border"
                      :class="student.status === 'absent' 
                        ? 'bg-red-100 border-red-500 text-red-700 dark:bg-red-900/30' 
                        : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300'"
                    >
                      Absent
                    </button>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <input 
                    v-model="student.notes"
                    type="text" 
                    placeholder="Motif / Observations..." 
                    class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Action footer -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end">
          <button 
            @click="saveAttendance"
            :disabled="saving"
            class="flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <LoadingSpinner v-if="saving" size="sm" class="mr-2" color="text-white" />
            Enregistrer l'appel
          </button>
        </div>
      </div>
    </template>
  </div>
</template>