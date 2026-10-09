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
    type: [Object, Function],
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
  <div class="group relative overflow-hidden bg-white dark:bg-gray-900 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-800 transition hover:shadow-md hover:-translate-y-0.5 duration-200">
    <span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-navy-600 via-teal-600 to-gold-500 opacity-80" aria-hidden="true"></span>
    <div class="flex items-start justify-between gap-3">
      <div class="min-w-0">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ title }}</p>
        <div v-if="loading" class="mt-2 h-9 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-24"></div>
        <p v-else class="mt-1.5 font-serif text-3xl font-bold leading-none text-navy-800 dark:text-white">{{ value }}</p>
      </div>
      <div class="p-2.5 bg-navy-50 dark:bg-navy-900/40 rounded-lg transition-colors group-hover:bg-teal-50 dark:group-hover:bg-teal-900/30">
        <component :is="icon" class="w-6 h-6 text-navy-600 dark:text-navy-200 transition-colors group-hover:text-teal-600 dark:group-hover:text-teal-300" />
      </div>
    </div>
    
    <div v-if="!loading" class="mt-4 flex items-center text-sm">
      <span 
        class="flex items-center font-medium px-2 py-0.5 rounded-full"
        :class="[
          trend === 'up' 
            ? 'text-teal-700 bg-teal-50 dark:text-teal-300 dark:bg-teal-900/30' 
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
