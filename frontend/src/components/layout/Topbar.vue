<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import ThemeToggle from '@/components/common/ThemeToggle.vue'
import { Bars3Icon } from '@heroicons/vue/24/outline'

const emit = defineEmits(['toggle-sidebar'])
const authStore = useAuthStore()

const userInitials = computed(() => authStore.user?.name?.charAt(0).toUpperCase() || 'U')
</script>

<template>
  <header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 transition-colors duration-200">
    <div class="flex items-center justify-between h-full px-6">
      <!-- Left section -->
      <div class="flex items-center">
        <button 
          @click="$emit('toggle-sidebar')" 
          class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 lg:hidden text-gray-600 dark:text-gray-300 transition-colors"
        >
          <Bars3Icon class="w-6 h-6" />
        </button>
      </div>

      <!-- Right section -->
      <div class="flex items-center space-x-4">
        <ThemeToggle />
        
        <div class="flex items-center space-x-3 pl-4 border-l border-gray-200 dark:border-gray-700">
          <div class="hidden sm:block text-right">
            <p class="text-sm font-medium text-gray-900 dark:text-white">
              {{ authStore.user?.name || 'User' }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
              Student
            </p>
          </div>
          
          <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold shadow-sm">
            {{ userInitials }}
          </div>
        </div>
      </div>
    </div>
  </header>
</template>
