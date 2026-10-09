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
  <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 transition-colors duration-200">
    <div v-if="title" class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
      <h3 class="font-serif text-lg font-semibold text-navy-800 dark:text-white">{{ title }}</h3>
    </div>
    <div :class="{ 'p-6': !noPadding }">
      <slot />
    </div>
  </div>
</template>
