<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Schedule Management</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage class schedules and timetables</p>
      </div>
      <div class="flex space-x-3">
        <select 
          v-model="currentAcademicYear" 
          class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
          <option value="2025-2026">2025-2026</option>
          <option value="2026-2027">2026-2027</option>
        </select>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Schedule Table -->
    <div v-else class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Time
              </th>
              <th v-for="day in days" :key="day" scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                {{ day }}
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="timeSlot in timeSlots" :key="timeSlot.start">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                {{ timeSlot.start }}<br/>{{ timeSlot.end }}
              </td>
              <td 
                v-for="day in days" 
                :key="`${day}-${timeSlot.start}`"
                class="px-2 py-2 text-sm text-gray-500 dark:text-gray-400 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                @click="openScheduleModal(day, timeSlot)"
              >
                <div v-if="getScheduleForSlot(day, timeSlot.start)" class="bg-indigo-100 dark:bg-indigo-900 rounded p-2 relative group">
                  <p class="font-medium text-indigo-900 dark:text-indigo-100 text-xs">
                    {{ getScheduleForSlot(day, timeSlot.start).module.name }}
                  </p>
                  <p class="text-indigo-700 dark:text-indigo-300 text-xs">
                    {{ getScheduleForSlot(day, timeSlot.start).teacher.full_name }}
                  </p>
                  <p v-if="getScheduleForSlot(day, timeSlot.start).group" class="text-indigo-600 dark:text-indigo-400 text-xs">
                    Group {{ getScheduleForSlot(day, timeSlot.start).group }}
                  </p>
                  <button 
                    @click.stop="deleteSchedule(getScheduleForSlot(day, timeSlot.start))"
                    class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity text-red-600 hover:text-red-800"
                  >
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </div>
                <div v-else class="h-16 flex items-center justify-center text-gray-400 dark:text-gray-600">
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Schedule Modal -->
    <ScheduleModal 
      :is-open="isModalOpen"
      :day="selectedDay"
      :start-time="selectedStartTime"
      :end-time="selectedEndTime"
      :academic-year="currentAcademicYear"
      @close="closeModal"
      @save="handleSave"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import schedulesApi from '@/api/endpoints/schedules'
import ScheduleModal from '@/components/admin/schedules/ScheduleModal.vue'

const loading = ref(false)
const schedules = ref([])
const isModalOpen = ref(false)
const selectedDay = ref(null)
const selectedStartTime = ref(null)
const selectedEndTime = ref(null)
const currentAcademicYear = ref('2025-2026')

const days = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday']

const timeSlots = [
  { start: '08:00', end: '09:30' },
  { start: '09:30', end: '11:00' },
  { start: '11:00', end: '12:30' },
  { start: '13:00', end: '14:30' },
  { start: '14:30', end: '16:00' },
  { start: '16:30', end: '18:00' },
  { start: '18:00', end: '19:30' },
]

const dayMapping = {
  'Saturday': 'saturday',
  'Sunday': 'sunday',
  'Monday': 'monday',
  'Tuesday': 'tuesday',
  'Wednesday': 'wednesday',
  'Thursday': 'thursday'
}

onMounted(async () => {
  await fetchSchedules()
})

const fetchSchedules = async () => {
  loading.value = true
  try {
    const response = await schedulesApi.getSchedules({
      academic_year: currentAcademicYear.value
    })
    schedules.value = response.data.schedules
    console.log('Fetched schedules:', schedules.value.length) // Debug
  } catch (error) {
    console.error('Failed to fetch schedules:', error)
  } finally {
    loading.value = false
  }
}

const getScheduleForSlot = (day, startTime) => {
  const dayKey = dayMapping[day]
  return schedules.value.find(s => {
    // Normalize time format (08:00:00 -> 08:00)
    const scheduleStart = s.start_time.substring(0, 5)
    return s.day === dayKey && scheduleStart === startTime
  })
}

const openScheduleModal = (day, timeSlot) => {
  selectedDay.value = dayMapping[day]
  selectedStartTime.value = timeSlot.start
  selectedEndTime.value = timeSlot.end
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  selectedDay.value = null
  selectedStartTime.value = null
  selectedEndTime.value = null
}

const handleSave = async (scheduleData) => {
  try {
    await schedulesApi.createSchedule(scheduleData)
    await fetchSchedules()
    closeModal()
  } catch (error) {
    console.error('Failed to save schedule:', error)
    alert(error.response?.data?.message || 'Failed to save schedule')
  }
}

const deleteSchedule = async (schedule) => {
  if (confirm(`Are you sure you want to delete this schedule?`)) {
    try {
      await schedulesApi.deleteSchedule(schedule.id)
      await fetchSchedules()
    } catch (error) {
      console.error('Failed to delete schedule:', error)
      alert(error.response?.data?.message || 'Failed to delete schedule')
    }
  }
}
</script>
