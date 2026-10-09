<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useNavigation } from '@/composables/useNavigation'
import ThemeToggle from '@/components/common/ThemeToggle.vue'
import LanguageSwitcher from '@/components/common/LanguageSwitcher.vue'
import { useI18n } from 'vue-i18n'
import { Bars3Icon, ChevronRightIcon } from '@heroicons/vue/24/outline'

defineEmits(['toggle-sidebar'])

const authStore = useAuthStore()
const { roleLabel, roleSpace, currentItem } = useNavigation()
const { t } = useI18n()

const userName = computed(() => authStore.userName || authStore.user?.email || t('layout.user'))
const initials = computed(() => userName.value
  .split(/\s+/)
  .filter(Boolean)
  .slice(0, 2)
  .map((part) => part[0].toUpperCase())
  .join('') || 'U')
</script>

<template>
  <header class="sticky top-0 z-30 h-16 shrink-0 border-b border-gray-200 bg-white/90 backdrop-blur dark:border-gray-800 dark:bg-gray-900/90">
    <div class="flex h-full items-center justify-between gap-4 px-4 sm:px-6">
      <div class="flex min-w-0 items-center gap-3">
        <button
          class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 lg:hidden"
          :aria-label="t('layout.openMenu')"
          @click="$emit('toggle-sidebar')"
        >
          <Bars3Icon class="h-6 w-6" />
        </button>
        <!-- Breadcrumb: the page itself shows the big title -->
        <nav class="flex min-w-0 items-center gap-2 text-sm" :aria-label="t('layout.breadcrumb')">
          <span class="hidden font-medium text-teal-600 dark:text-teal-400 sm:inline">{{ roleSpace }}</span>
          <ChevronRightIcon v-if="currentItem" class="hidden h-4 w-4 shrink-0 text-gray-400 rtl:rotate-180 sm:block" aria-hidden="true" />
          <span class="truncate font-semibold text-navy-800 dark:text-white">{{ currentItem?.name || 'INSFP' }}</span>
        </nav>
      </div>

      <div class="flex items-center gap-2 sm:gap-4">
        <LanguageSwitcher class="hidden sm:inline-flex" />
        <ThemeToggle />
        <div class="flex items-center gap-3 border-s border-gray-200 ps-3 dark:border-gray-700 sm:ps-4">
          <div class="hidden text-end sm:block">
            <p class="max-w-[12rem] truncate text-sm font-medium text-gray-900 dark:text-white">{{ userName }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ roleLabel }}</p>
          </div>
          <div class="flex h-9 w-9 items-center justify-center rounded-full bg-navy-600 text-xs font-bold text-white ring-2 ring-navy-100 dark:ring-navy-900">
            {{ initials }}
          </div>
        </div>
      </div>
    </div>
  </header>
</template>
