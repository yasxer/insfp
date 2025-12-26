<script setup>
import { ref, onMounted, computed } from 'vue'
import Card from '@/components/common/Card.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import studentApi from '@/api/endpoints/student'

const loading = ref(true)
const scheduleData = ref([])
const error = ref(null)
const currentWeek = ref('Week 1, Semester 1')

const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
const timeSlots = [
  '08:00 - 09:30',
  '09:45 - 11:15',
  '11:30 - 13:00',
  '14:00 - 15:30',
  '15:45 - 17:15'
]

// Convert day name from API (lowercase French) to English
const normalizeDayName = (dayName) => {
  const dayMap = {
    'lundi': 'Monday',
    'mardi': 'Tuesday', 
    'mercredi': 'Wednesday',
    'jeudi': 'Thursday',
    'vendredi': 'Friday',
    'samedi': 'Saturday',
    'dimanche': 'Sunday',
    'monday': 'Monday',
    'tuesday': 'Tuesday',
    'wednesday': 'Wednesday',
    'thursday': 'Thursday',
    'friday': 'Friday',
    'saturday': 'Saturday',
    'sunday': 'Sunday'
  }
  return dayMap[dayName.toLowerCase()] || dayName
}

// Check if time falls within slot
const isTimeInSlot = (time, slot) => {
  const [slotStart] = slot.split(' - ')
  return time === slotStart
}

const getClassForSlot = (day, timeSlot) => {
  const found = scheduleData.value.find(item => {
    const itemDay = normalizeDayName(item.day_name)
    // Handle start_time as string or object
    let itemTime = ''
    if (typeof item.start_time === 'string') {
      itemTime = item.start_time.substring(0, 5)
    } else if (item.start_time) {
      itemTime = item.start_time.toString().substring(0, 5)
    }
    
    const slotTime = timeSlot.substring(0, 5)
    const match = itemDay === day && itemTime === slotTime
    
    if (match) {
      console.log('Match found:', {
        day,
        timeSlot,
        itemDay,
        itemTime,
        slotTime,
        class: item.module?.name
      })
    }
    
    return match
  })
  return found
}

onMounted(async () => {
  try {
    loading.value = true
    console.log('Loading schedule...')
    const response = await studentApi.getSchedule()
    console.log('Schedule API response:', response)
    
    // Response is now array of days directly
    if (Array.isArray(response)) {
      // Flatten array of days to array of classes
      scheduleData.value = response.flatMap(day => day.classes || [])
      console.log('Schedule classes loaded:', scheduleData.value.length)
      console.log('Schedule data sample:', scheduleData.value[0])
      console.log('Full schedule data:', JSON.parse(JSON.stringify(scheduleData.value)))
    } else {
      scheduleData.value = []
      console.warn('Unexpected schedule response format:', response)
    }
  } catch (err) {
    console.error('Failed to load schedule:', err)
    error.value = 'Failed to load schedule'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Schedule</h1>
      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        {{ currentWeek }}
      </p>
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
                  <div v-if="getClassForSlot(day, timeSlot)" class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-3 rounded h-full">
                    <p class="font-semibold text-sm text-gray-900 dark:text-white">
                      {{ getClassForSlot(day, timeSlot).module.name }}
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                      {{ getClassForSlot(day, timeSlot).teacher?.full_name || 'TBA' }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                      Room {{ getClassForSlot(day, timeSlot).room }}
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
          <span class="text-gray-600 dark:text-gray-400">Regular Class</span>
        </div>
      </div>
    </div>
  </div>
</template>
