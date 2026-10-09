<script setup>
import { useI18n } from 'vue-i18n'
import { dateLocale } from '@/i18n'
const { t } = useI18n()
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import teacherApi from '@/api/endpoints/teacherPortal'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import {
  BookOpenIcon,
  CalendarIcon,
} from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const loading = ref(true)
const dashboardData = ref({
  teacher: null,
  statistics: {
    modules_count: 0,
    can_teach_modules_count: 0,
    upcoming_lessons_count: 0
  }
})

onMounted(async () => {
  try {
    const data = await teacherApi.getDashboardStats()
    dashboardData.value = data
  } catch (error) {
    console.error('Failed to load dashboard data:', error)
  } finally {
    loading.value = false
  }
})

// Current date formatted
const currentDate = new Date().toLocaleDateString(dateLocale(), {
  weekday: 'long',
  year: 'numeric',
  month: 'long',
  day: 'numeric'
})
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ t('teacher.dashboard.tableau_bord') }}</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          {{ t('labels.welcome', { name: dashboardData.teacher?.full_name || authStore.user?.name || t('teacher.dashboard.professeur') }) }}
        </p>
      </div>
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 capitalize">
          <CalendarIcon class="w-5 h-5" />
          {{ currentDate }}
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <LoadingSpinner size="lg" />
    </div>

    <!-- Content -->
    <template v-else>
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Mes Modules Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex items-center">
          <div class="p-3 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 mr-4">
            <BookOpenIcon class="w-8 h-8" />
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('teacher.dashboard.mes_modules_affectes') }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">
              {{ dashboardData.statistics.modules_count }}
            </p>
          </div>
        </div>


        <!-- Séances à venir Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex items-center">
          <div class="p-3 rounded-full bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400 mr-4">
            <CalendarIcon class="w-8 h-8" />
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('teacher.dashboard.seances_semaine') }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">
              {{ dashboardData.statistics.upcoming_lessons_count }}
            </p>
          </div>
        </div>
      </div>

      <!-- Informations Enseignant -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ t('teacher.dashboard.informations_professionnelles') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex items-center gap-3">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold uppercase">
              {{ dashboardData.teacher?.full_name?.charAt(0) || 'P' }}
            </div>
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('teacher.dashboard.nom_complet') }}</p>
              <p class="font-medium text-gray-900 dark:text-white">{{ dashboardData.teacher?.full_name || t('teacher.dashboard.non_defini') }}</p>
            </div>
          </div>
          <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('common.specialty') }}</p>
            <p class="font-medium text-gray-900 dark:text-white">
              {{ dashboardData.teacher?.specialty?.name || t('teacher.dashboard.aucune_specialite_assignee') }}
            </p>
          </div>
          <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('common.email') }}</p>
            <p class="font-medium text-gray-900 dark:text-white">{{ dashboardData.teacher?.email || authStore.user?.email }}</p>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>