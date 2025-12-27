<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [Number, String],
    required: true
  },
  change: {
    type: Number,
    default: 0
  },
  changeText: {
    type: String,
    default: 'vs mois dernier'
  },
  icon: {
    type: Object,
    required: true
  },
  trend: {
    type: String,
    default: 'up', // 'up' or 'down'
    validator: (value) => ['up', 'down'].includes(value)
  },
  loading: {
    type: Boolean,
    default: false
  }
})

import { ArrowTrendingUpIcon, ArrowTrendingDownIcon } from '@heroicons/vue/24/solid'
</script>

<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-200">
    <div class="flex items-start justify-between">
      <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ title }}</p>
        <div v-if="loading" class="mt-2 h-10 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-24"></div>
        <h3 v-else class="mt-2 text-4xl font-bold text-gray-900 dark:text-white">{{ value }}</h3>
      </div>
      <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
        <component :is="icon" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
      </div>
    </div>
    
    <div v-if="!loading" class="mt-4 flex items-center text-sm">
      <span 
        class="flex items-center font-medium px-2 py-0.5 rounded-full"
        :class="[
          trend === 'up' 
            ? 'text-green-700 bg-green-100 dark:text-green-400 dark:bg-green-900/30' 
            : 'text-red-700 bg-red-100 dark:text-red-400 dark:bg-red-900/30'
        ]"
      >
        <component 
          :is="trend === 'up' ? ArrowTrendingUpIcon : ArrowTrendingDownIcon" 
          class="w-4 h-4 mr-1" 
        />
        {{ Math.abs(change) }}%
      </span>
      <span class="ml-2 text-gray-500 dark:text-gray-400">{{ changeText }}</span>
    </div>
    
    <div v-else class="mt-4 h-6 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-32"></div>
  </div>
</template>
