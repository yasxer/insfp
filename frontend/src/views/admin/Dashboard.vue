<script setup>
import { onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useAdminDashboardStore } from '@/stores/adminDashboard'
import axios from '@/api/axios'
import { 
  UsersIcon, 
  AcademicCapIcon, 
  BriefcaseIcon, 
  DocumentTextIcon,
  ArrowPathIcon
} from '@heroicons/vue/24/outline'

import StatCard from './components/StatCard.vue'
import StudentBarChart from './components/StudentBarChart.vue'
import TeacherBarChart from './components/TeacherBarChart.vue'
import DistributionChart from './components/DistributionChart.vue'
import FeaturedStudentsTable from './components/FeaturedStudentsTable.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const store = useAdminDashboardStore()
const { 
  statistics, 
  featuredStudents, 
  studentsBySpecialty, 
  teachersBySpecialty, 
  loading, 
  error 
} = storeToRefs(store)

// State for toggle between students and teachers
const showTeachers = ref(false)
const featuredTeachers = ref([])
const loadingTeachers = ref(false)

onMounted(() => {
  store.fetchDashboardData()
})

const refreshData = () => {
  store.fetchDashboardData()
}

const viewAllStudents = () => {
  showTeachers.value = false
}

const viewAllTeachers = async () => {
  showTeachers.value = true
  if (featuredTeachers.value.length === 0) {
    loadingTeachers.value = true
    try {
      const response = await axios.get('/api/admin/teachers')
      // Get 9 teachers from response
      const allTeachers = response.data.teachers || response.data.data || response.data
      featuredTeachers.value = allTeachers.slice(0, 9).map(t => ({
        id: t.id,
        name: t.full_name || `${t.first_name} ${t.last_name}`,
        avatar_initials: `${t.first_name[0]}${t.last_name[0]}`,
        registration_number: t.email || `ID-${t.id}`,
        specialty: t.specialization || 'General',
        year: '-',
        status: t.is_approved ? 'Active' : 'Pending'
      }))
    } catch (err) {
      console.error('Error fetching teachers:', err)
      featuredTeachers.value = []
    } finally {
      loadingTeachers.value = false
    }
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Welcome back, Admin User
        </p>
      </div>
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
          </svg>
          {{ new Date().toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: 'numeric' }) }}
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-if="error" class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="text-red-500">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
            <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
          </svg>
        </div>
        <p class="text-red-800 dark:text-red-200">{{ error }}</p>
      </div>
      <button @click="refreshData" class="text-sm font-medium text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
        Réessayer
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading && !statistics" class="flex justify-center py-12">
      <LoadingSpinner size="lg" />
    </div>

    <template v-else-if="statistics">
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <StatCard
          title="Total Students"
          :value="statistics.students.count.toLocaleString()"
          :change="statistics.students.change"
          :trend="statistics.students.trend"
          :icon="UsersIcon"
          :loading="loading"
        />
        <StatCard
          title="Total Teachers"
          :value="statistics.teachers.count.toLocaleString()"
          :change="statistics.teachers.change"
          :trend="statistics.teachers.trend"
          :icon="AcademicCapIcon"
          :loading="loading"
        />
        <StatCard
          title="Total Courses"
          :value="statistics.specialties.count"
          :change="statistics.specialties.change"
          change-text="New this week"
          :trend="statistics.specialties.trend"
          :icon="BriefcaseIcon"
          :loading="loading"
        />
        <StatCard
          title="Specialties"
          :value="statistics.exams.count"
          :change="statistics.exams.change"
          :trend="statistics.exams.trend"
          :icon="DocumentTextIcon"
          :loading="loading"
        />
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Students by Specialty -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Students by Specialty</h3>
            <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
              </svg>
            </button>
          </div>
          <StudentBarChart :data="studentsBySpecialty" />
        </div>

        <!-- Teachers by Specialty -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Teachers by Specialty</h3>
            <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
              </svg>
            </button>
          </div>
          <TeacherBarChart :data="teachersBySpecialty" />
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-center gap-4 mb-6">
        <button 
          @click="viewAllStudents" 
          :class="[
            'flex items-center gap-2 px-6 py-2.5 rounded-lg transition-colors',
            showTeachers ? 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-700' : 'bg-[#0f172a] text-white hover:bg-[#1e293b]'
          ]"
        >
          <UsersIcon class="w-5 h-5" />
          <span class="font-medium">Students</span>
        </button>
        
        <button 
          @click="viewAllTeachers"
          :class="[
            'flex items-center gap-2 px-6 py-2.5 rounded-lg transition-colors',
            showTeachers ? 'bg-[#0f172a] text-white hover:bg-[#1e293b]' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-700'
          ]"
        >
          <AcademicCapIcon class="w-5 h-5" />
          <span class="font-medium">Teachers</span>
        </button>
      </div>

      <!-- Featured Table -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
              {{ showTeachers ? "Featured Teachers" : "Today's Featured Students" }}
            </h3>
            <span class="text-sm text-gray-500">{{ new Date().toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: 'numeric' }) }}</span>
          </div>
        </div>
        
        <div v-if="loadingTeachers" class="flex justify-center py-12">
          <LoadingSpinner size="lg" />
        </div>
        
        <FeaturedStudentsTable 
          v-else
          :students="showTeachers ? featuredTeachers : featuredStudents.map(s => ({
            id: s.id,
            name: s.name,
            avatar_initials: s.avatar_initials,
            registration_number: s.registration_number,
            specialty: s.specialty,
            year: s.year,
            status: s.status === 'enrolled' ? 'Enrolled' : s.status === 'Graduated' ? 'Graduated' : 'On Leave'
          }))"
          :is-teachers="showTeachers"
        />
      </div>
    </template>
  </div>
</template>
