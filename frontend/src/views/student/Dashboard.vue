<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import studentApi from '@/api/endpoints/student'
import Card from '@/components/common/Card.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { 
  BookOpenIcon, 
  AcademicCapIcon,
  ClockIcon, 
  ClipboardDocumentListIcon,
  CalendarIcon
} from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const loading = ref(true)
const error = ref(null)
const stats = ref({
  modulesCount: 0,
  semesterAverage: null,
  classesThisWeek: 0,
  pendingTasks: 0
})
const attendanceData = ref({
  total: 0,
  present: 0,
  late: 0,
  absent: 0,
  excused: 0
})
const weeklySchedule = ref([])
const modules = ref([])

onMounted(async () => {
  try {
    loading.value = true
    console.log('Loading dashboard...')
    const data = await studentApi.getDashboard()
    console.log('Dashboard response:', data)
    
    // Update stats with API response
    stats.value = {
      modulesCount: data.statistics?.modules_count || 0,
      semesterAverage: data.statistics?.semester_average ?? null,
      classesThisWeek: data.statistics?.classes_this_week || 0,
      pendingTasks: data.statistics?.pending_homeworks ?? data.statistics?.pending_tasks ?? 0
    }
    
    attendanceData.value = {
      total: data.statistics?.attendance?.total || 0,
      present: data.statistics?.attendance?.present || 0,
      late: data.statistics?.attendance?.late || 0,
      absent: data.statistics?.attendance?.absent || 0,
      excused: data.statistics?.attendance?.excused || 0
    }
    
    weeklySchedule.value = data.weekly_schedule || []
    modules.value = data.modules || []
    
    console.log('Stats:', stats.value)
    console.log('Attendance:', attendanceData.value)
    console.log('Weekly schedule:', weeklySchedule.value)
    
    // Update auth store user info if needed
    if (data.student) {
      authStore.user = { ...authStore.user, ...data.student }
    }
    
  } catch (err) {
    console.error('Failed to fetch dashboard stats:', err)
    error.value = t('student.dashboard.impossible_charger_tableau_bord')
  } finally {
    loading.value = false
  }
})

// Real figures only: no invented trends
const statCards = computed(() => {
  const avg = stats.value.semesterAverage
  const pending = stats.value.pendingTasks
  return [
    {
      label: t('labels.studentStats.modules'),
      value: stats.value.modulesCount,
      icon: BookOpenIcon,
      hint: authStore.user?.current_semester ? t('labels.semester', { n: authStore.user.current_semester }) : t('labels.currentSemester'),
      hintClass: 'text-gray-500 dark:text-gray-400',
    },
    {
      label: t('labels.studentStats.average'),
      value: avg === null ? '—' : Number(avg).toFixed(2),
      icon: AcademicCapIcon,
      hint: avg === null ? t('labels.studentStats.noGrade') : avg >= 10 ? t('labels.studentStats.above') : t('labels.studentStats.below'),
      hintClass: avg === null ? 'text-gray-500 dark:text-gray-400' : avg >= 10 ? 'text-teal-600 dark:text-teal-300' : 'text-red-600 dark:text-red-300',
    },
    {
      label: t('labels.studentStats.sessions'),
      value: stats.value.classesThisWeek,
      icon: ClockIcon,
      hint: t('labels.studentStats.perSchedule'),
      hintClass: 'text-gray-500 dark:text-gray-400',
    },
    {
      label: t('labels.studentStats.homeworks'),
      value: pending,
      icon: ClipboardDocumentListIcon,
      hint: pending > 0 ? t('labels.studentStats.beforeDeadline') : t('labels.studentStats.noPending'),
      hintClass: pending > 0 ? 'text-amber-600 dark:text-amber-300' : 'text-gray-500 dark:text-gray-400',
    },
  ]
})

const maxAttendanceValue = computed(() => {
  return Math.max(attendanceData.value.total, attendanceData.value.present, attendanceData.value.late, attendanceData.value.absent, attendanceData.value.excused, 1)
})

const getBarHeight = (value) => {
  return `${(value / maxAttendanceValue.value) * 100}%`
}

const weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']
const timeSlots = ['08:00 - 10:30', '11:00 - 12:30']

const getClassForSlot = (day, time) => {
  return weeklySchedule.value.find(item => 
    item.day === day && item.time === time
  )
}
</script>

<template>
  <div>
    <div v-if="loading" class="flex justify-center items-center h-64">
      <LoadingSpinner size="large" />
    </div>

    <div v-else>
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ t('student.dashboard.tableau_bord') }}</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          {{ t('labels.welcome', { name: authStore.userName || t('common.student') }) }}
        </p>
        <div v-if="authStore.user?.specialty" class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
          {{ t('student.dashboard.semestre', { p0: authStore.user.specialty.name, p1: authStore.user.current_semester }) }}
        </div>
      </div>

      <div v-if="error" class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
        <p class="text-red-600 dark:text-red-400 text-sm">{{ error }}</p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div v-for="card in statCards" :key="card.label" class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
          <span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-navy-600 via-teal-600 to-gold-500 opacity-80" aria-hidden="true"></span>
          <div class="flex items-start justify-between gap-3">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ card.label }}</p>
            <span class="rounded-lg bg-navy-50 p-2 dark:bg-navy-900/40">
              <component :is="card.icon" class="h-5 w-5 text-navy-600 dark:text-navy-200" />
            </span>
          </div>
          <p class="mt-1 font-serif text-3xl font-bold leading-none text-navy-800 dark:text-white">{{ card.value }}</p>
          <p class="mt-3 text-xs font-medium" :class="card.hintClass">{{ card.hint }}</p>
        </div>
      </div>

      <!-- Attendance Overview Chart -->
      <Card :title="t('student.dashboard.mon_assiduite')" class="mb-8">
        <div v-if="attendanceData.total > 0" class="relative py-8">
          <!-- Y-axis labels -->
          <div class="absolute left-0 top-8 h-80 flex flex-col justify-between text-xs text-gray-500 dark:text-gray-400 pr-2 text-right">
            <span>{{ maxAttendanceValue }}</span>
            <span>{{ maxAttendanceValue / 2 }}</span>
            <span>0</span>
          </div>

          <!-- Chart container -->
          <div class="ml-12 flex items-end justify-center space-x-12 h-80 border-l-2 border-b-2 border-gray-300 dark:border-gray-600 pb-4">
            <!-- Séances Bar -->
            <div class="flex flex-col items-center justify-end h-full group">
              <div class="w-32 bg-navy-600 rounded-t-xl flex items-center justify-center text-white font-bold text-lg shadow-lg hover:shadow-2xl hover:shadow-navy-600/30 transition-all duration-300"
                   :style="{ height: `${(attendanceData.total / maxAttendanceValue * 100)}%`, minHeight: '60px' }">
                {{ attendanceData.total }}
              </div>
              <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ t('student.dashboard.seances') }}</p>
            </div>

            <!-- Present Bar -->
            <div class="flex flex-col items-center justify-end h-full group">
              <div class="w-32 bg-teal-500 rounded-t-xl flex items-center justify-center text-white font-bold text-lg shadow-lg hover:shadow-2xl hover:shadow-teal-500/30 transition-all duration-300"
                   :style="{ height: `${(attendanceData.present / maxAttendanceValue * 100)}%`, minHeight: '60px' }">
                {{ attendanceData.present }}
              </div>
              <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ t('common.present') }}</p>
            </div>

            <!-- Late Bar -->
            <div class="flex flex-col items-center justify-end h-full group">
              <div v-if="attendanceData.late > 0" class="w-32 bg-amber-300 rounded-t-xl flex items-center justify-center text-gray-800 font-bold text-lg shadow-lg hover:shadow-2xl hover:shadow-amber-300/50 transition-all duration-300"
                   :style="{ height: `${(attendanceData.late / maxAttendanceValue * 100)}%`, minHeight: '60px' }">
                {{ attendanceData.late }}
              </div>
              <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ t('common.late') }}</p>
            </div>

            <!-- Absent Bar -->
            <div class="flex flex-col items-center justify-end h-full group">
              <div v-if="attendanceData.absent > 0" class="w-32 bg-red-400 rounded-t-xl flex items-center justify-center text-white font-bold text-lg shadow-lg hover:shadow-2xl hover:shadow-red-400/50 transition-all duration-300"
                   :style="{ height: `${(attendanceData.absent / maxAttendanceValue * 100)}%`, minHeight: '60px' }">
                {{ attendanceData.absent }}
              </div>
              <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ t('common.absent') }}</p>
            </div>

            <!-- Excused Bar -->
            <div class="flex flex-col items-center justify-end h-full group">
              <div v-if="attendanceData.excused > 0" class="w-32 bg-gray-400 rounded-t-xl flex items-center justify-center text-white font-bold text-lg shadow-lg hover:shadow-2xl hover:shadow-gray-400/30 transition-all duration-300"
                   :style="{ height: `${(attendanceData.excused / maxAttendanceValue * 100)}%`, minHeight: '60px' }">
                {{ attendanceData.excused }}
              </div>
              <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ t('common.excused') }}</p>
            </div>
          </div>
        </div>
        <div v-else class="h-80 flex items-center justify-center text-gray-500 dark:text-gray-400">
          {{ t('student.dashboard.aucune_donnee_dassiduite_moment') }}
        </div>

        <!-- Legend -->
        <div class="flex items-center justify-center flex-wrap gap-6 mt-8">
          <div class="flex items-center">
            <div class="w-5 h-5 bg-navy-600 rounded mr-2"></div>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ t('student.dashboard.seances') }}</span>
          </div>
          <div class="flex items-center">
            <div class="w-5 h-5 bg-teal-500 rounded mr-2"></div>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ t('common.present') }}</span>
          </div>
          <div class="flex items-center">
            <div class="w-5 h-5 bg-amber-300 rounded mr-2"></div>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ t('common.late') }}</span>
          </div>
          <div class="flex items-center">
            <div class="w-5 h-5 bg-red-400 rounded mr-2"></div>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ t('common.absent') }}</span>
          </div>
          <div class="flex items-center">
            <div class="w-5 h-5 bg-gray-400 rounded mr-2"></div>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ t('common.excused') }}</span>
          </div>
        </div>
      </Card>

      <!-- Modules List with Coefficients -->
      <Card :title="t('student.dashboard.modules_semestre')" class="mb-8">
        <div v-if="modules.length > 0" class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b-2 border-gray-300 dark:border-gray-600">
                <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-800 dark:to-gray-800/50">{{ t('common.code') }}</th>
                <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-800 dark:to-gray-800/50">{{ t('common.module') }}</th>
                <th class="text-center py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-800 dark:to-gray-800/50">{{ t('common.coefficient') }}</th>
                <th class="text-center py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-800 dark:to-gray-800/50">{{ t('student.dashboard.heures_semaine') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="module in modules" :key="module.id" class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                <td class="py-4 px-4 text-sm font-mono font-semibold text-blue-600 dark:text-blue-400">{{ module.code }}</td>
                <td class="py-4 px-4 text-sm font-medium text-gray-900 dark:text-white">{{ module.name }}</td>
                <td class="py-4 px-4 text-center">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-900 dark:from-blue-900/30 dark:to-indigo-900/30 dark:text-blue-100">
                    {{ module.coefficient }}
                  </span>
                </td>
                <td class="py-4 px-4 text-center text-sm text-gray-600 dark:text-gray-400">{{ module.hours_per_week }}h</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="bg-gray-50 dark:bg-gray-800/50">
                <td colspan="2" class="py-4 px-4 text-sm font-bold text-gray-900 dark:text-white">{{ t('common.total') }}</td>
                <td class="py-4 px-4 text-center">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-gradient-to-r from-green-100 to-emerald-100 text-green-900 dark:from-green-900/30 dark:to-emerald-900/30 dark:text-green-100">
                    {{ modules.reduce((sum, m) => sum + (m.coefficient || 0), 0) }}
                  </span>
                </td>
                <td class="py-4 px-4 text-center text-sm font-bold text-gray-900 dark:text-white">
                  {{ modules.reduce((sum, m) => sum + (m.hours_per_week || 0), 0) }}h
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
        <div v-else class="h-32 flex items-center justify-center text-gray-500 dark:text-gray-400">
          {{ t('student.dashboard.aucun_module_semestre_actuel') }}
        </div>
      </Card>
    </div>
  </div>
</template>
