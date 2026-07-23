<template>
  <div class="animate-pulse">
    <div class="grid gap-3" :style="{ gridTemplateColumns: `repeat(${columns}, minmax(0, 1fr))` }">
      <div
        v-for="i in columns"
        :key="`head-${i}`"
        class="h-4 rounded bg-gray-300 dark:bg-gray-600"
      ></div>
    </div>
    <div
      v-for="row in rows"
      :key="`row-${row}`"
      class="grid gap-3 mt-4"
      :style="{ gridTemplateColumns: `repeat(${columns}, minmax(0, 1fr))` }"
    >
      <div
        v-for="col in columns"
        :key="`cell-${row}-${col}`"
        class="h-4 rounded bg-gray-200 dark:bg-gray-700"
      ></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  rows: { type: Number, default: 5 },
  columns: { type: Number, default: 4 }
})
</script>
