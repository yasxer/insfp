<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useNavigation } from '@/composables/useNavigation'
import { useI18n } from 'vue-i18n'
import studentApi from '@/api/endpoints/student'
import teacherApi from '@/api/endpoints/teacherPortal'
import adminApi from '@/api/endpoints/admin'
import { ArrowLeftOnRectangleIcon, XMarkIcon, Bars3Icon, ChevronDoubleLeftIcon } from '@heroicons/vue/24/outline'

defineProps({
  open: {
    type: Boolean,
    required: true
  }
})

const emit = defineEmits(['close'])

const router = useRouter()
const authStore = useAuthStore()
const { items, role, roleLabel, isActive } = useNavigation()
const { t } = useI18n()

// Desktop: icons only by default, the top button widens it. The choice is remembered.
// (On mobile the sidebar always opens full width.)
const COLLAPSED_KEY = 'sidebar-collapsed'
const readCollapsed = () => {
  try {
    return localStorage.getItem(COLLAPSED_KEY) !== 'false'
  } catch {
    return true
  }
}
const collapsed = ref(readCollapsed())
const toggleCollapsed = () => {
  collapsed.value = !collapsed.value
  try {
    localStorage.setItem(COLLAPSED_KEY, String(collapsed.value))
  } catch {
    // storage unavailable: keep the state for this visit only
  }
}

const badges = ref({
  messages: 0,
  lessons: 0,
  documents: 0,
  advancementReviews: 0
})

const userName = computed(() => authStore.userName || authStore.user?.email || t('layout.user'))
const initials = computed(() => userName.value
  .split(/\s+/)
  .filter(Boolean)
  .slice(0, 2)
  .map((part) => part[0].toUpperCase())
  .join('') || 'U')

const fetchBadges = async () => {
  if (role.value === 'student') {
    try {
      const [msgRes, lessonRes, docRes] = await Promise.all([
        studentApi.getUnreadMessagesCount(),
        studentApi.getNewLessonsCount(),
        studentApi.getNewDocumentsCount()
      ])
      badges.value = {
        ...badges.value,
        messages: msgRes.count || 0,
        lessons: lessonRes.count || 0,
        documents: docRes.count || 0
      }
    } catch (err) {
      console.error('Failed to fetch student badges', err)
    }
  } else if (role.value === 'teacher') {
    try {
      const [msgRes, docRes] = await Promise.all([
        teacherApi.getUnreadMessagesCount(),
        teacherApi.getNewDocumentsCount()
      ])
      badges.value = { ...badges.value, messages: msgRes.count || 0, documents: docRes.count || 0 }
    } catch (err) {
      console.error('Failed to fetch teacher badges', err)
    }
  } else if (role.value === 'administration') {
    try {
      const res = await adminApi.getAdvancementReviews('pending')
      badges.value.advancementReviews = res.pending_count || 0
    } catch (err) {
      console.error('Failed to fetch admin badges', err)
    }
  }
}

onMounted(() => {
  fetchBadges()
  // Refresh the counters when a message gets read
  window.addEventListener('message-read', fetchBadges)
})

onUnmounted(() => {
  window.removeEventListener('message-read', fetchBadges)
})

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
  emit('close')
}
</script>

<template>
  <div>
    <!-- Mobile overlay -->
    <Transition name="overlay">
      <div
        v-if="open"
        class="fixed inset-0 z-40 bg-navy-950/40 backdrop-blur-sm lg:hidden"
        @click="$emit('close')"
      ></div>
    </Transition>

    <aside
      class="fixed inset-y-0 start-0 z-50 flex h-full w-64 flex-col border-e border-gray-200 bg-white transition-[transform,width] duration-200 dark:border-gray-800 dark:bg-gray-900 lg:static lg:translate-x-0"
      :class="[open ? 'translate-x-0' : '-translate-x-full rtl:translate-x-full', collapsed ? 'lg:w-20' : 'lg:w-64']"
    >
      <!-- Top: logo + widen/narrow button -->
      <div
        class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-gray-100 px-4 dark:border-gray-800"
        :class="{ 'lg:justify-center lg:px-0': collapsed }"
      >
        <router-link to="/" class="flex min-w-0 items-center gap-3" :class="{ 'lg:hidden': collapsed }">
          <img src="/logo.png" alt="Logo INSFP" class="h-9 w-9 shrink-0 object-contain" />
          <span class="flex min-w-0 flex-col leading-tight">
            <span class="font-serif text-lg font-bold tracking-wide text-navy-700 dark:text-white">INSFP</span>
            <span class="truncate text-xs text-gray-500 dark:text-gray-400">{{ roleLabel || t('layout.platform') }}</span>
          </span>
        </router-link>

        <!-- Mobile: close -->
        <button class="rounded-md p-1.5 text-gray-500 hover:bg-gray-100 hover:text-navy-700 dark:text-gray-400 dark:hover:bg-gray-800 lg:hidden" :aria-label="t('layout.closeMenu')" @click="$emit('close')">
          <XMarkIcon class="h-6 w-6" />
        </button>

        <!-- Desktop: widen / narrow -->
        <button
          class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-navy-50 hover:text-navy-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white lg:inline-flex"
          :aria-label="collapsed ? t('layout.expandMenu') : t('layout.collapseMenu')"
          :title="collapsed ? t('layout.expandMenu') : t('layout.collapseMenu')"
          :aria-expanded="!collapsed"
          @click="toggleCollapsed"
        >
          <Bars3Icon v-if="collapsed" class="h-6 w-6" />
          <ChevronDoubleLeftIcon v-else class="h-5 w-5 rtl:rotate-180" />
        </button>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 space-y-1 overflow-y-auto overflow-x-hidden px-3 py-4" :aria-label="t('layout.mainMenu')">
        <router-link
          v-for="item in items"
          :key="item.path"
          :to="item.path"
          class="group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
          :class="[
            isActive(item.path)
              ? 'bg-navy-50 text-navy-700 dark:bg-navy-900/50 dark:text-white'
              : 'text-gray-600 hover:bg-gray-100 hover:text-navy-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white',
            { 'lg:justify-center lg:px-0': collapsed }
          ]"
          :title="collapsed ? item.name : undefined"
          :aria-current="isActive(item.path) ? 'page' : undefined"
          @click="$emit('close')"
        >
          <span
            v-if="isActive(item.path)"
            class="absolute start-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-e-full bg-gold-500"
            aria-hidden="true"
          ></span>
          <component
            :is="item.icon"
            class="h-5 w-5 shrink-0"
            :class="isActive(item.path) ? 'text-navy-600 dark:text-gold-400' : 'text-gray-400 group-hover:text-navy-600 dark:group-hover:text-gray-200'"
          />
          <span class="flex-1 truncate" :class="{ 'lg:hidden': collapsed }">{{ item.name }}</span>

          <template v-if="item.badge && badges[item.badge] > 0">
            <!-- count when wide, dot on the icon when narrow -->
            <span
              class="min-w-[1.25rem] rounded-full bg-gold-500 px-1.5 py-0.5 text-center text-[11px] font-bold leading-none text-navy-900"
              :class="{ 'lg:hidden': collapsed }"
            >{{ badges[item.badge] > 99 ? '99+' : badges[item.badge] }}</span>
            <span
              v-if="collapsed"
              class="absolute end-5 top-1.5 hidden h-2.5 w-2.5 rounded-full bg-gold-500 ring-2 ring-white dark:ring-gray-900 lg:block"
              aria-hidden="true"
            ></span>
          </template>
        </router-link>
      </nav>

      <!-- User -->
      <div class="shrink-0 border-t border-gray-100 p-3 dark:border-gray-800">
        <div class="flex items-center gap-3 px-2 py-2" :class="{ 'lg:justify-center lg:px-0': collapsed }" :title="collapsed ? userName : undefined">
          <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-teal-600 text-xs font-bold text-white">
            {{ initials }}
          </div>
          <div class="min-w-0 flex-1" :class="{ 'lg:hidden': collapsed }">
            <p class="truncate text-sm font-medium text-gray-900 dark:text-white">{{ userName }}</p>
            <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ roleLabel }}</p>
          </div>
        </div>
        <button
          class="mt-1 flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-colors hover:bg-red-50 hover:text-red-600 dark:text-gray-300 dark:hover:bg-red-900/20 dark:hover:text-red-300"
          :class="{ 'lg:justify-center lg:px-0': collapsed }"
          :title="collapsed ? t('layout.logout') : undefined"
          @click="handleLogout"
        >
          <ArrowLeftOnRectangleIcon class="h-5 w-5 shrink-0" />
          <span :class="{ 'lg:hidden': collapsed }">{{ t('layout.logout') }}</span>
        </button>
      </div>
    </aside>
  </div>
</template>

<style scoped>
.overlay-enter-active,
.overlay-leave-active {
  transition: opacity 0.2s ease;
}
.overlay-enter-from,
.overlay-leave-to {
  opacity: 0;
}
</style>
