<template>
  <div
    v-if="pagination && pagination.last_page > 1"
    class="flex items-center justify-between px-4 py-3 sm:px-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 rounded-b-lg"
  >
    <!-- Mobile -->
    <div class="flex flex-1 justify-between sm:hidden">
      <button
        @click="goTo(pagination.current_page - 1)"
        :disabled="pagination.current_page <= 1"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-600"
      >
        Previous
      </button>
      <button
        @click="goTo(pagination.current_page + 1)"
        :disabled="pagination.current_page >= pagination.last_page"
        class="relative ml-3 inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-600"
      >
        Next
      </button>
    </div>

    <!-- Desktop -->
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <p class="text-sm text-gray-700 dark:text-gray-300">
        Showing
        <span class="font-medium">{{ rangeStart }}</span>
        to
        <span class="font-medium">{{ rangeEnd }}</span>
        of
        <span class="font-medium">{{ pagination.total }}</span>
        results
      </p>
      <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
        <button
          @click="goTo(pagination.current_page - 1)"
          :disabled="pagination.current_page <= 1"
          class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-600"
        >
          <span class="sr-only">Previous</span>
          &laquo;
        </button>

        <button
          v-for="page in pageNumbers"
          :key="page"
          @click="goTo(page)"
          :class="[
            page === pagination.current_page
              ? 'z-10 bg-indigo-600 text-white border-indigo-600'
              : 'text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600',
            'relative inline-flex items-center px-4 py-2 text-sm font-medium border'
          ]"
        >
          {{ page }}
        </button>

        <button
          @click="goTo(pagination.current_page + 1)"
          :disabled="pagination.current_page >= pagination.last_page"
          class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-600"
        >
          <span class="sr-only">Next</span>
          &raquo;
        </button>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  pagination: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['change-page'])

const rangeStart = computed(() => {
  if (props.pagination.total === 0) return 0
  return (props.pagination.current_page - 1) * props.pagination.per_page + 1
})

const rangeEnd = computed(() => {
  return Math.min(props.pagination.current_page * props.pagination.per_page, props.pagination.total)
})

const pageNumbers = computed(() => {
  const { current_page, last_page } = props.pagination
  const delta = 2
  const start = Math.max(1, current_page - delta)
  const end = Math.min(last_page, current_page + delta)
  const pages = []
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

const goTo = (page) => {
  if (page < 1 || page > props.pagination.last_page || page === props.pagination.current_page) return
  emit('change-page', page)
}
</script>
