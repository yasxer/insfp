<script setup>
import { storeToRefs } from 'pinia'
import { useThemeStore } from '@/stores/theme'

const themeStore = useThemeStore()
const { isDark } = storeToRefs(themeStore)
const { toggleTheme } = themeStore
</script>

<template>
  <button 
    @click="toggleTheme" 
    class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors text-gray-600 dark:text-gray-300"
    :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
  >
    <span v-if="isDark" class="text-xl">☀️</span>
    <span v-else class="text-xl">🌙</span>
  </button>
</template>
