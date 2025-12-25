<script setup>
defineProps({
  title: {
    type: String,
    default: ''
  },
  noPadding: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-200">
    <div v-if="title" class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ title }}</h3>
    </div>
    <div :class="{ 'p-6': !noPadding }">
      <slot />
    </div>
  </div>
</template>
