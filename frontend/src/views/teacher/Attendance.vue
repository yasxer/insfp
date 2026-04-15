<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import teacherApi from '@/api/endpoints/teacherPortal'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { CalendarIcon, ClockIcon, MapPinIcon, BookOpenIcon, CheckCircleIcon, ArrowRightIcon } from '@heroicons/vue/24/outline'

const router = useRouter()
const loading = ref(true)
const sessions = ref([])

onMounted(async () => {
  try {
    const data = await teacherApi.getAttendanceSessions()
    sessions.value = data.data || data || []
  } catch (error) {
    console.error('Failed to load attendance sessions:', error)
  } finally {
    loading.value = false
  }
})

const goToMarkAttendance = (scheduleId, date) => {
  router.push({ 
    name: 'TeacherMarkAttendance', 
    params: { scheduleId: scheduleId.toString(), date: date } 
  })
}

// Helper to format date
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric', month: 'short' })
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestion des Présences</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Séances programmées pour les deux prochaines semaines
        </p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <LoadingSpinner size="lg" />
    </div>

    <!-- Content -->
    <template v-else>
      <div v-if="sessions.length === 0" class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <CalendarIcon class="w-12 h-12 mx-auto text-gray-400 mb-4" />
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Aucune séance à venir</h3>
        <p class="mt-1 text-gray-500">Vous n'avez pas de créneaux programmés pour le moment.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Session Card -->
        <div 
          v-for="session in sessions" 
          :key="`${session.id}-${session.date}`"
          class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border transition-shadow relative overflow-hidden flex flex-col"
          :class="session.attendance_taken ? 'border-green-300 dark:border-green-800' : 'border-gray-200 dark:border-gray-700 hover:shadow-md'"
        >
          <!-- Status Banner -->
          <div 
            class="h-1.5 w-full absolute top-0 left-0"
            :class="session.attendance_taken ? 'bg-green-500' : 'bg-blue-500'"
          ></div>
          
          <div class="p-5 flex-1">
            <div class="flex justify-between items-start mb-3">
              <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full capitalize"
                :class="session.attendance_taken ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'">
                {{ formatDate(session.date) }}
              </span>
              <div v-if="session.attendance_taken" class="flex items-center text-green-600 dark:text-green-400 text-xs font-bold">
                <CheckCircleIcon class="w-4 h-4 mr-1" /> Fait
              </div>
            </div>
            
            <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight mb-1 truncate" :title="session.module?.name">
              {{ session.module?.name || 'Session' }}
            </h3>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">
              {{ session.module?.code }}
            </p>
            
            <div class="space-y-2 mb-4">
              <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                <ClockIcon class="w-4 h-4 mr-2 text-gray-400" />
                <span>{{ session.start_time }} - {{ session.end_time }}</span>
              </div>
              <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                <MapPinIcon class="w-4 h-4 mr-2 text-gray-400" />
                <span>{{ session.room || 'Salle non assignée' }}</span>
              </div>
            </div>
          </div>
          
          <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
            <button 
              @click="goToMarkAttendance(session.id, session.date)"
              class="w-full flex items-center justify-center gap-2 text-sm font-medium transition-colors rounded-lg py-2"
              :class="session.attendance_taken 
                ? 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600' 
                : 'text-white bg-blue-600 hover:bg-blue-700'"
            >
              {{ session.attendance_taken ? 'Modifier l\'appel' : 'Faire l\'appel' }}
              <ArrowRightIcon class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>