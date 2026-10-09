import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
import {
  HomeIcon,
  CalendarIcon,
  ClipboardDocumentCheckIcon,
  AcademicCapIcon,
  UserCircleIcon,
  UsersIcon,
  EnvelopeIcon,
  BookOpenIcon,
  ClipboardDocumentListIcon,
  DocumentTextIcon,
  IdentificationIcon,
  ArrowTrendingUpIcon,
  RectangleStackIcon,
  ScaleIcon,
  Squares2X2Icon,
  FolderIcon,
  DocumentCheckIcon,
} from '@heroicons/vue/24/outline'

// Menu of each role. `key` is the nav.* translation, `badge` names a counter fetched by the sidebar.
const MENUS = {
  student: [
    { key: 'dashboard', icon: HomeIcon, path: '/student/dashboard' },
    { key: 'messages', icon: EnvelopeIcon, path: '/student/messages', badge: 'messages' },
    { key: 'courses', icon: BookOpenIcon, path: '/student/courses', badge: 'lessons' },
    { key: 'documents', icon: FolderIcon, path: '/student/documents', badge: 'documents' },
    { key: 'homeworks', icon: ClipboardDocumentListIcon, path: '/student/homeworks' },
    { key: 'schedule', icon: CalendarIcon, path: '/student/schedule' },
    { key: 'attendance', icon: ClipboardDocumentCheckIcon, path: '/student/attendance' },
    { key: 'exams', icon: DocumentCheckIcon, path: '/student/exams' },
    { key: 'deliberations', icon: ScaleIcon, path: '/student/deliberations' },
    { key: 'profile', icon: UserCircleIcon, path: '/student/profile' },
  ],
  teacher: [
    { key: 'dashboard', icon: HomeIcon, path: '/teacher/dashboard' },
    { key: 'messages', icon: EnvelopeIcon, path: '/teacher/messages', badge: 'messages' },
    { key: 'documents', icon: FolderIcon, path: '/teacher/documents', badge: 'documents' },
    { key: 'modules', icon: Squares2X2Icon, path: '/teacher/modules' },
    { key: 'courses', icon: BookOpenIcon, path: '/teacher/courses' },
    { key: 'homeworks', icon: ClipboardDocumentListIcon, path: '/teacher/homeworks' },
    { key: 'schedule', icon: CalendarIcon, path: '/teacher/schedule' },
    { key: 'attendance', icon: ClipboardDocumentCheckIcon, path: '/teacher/attendance' },
    { key: 'examsGrades', icon: DocumentCheckIcon, path: '/teacher/exams' },
  ],
  administration: [
    { key: 'dashboard', icon: HomeIcon, path: '/admin/dashboard' },
    { key: 'students', icon: AcademicCapIcon, path: '/admin/students' },
    { key: 'teachers', icon: UsersIcon, path: '/admin/teachers' },
    { key: 'specialties', icon: RectangleStackIcon, path: '/admin/specialties' },
    { key: 'sessions', icon: BookOpenIcon, path: '/admin/sessions' },
    { key: 'schedules', icon: CalendarIcon, path: '/admin/schedule' },
    { key: 'exams', icon: DocumentCheckIcon, path: '/admin/exams' },
    { key: 'deliberations', icon: ScaleIcon, path: '/admin/deliberations' },
    { key: 'passages', icon: ArrowTrendingUpIcon, path: '/admin/advancement-reviews', badge: 'advancementReviews' },
    { key: 'registrationNumbers', icon: IdentificationIcon, path: '/admin/registration-generator' },
    { key: 'files', icon: DocumentTextIcon, path: '/admin/files' },
    { key: 'profile', icon: UserCircleIcon, path: '/admin/profile' },
  ],
}

export function useNavigation() {
  const route = useRoute()
  const authStore = useAuthStore()

  const { t } = useI18n()

  const role = computed(() => authStore.user?.role)
  const items = computed(() => (MENUS[role.value] || []).map((item) => ({ ...item, name: t(`nav.${item.key}`) })))
  const roleLabel = computed(() => (role.value ? t(`roles.${role.value}.label`) : ''))
  const roleSpace = computed(() => (role.value ? t(`roles.${role.value}.space`) : ''))

  // A menu entry stays active on its sub-pages (e.g. /admin/students/12)
  const isActive = (path) => route.path === path || route.path.startsWith(path + '/')

  const currentItem = computed(() => items.value.find((item) => isActive(item.path)))

  return { items, role, roleLabel, roleSpace, isActive, currentItem }
}
