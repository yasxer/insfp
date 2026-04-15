<script setup>
import { ref, onMounted } from 'vue'
import Card from '@/components/common/Card.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import teacherApi from '@/api/endpoints/teacherPortal'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

const loading = ref(true)
const scheduleData = ref([])
const weekInfo = ref({ start_date: '', end_date: '' })
const error = ref(null)
const currentWeekType = ref('current') // 'current' or 'next'

// Days starting from Saturday (Algerian week)
const days = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday']
const timeSlots = [
  '08:00 - 09:30',
  '09:30 - 11:00',
  '11:00 - 12:30',
  '13:00 - 14:30',
  '14:30 - 16:00',
  '16:00 - 17:30'
]

// French days definition for display format logic (matching student)
const frenchToEnglish = (frenchDay) => {
  const map = {
    'lundi': 'Monday',
    'mardi': 'Tuesday',
    'mercredi': 'Wednesday',
    'jeudi': 'Thursday',
    'vendredi': 'Friday',
    'samedi': 'Saturday',
    'dimanche': 'Sunday'
  }
  return map[frenchDay?.toLowerCase()] || frenchDay
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })
}

const getClassForSlot = (day, timeSlot) => {
  const slotStartTime = timeSlot.split(' - ')[0]
  
  return scheduleData.value.find(classItem => {
    let classDay = classItem.day_name || ''
    classDay = frenchToEnglish(classDay)
    
    // Handle time format - remove seconds if present
    let classStartTime = classItem.start_time || ''
    if (classStartTime.length > 5) {
      classStartTime = classStartTime.substring(0, 5)
    }
    
    return classDay === day && classStartTime === slotStartTime
  })
}

const fetchSchedule = async () => {
  loading.value = true
  error.value = null
  try {
    const data = await teacherApi.getSchedule(currentWeekType.value)
    if (data.lessons) {
      scheduleData.value = data.lessons
      weekInfo.value = data.week
    } else {
      scheduleData.value = []
    }
  } catch (err) {
    console.error('Failed to load schedule:', err)
    error.value = 'Failed to load schedule'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchSchedule()
})

const toggleWeek = () => {
  currentWeekType.value = currentWeekType.value === 'current' ? 'next' : 'current'
  fetchSchedule()
}
</script>

<template>
  <div>
    <!-- Header Controls -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Emploi du temps</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Semaine du {{ formatDate(weekInfo.start_date) }} au {{ formatDate(weekInfo.end_date) }}
        </p>
      </div>

      <div class="flex items-center gap-2 bg-white dark:bg-gray-800 p-1 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <button 
          @click="toggleWeek" 
          :disabled="currentWeekType === 'current'"
          class="p-2 rounded-md transition-colors disabled:opacity-50"
          :class="currentWeekType === 'current' ? 'text-gray-300 dark:text-gray-600' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'"
        >
          <ChevronLeftIcon class="w-5 h-5" />
        </button>
        <span class="px-4 text-sm font-medium text-gray-900 dark:text-white w-40 text-center">
          {{ currentWeekType === 'current' ? 'Cette semaine' : 'Semaine prochaine' }}
        </span>
        <button 
          @click="toggleWeek" 
          :disabled="currentWeekType === 'next'"
          class="p-2 rounded-md transition-colors disabled:opacity-50"
          :class="currentWeekType === 'next' ? 'text-gray-300 dark:text-gray-600' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'"
        >
          <ChevronRightIcon class="w-5 h-5" />
        </button>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center items-center h-64">
      <LoadingSpinner size="large" />
    </div>

    <div v-else>
      <!-- Error Alert -->
      <div v-if="error" class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
        <p class="text-red-600 dark:text-red-400 text-sm">{{ error }}</p>
      </div>

      <!-- Schedule Table -->
      <Card no-padding>
        <div class="overflow-x-auto">
          <table class="w-full border-collapse min-w-[800px]">
            <thead>
              <tr class="bg-gray-50 dark:bg-gray-800">
                <th class="border border-gray-200 dark:border-gray-700 p-3 text-left font-semibold text-gray-700 dark:text-gray-300 w-32">Time</th>
                <th v-for="day in days" :key="day" class="border border-gray-200 dark:border-gray-700 p-3 text-center font-semibold text-gray-700 dark:text-gray-300">
                  {{ day }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="timeSlot in timeSlots" :key="timeSlot">
                <td class="border border-gray-200 dark:border-gray-700 p-3 font-medium text-sm whitespace-nowrap bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                  {{ timeSlot }}
                </td>
                <td v-for="day in days" :key="day" class="border border-gray-200 dark:border-gray-700 p-2 min-w-[140px]">
                  <div v-if="getClassForSlot(day, timeSlot)" class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-3 rounded h-full relative group">
                    <p class="font-semibold text-sm text-gray-900 dark:text-white truncate" :title="getClassForSlot(day, timeSlot).module?.name">
                      {{ getClassForSlot(day, timeSlot).module?.name }}
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 font-medium truncate">
                      {{ getClassForSlot(day, timeSlot).specialty?.code || 'Spécialité N/A' }}
                      <span v-if="getClassForSlot(day, timeSlot).group">- Gr: {{ getClassForSlot(day, timeSlot).group }}</span>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                      Salle {{ getClassForSlot(day, timeSlot).room || 'N/A' }}
                    </p>
                  </div>
                  <div v-else class="h-20"></div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </Card>

      <!-- Legend -->
      <div class="mt-6 flex gap-4 text-sm">
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 bg-blue-500 rounded"></div>
          <span class="text-gray-600 dark:text-gray-400">Cours planifié</span>
        </div>
      </div>
    </div>
  </div>
</template>
