<script setup>
import { computed, ref, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import studentApi from '@/api/endpoints/student'
import {
  HomeIcon,
  CalendarIcon,
  ClipboardDocumentCheckIcon,
  AcademicCapIcon,
  UserCircleIcon,
  ArrowLeftOnRectangleIcon,
  XMarkIcon,
  EnvelopeIcon,
  BookOpenIcon,
  DocumentTextIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  open: {
    type: Boolean,
    required: true
  }
})

const emit = defineEmits(['close'])

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const badges = ref({
  messages: 0,
  lessons: 0,
  documents: 0
})

const userDisplay = ref({
  name: '',
  role: ''
})

// Watch for user changes in auth store
watch(() => authStore.user, (newUser) => {
  if (newUser) {
    userDisplay.value = {
      name: newUser.name || newUser.email || 'User',
      role: newUser.role || ''
    }
  }
}, { immediate: true, deep: true })

const fetchBadges = async () => {
  if (authStore.user?.role === 'student') {
    try {
      const [msgRes, lessonRes, docRes] = await Promise.all([
        studentApi.getUnreadMessagesCount(),
        studentApi.getNewLessonsCount(),
        studentApi.getNewDocumentsCount()
      ])
      badges.value = {
        messages: msgRes.count,
        lessons: lessonRes.count,
        documents: docRes.count
      }
    } catch (err) {
      console.error('Failed to fetch badges', err)
    }
  }
}

onMounted(() => {
  fetchBadges()
  
  // Listen for message-read events to refresh badge count
  window.addEventListener('message-read', fetchBadges)
})

// Cleanup on unmount
onUnmounted(() => {
  window.removeEventListener('message-read', fetchBadges)
})

// Dynamic navigation items based on user role
const navigationItems = computed(() => {
  const role = authStore.user?.role
  
  // Student navigation
  if (role === 'student') {
    return [
      { name: 'Dashboard', icon: HomeIcon, path: '/student/dashboard' },
      { 
        name: 'Messages', 
        icon: EnvelopeIcon, 
        path: '/student/messages',
        badge: badges.value.messages 
      },
      { 
        name: 'Courses', 
        icon: BookOpenIcon, 
        path: '/student/courses',
        badge: badges.value.lessons
      },
      { 
        name: 'Documents', 
        icon: DocumentTextIcon, 
        path: '/student/documents',
        badge: badges.value.documents
      },
      { name: 'Schedule', icon: CalendarIcon, path: '/student/schedule' },
      { name: 'Attendance', icon: ClipboardDocumentCheckIcon, path: '/student/attendance' },
      { name: 'Exams', icon: AcademicCapIcon, path: '/student/exams' },
      { name: 'Profile', icon: UserCircleIcon, path: '/student/profile' },
    ]
  }
  
  // Teacher navigation
  if (role === 'teacher') {
    return [
      { name: 'Dashboard', icon: HomeIcon, path: '/teacher/dashboard' },
      { name: 'Schedule', icon: CalendarIcon, path: '/teacher/schedule' },
      { name: 'Attendance', icon: ClipboardDocumentCheckIcon, path: '/teacher/attendance' },
      { name: 'Grades', icon: AcademicCapIcon, path: '/teacher/grades' },
      { name: 'Profile', icon: UserCircleIcon, path: '/teacher/profile' },
    ]
  }
  
  // Admin navigation
  if (role === 'administration') {
    return [
      { name: 'Dashboard', icon: HomeIcon, path: '/admin/dashboard' },
      { name: 'Students', icon: AcademicCapIcon, path: '/admin/students' },
      { name: 'Teachers', icon: UserCircleIcon, path: '/admin/teachers' },
      { name: 'Specialties', icon: ClipboardDocumentCheckIcon, path: '/admin/specialties' },
      { name: 'Reports', icon: CalendarIcon, path: '/admin/reports' },
    ]
  }
  
  // Default fallback
  return []
})

const isActive = (path) => {
  return route.path === path
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
  emit('close')
}
</script>

<template>
  <div>
    <!-- Mobile overlay -->
    <div 
      v-if="open" 
      @click="$emit('close')" 
      class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
    ></div>

    <!-- Sidebar -->
    <aside 
      class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 transition-transform duration-200 flex flex-col h-full"
      :class="[
        open ? 'translate-x-0' : '-translate-x-full',
        'lg:translate-x-0'
      ]"
    >
      <!-- Header -->
      <div class="h-16 px-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between shrink-0">
        <span class="text-xl font-bold text-blue-600 dark:text-white">INSFP</span>
        <XMarkIcon 
          class="w-6 h-6 text-gray-500 dark:text-white cursor-pointer lg:hidden" 
          @click="$emit('close')" 
        />
      </div>

      <!-- Navigation -->
      <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <router-link 
          v-for="item in navigationItems" 
          :key="item.path" 
          :to="item.path"
          class="flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-colors relative"
          :class="isActive(item.path) ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'"
        >
          <div class="flex items-center">
            <component :is="item.icon" class="w-5 h-5 mr-3" />
            {{ item.name }}
          </div>
          <!-- Red dot indicator -->
          <span 
            v-if="item.badge && item.badge > 0" 
            class="w-2 h-2 bg-red-500 rounded-full animate-pulse"
          ></span>
        </router-link>
      </nav>

      <!-- Footer -->
      <div class="p-4 border-t border-gray-200 dark:border-gray-700 shrink-0 space-y-2">
        <div class="flex items-center gap-3 mb-3 px-4">
          <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center shrink-0">
            <span class="text-blue-600 dark:text-blue-400 font-semibold text-xs">
              {{ userDisplay.name?.charAt(0)?.toUpperCase() || 'U' }}
            </span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
              {{ userDisplay.name || 'User' }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 capitalize truncate">
              {{ userDisplay.role }}
            </p>
          </div>
        </div>
        <button 
          @click="handleLogout" 
          class="w-full flex items-center px-4 py-3 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
        >
          <ArrowLeftOnRectangleIcon class="w-5 h-5 mr-3" />
          Logout
        </button>
      </div>
    </aside>
  </div>
</template>
