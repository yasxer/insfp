<template>
  <div
    :class="[
      'flex items-start gap-3 w-full max-w-sm p-4 rounded-lg shadow-lg border pointer-events-auto',
      colors[toast.type]
    ]"
  >
    <component :is="icons[toast.type]" class="w-5 h-5 flex-shrink-0 mt-0.5" />
    <p class="text-sm font-medium flex-1">{{ toast.message }}</p>
    <button @click="$emit('close', toast.id)" class="flex-shrink-0 opacity-70 hover:opacity-100">
      <XMarkIcon class="w-4 h-4" />
    </button>
  </div>
</template>

<script setup>
import {
  CheckCircleIcon,
  ExclamationCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

defineProps({
  toast: { type: Object, required: true }
})

defineEmits(['close'])

const colors = {
  success: 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/30 dark:border-green-800 dark:text-green-300',
  error: 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/30 dark:border-red-800 dark:text-red-300',
  warning: 'bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-900/30 dark:border-amber-800 dark:text-amber-300',
  info: 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/30 dark:border-blue-800 dark:text-blue-300'
}

const icons = {
  success: CheckCircleIcon,
  error: ExclamationCircleIcon,
  warning: ExclamationTriangleIcon,
  info: InformationCircleIcon
}
</script>
