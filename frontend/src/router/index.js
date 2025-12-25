import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/auth/Login.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/auth/Register.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/complete-profile',
    name: 'CompleteProfile',
    component: () => import('@/views/student/CompleteProfile.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/student',
    component: () => import('@/components/layout/DashboardLayout.vue'),
    meta: { requiresAuth: true, role: 'student' },
    children: [
      {
        path: 'dashboard',
        name: 'StudentDashboard',
        component: () => import('@/views/student/Dashboard.vue')
      },
      {
        path: 'schedule',
        name: 'Schedule',
        component: () => import('@/views/student/Schedule.vue')
      },
      {
        path: 'attendance',
        name: 'Attendance',
        component: () => import('@/views/student/Attendance.vue')
      },
      {
        path: 'exams',
        name: 'Exams',
        component: () => import('@/views/student/Exams.vue')
      },
      {
        path: 'profile',
        name: 'Profile',
        component: () => import('@/views/student/Profile.vue')
      }
    ]
  },
  {
    path: '/',
    redirect: '/login'
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (authStore.isAuthenticated && authStore.isStudent && !authStore.profileComplete && to.path !== '/complete-profile') {
    // Student with incomplete profile, redirect to complete profile
    next('/complete-profile')
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next('/student/dashboard')
  } else {
    next()
  }
})

export default router
