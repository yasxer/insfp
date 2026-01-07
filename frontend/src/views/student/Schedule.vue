<script setup>
import { ref, onMounted, computed } from 'vue'
import Card from '@/components/common/Card.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import studentApi from '@/api/endpoints/student'

const loading = ref(true)
const scheduleData = ref([])
const error = ref(null)
const currentWeek = ref('Week 1, Semester 1')

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

// Convert French day names to English
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

onMounted(async () => {
  try {
    loading.value = true
    const response = await studentApi.getSchedule()
    
    console.log('Schedule API response:', response)
    
    if (Array.isArray(response)) {
      const allClasses = []
      response.forEach((dayData) => {
        if (dayData.classes && Array.isArray(dayData.classes)) {
          dayData.classes.forEach(classItem => {
            classItem.day_name = dayData.day_name
            allClasses.push(classItem)
          })
        }
      })
      scheduleData.value = allClasses
      console.log('Processed schedule data:', allClasses)
    } else {
      scheduleData.value = []
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
