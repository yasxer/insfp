<script setup>
import { storeToRefs } from 'pinia'
import { useThemeStore } from '@/stores/theme'
import { SunIcon, MoonIcon } from '@heroicons/vue/24/outline'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const themeStore = useThemeStore()
const { isDark } = storeToRefs(themeStore)
const { toggleTheme } = themeStore
</script>

<template>
  <button
    class="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-navy-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gold-400"
    :aria-label="isDark ? t('theme.toLight') : t('theme.toDark')"
    :title="isDark ? t('theme.light') : t('theme.dark')"
    @click="toggleTheme"
  >
    <SunIcon v-if="isDark" class="h-5 w-5" />
    <MoonIcon v-else class="h-5 w-5" />
  </button>
</template>
