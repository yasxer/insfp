<script setup>
import { ref, onMounted, computed } from 'vue'
import teacherApi from '@/api/endpoints/teacherPortal'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { CalendarIcon, ClockIcon, MapPinIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

const loading = ref(true)
const currentWeekType = ref('current') // 'current' or 'next'
const scheduleData = ref({
  week: { start_date: '', end_date: '' },
  lessons: []
})

// French days for display
const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']
const DAYS_FR = {
  'Monday': 'Lundi', 'Tuesday': 'Mardi', 'Wednesday': 'Mercredi', 
  'Thursday': 'Jeudi', 'Friday': 'Vendredi', 'Saturday': 'Samedi', 'Sunday': 'Dimanche'
}

const fetchSchedule = async () => {
  loading.value = true
  try {
    const data = await teacherApi.getSchedule(currentWeekType.value)
    scheduleData.value = data
  } catch (error) {
    console.error('Failed to load schedule:', error)
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

// Group lessons by day
const groupedSchedule = computed(() => {
  const grouped = {}
  
  // Initialize all days (Mon-Sat usually, maybe Sun)
  DAYS.forEach(day => {
    grouped[day] = []
  })
  
  if (scheduleData.value.lessons) {
    scheduleData.value.lessons.forEach(lesson => {
      // Find the day name and add to our grouped array
      if (grouped[lesson.day_name]) {
        grouped[lesson.day_name].push(lesson)
      }
    })
  }
  
  return grouped
})

// Format helpers
const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })
}

const getDayDate = (dayName) => {
  const lessonForDay = scheduleData.value.lessons?.find(l => l.day_name === dayName)
  if (lessonForDay && lessonForDay.date) {
    return new Date(lessonForDay.date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })
  }
  return '' // Could compute offset from week.start_date realistically
}
</script>

<template>
  <div class="space-y-6 flex flex-col h-full max-h-[calc(100vh-100px)]">
    <!-- Header Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 flex-shrink-0">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Emploi du temps</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Semaine du {{ formatDate(scheduleData.week?.start_date) }} au {{ formatDate(scheduleData.week?.end_date) }}
        </p>
      </div>

      <div class="flex items-center gap-2 bg-white dark:bg-gray-800 p-1 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <button 
          @click="toggleWeek" 
          :disabled="currentWeekType === 'current'"
          class="p-2 rounded-md transition-colors"
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
          class="p-2 rounded-md transition-colors"
          :class="currentWeekType === 'next' ? 'text-gray-300 dark:text-gray-600' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'"
        >
          <ChevronRightIcon class="w-5 h-5" />
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center flex-1 items-center">
      <LoadingSpinner size="lg" />
    </div>

    <!-- Empty State -->
    <div v-else-if="!scheduleData.lessons || scheduleData.lessons.length === 0" class="flex-1 flex flex-col items-center justify-center bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
      <CalendarIcon class="w-16 h-16 text-gray-400 mb-4" />
      <h3 class="text-xl font-medium text-gray-900 dark:text-white">Aucun cours planifié</h3>
      <p class="mt-2 text-gray-500">Vous n'avez pas de créneaux programmés pour cette semaine.</p>
    </div>

    <!-- Desktop Grid View (hidden on small screens) -->
    <div v-else class="hidden md:flex flex-1 overflow-hidden bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
      <!-- We divide available space into a simple horizontal layout -->
      <div class="flex w-full divide-x divide-gray-200 dark:divide-gray-700 overflow-x-auto">
        <!-- Day Columns (Excluding Sunday usually, but let's include if it has data) -->
        <div 
          v-for="day in DAYS.filter(d => d !== 'Sunday' || groupedSchedule[d].length > 0)" 
          :key="day"
          class="flex-1 min-w-[200px] flex flex-col"
        >
          <!-- Day Header -->
          <div class="py-3 text-center border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80 sticky top-0">
            <h3 class="font-bold text-gray-900 dark:text-white">{{ DAYS_FR[day] }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ getDayDate(day) }}</p>
          </div>
          
          <!-- Lessons -->
          <div class="flex-1 p-2 space-y-3 overflow-y-auto bg-gray-50/30 dark:bg-transparent">
            <!-- Empty day slot -->
            <div v-if="groupedSchedule[day].length === 0" class="h-full flex items-center justify-center py-6">
              <span class="text-sm font-medium text-gray-400 dark:text-gray-600">Libre</span>
            </div>

            <!-- Lesson Cards -->
            <div 
              v-for="lesson in groupedSchedule[day]" 
              :key="`${lesson.date}-${lesson.start_time}`"
              class="bg-white dark:bg-gray-700 p-3 rounded-lg border-l-4 border-l-blue-500 border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md transition-shadow relative"
            >
              <div class="flex items-center text-xs font-semibold text-blue-600 dark:text-blue-400 mb-1.5">
                <ClockIcon class="w-3.5 h-3.5 mr-1" />
                {{ lesson.start_time }} - {{ lesson.end_time }}
              </div>
              
              <h4 class="font-bold text-sm text-gray-900 dark:text-white leading-snug mb-1 truncate" :title="lesson.module?.name">
                {{ lesson.module?.name }}
              </h4>
              <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-3">
                {{ lesson.module?.code }}
              </p>
              
              <div class="flex items-center text-xs text-gray-600 dark:text-gray-300 mt-auto">
                <MapPinIcon class="w-3.5 h-3.5 mr-1 text-gray-400" />
                <span class="truncate">{{ lesson.room || 'Salle N/A' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile List View (hidden on desktop) -->
    <div v-if="!loading && scheduleData.lessons?.length > 0" class="md:hidden flex-1 overflow-y-auto space-y-6 pb-6">
      <div 
        v-for="day in DAYS.filter(d => groupedSchedule[d].length > 0)" 
        :key="`mobile-${day}`"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
      >
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80 flex justify-between items-center">
          <h3 class="font-bold text-gray-900 dark:text-white">{{ DAYS_FR[day] }}</h3>
          <span class="text-sm text-gray-500 dark:text-gray-400">{{ getDayDate(day) }}</span>
        </div>
        
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
          <div 
            v-for="lesson in groupedSchedule[day]" 
            :key="`mobile-${lesson.date}-${lesson.start_time}`"
            class="p-4 flex gap-4"
          >
            <div class="flex flex-col items-center justify-center min-w-[60px] text-center border-r border-gray-100 dark:border-gray-700 pr-4">
              <span class="text-sm font-bold text-gray-900 dark:text-white">{{ lesson.start_time }}</span>
              <span class="text-xs text-gray-500">{{ lesson.end_time }}</span>
            </div>
            <div class="flex-1">
              <h4 class="font-bold text-sm text-blue-600 dark:text-blue-400 mb-0.5">
                {{ lesson.module?.name }}
              </h4>
              <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                {{ lesson.module?.code }}
              </p>
              <div class="flex items-center text-xs text-gray-600 dark:text-gray-300">
                <MapPinIcon class="w-3.5 h-3.5 mr-1 text-gray-400" />
                {{ lesson.room || 'Salle N/A' }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>