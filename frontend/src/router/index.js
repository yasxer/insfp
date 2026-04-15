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
    redirect: '/student/dashboard',
    children: [
        {
          path: 'homeworks',
          name: 'student.homeworks',
          component: () => import('../views/student/Homeworks.vue')
        },
      {
        path: 'dashboard',
        name: 'StudentDashboard',
        component: () => import('@/views/student/Dashboard.vue')
      },
      {
        path: 'messages',
        name: 'StudentMessages',
        component: () => import('@/views/student/Messages.vue')
      },
      {
        path: 'courses',
        name: 'StudentCourses',
        component: () => import('@/views/student/Courses.vue')
      },
      {
        path: 'documents',
        name: 'StudentDocuments',
        component: () => import('@/views/student/Documents.vue')
      },
      {
        path: 'deliberations',
        name: 'StudentDeliberations',
        component: () => import('@/views/student/Deliberations.vue')
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
    path: '/teacher',
    component: () => import('@/components/layout/DashboardLayout.vue'),
    meta: { requiresAuth: true, role: 'teacher' },
    redirect: '/teacher/dashboard',
    children: [
        {
          path: 'homeworks',
          name: 'teacher.homeworks',
          component: () => import('../views/teacher/Homeworks.vue')
        },
        {
          path: 'homeworks/:id',
          name: 'teacher.homeworkDetail',
          component: () => import('../views/teacher/HomeworkDetail.vue')
        },

      {
        path: 'dashboard',
        name: 'TeacherDashboard',
        component: () => import('@/views/teacher/Dashboard.vue')
      },
      {
        path: 'courses',
        name: 'TeacherCourses',
        component: () => import('@/views/teacher/Courses.vue')
      },
      {
        path: 'modules',
        name: 'TeacherModules',
        component: () => import('@/views/teacher/Modules.vue')
      },
      {
        path: 'modules/:id/students',
        name: 'TeacherModuleStudents',
        component: () => import('@/views/teacher/ModuleStudents.vue')
      },
      {
        path: 'schedule',
        name: 'TeacherSchedule',
        component: () => import('@/views/teacher/Schedule.vue')
      },
      {
        path: 'messages',
        name: 'TeacherMessages',
        component: () => import('@/views/teacher/Messages.vue')
      },
      {
        path: 'documents',
        name: 'TeacherDocuments',
        component: () => import('@/views/teacher/Documents.vue')
      },
      {
        path: 'attendance',
        name: 'TeacherAttendance',
        component: () => import('@/views/teacher/Attendance.vue')
      },
      {
        path: 'attendance/session/:scheduleId/:date',
        name: 'TeacherMarkAttendance',
        component: () => import('@/views/teacher/MarkAttendance.vue')
      },
      {
        path: 'exams',
        name: 'TeacherExams',
        component: () => import('@/views/teacher/Exams.vue')
      },
      {
        path: 'exams/:id/grading',
        name: 'TeacherGrading',
        component: () => import('@/views/teacher/Grading.vue')
      },
      {
        path: 'profile',
        name: 'TeacherProfile',
        component: () => import('@/views/teacher/Profile.vue')
      }
    ]
  },
  {
    path: '/admin',
    component: () => import('@/components/layout/DashboardLayout.vue'),
    meta: { requiresAuth: true, role: 'administration' },
    redirect: '/admin/dashboard',
    children: [

      {
        path: 'dashboard',
        name: 'AdminDashboard',
        component: () => import('@/views/admin/Dashboard.vue')
      },
      {
        path: 'deliberations',
        name: 'AdminDeliberations',
        component: () => import('@/views/admin/Deliberations.vue')
      },
      {
        path: 'students',
        name: 'AdminStudents',
        component: () => import('@/views/admin/Students.vue')
      },
      {
        path: 'students/:id',
        name: 'AdminStudentDetails',
        component: () => import('@/views/admin/StudentDetails.vue')
      },
      {
        path: 'teachers',
        name: 'AdminTeachers',
        component: () => import('@/views/admin/Teachers.vue')
      },
      {
        path: 'teachers/:id',
        name: 'AdminTeacherDetails',
        component: () => import('@/views/admin/TeacherDetails.vue')
      },
      {
        path: 'specialties',
        name: 'AdminSpecialties',
        component: () => import('@/views/admin/Specialties.vue')
      },
      {
        path: 'specialties/:id',
        name: 'AdminSpecialtyDetails',
        component: () => import('@/views/admin/SpecialtyDetails.vue')
      },
      {
        path: 'schedule',
        name: 'AdminSchedule',
        component: () => import('@/views/admin/Schedule.vue')
      },
      {
        path: 'sessions',
        name: 'AdminSessions',
        component: () => import('@/views/admin/Sessions.vue')
      },
      {
        path: 'registration-generator',
        name: 'AdminRegistrationGenerator',
        component: () => import('@/views/admin/RegistrationGenerator.vue')
      },
      {
        path: 'files',
        name: 'AdminFiles',
        component: () => import('@/views/admin/Files.vue')
      },
      {
        path: 'exams',
        name: 'AdminExams',
        component: () => import('@/views/admin/Exams.vue')
      },
      {
        path: 'exams/:id/grades',
        name: 'AdminExamGrades',
        component: () => import('@/views/admin/ExamGrades.vue')
      },
      {
        path: 'profile',
        name: 'AdminProfile',
        component: () => import('@/views/admin/Profile.vue')
      }
    ]
  },
  {
    path: '/dashboard',
    redirect: (to) => {
      const authStore = useAuthStore()
      if (authStore.isStudent) return '/student/dashboard'
      if (authStore.isTeacher) return '/teacher/dashboard'
      if (authStore.isAdmin) return '/admin/dashboard'
      return '/login'
    }
  },
  {
    path: '/',
    name: 'Home',
    component: () => import('@/views/Home.vue')
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  // Check authentication first
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    console.log('Not authenticated, redirecting to login')
    next('/login')
    return
  }

  // Check if guest route but user is authenticated
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    console.log('Already authenticated, redirecting to dashboard')
    if (authStore.isStudent) {
      next('/student/dashboard')
    } else if (authStore.isTeacher) {
      next('/teacher/dashboard')
    } else if (authStore.isAdmin) {
      next('/admin/dashboard')
    } else {
      next('/dashboard')
    }
    return
  }

  // Check role-based access
  if (to.meta.role && authStore.userRole && to.meta.role !== authStore.userRole) {
    console.log(`Access denied: route requires ${to.meta.role}, user is ${authStore.userRole}`)
    if (authStore.isStudent) {
      next('/student/dashboard')
    } else if (authStore.isTeacher) {
      next('/teacher/dashboard')
    } else if (authStore.isAdmin) {
      next('/admin/dashboard')
    } else {
      next('/login')
    }
    return
  }

  // Check student profile completion (but only if user data is loaded)
  if (authStore.isAuthenticated && authStore.isStudent && authStore.user && to.path !== '/complete-profile') {
    if (!authStore.profileComplete) {
      console.log('Profile incomplete, redirecting to complete profile')
      next('/complete-profile')
      return
    }
  }

  // Prevent access to complete-profile if already complete
  if (to.path === '/complete-profile' && authStore.profileComplete) {
    console.log('Profile already complete, redirecting to dashboard')
    next('/student/dashboard')
    return
  }

  // Allow navigation
  next()
})

export default router

