<script setup>
defineProps({
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value)
  }
})

const sizeClasses = {
  small: 'h-6 w-6 border-2',
  medium: 'h-12 w-12 border-b-2',
  large: 'h-16 w-16 border-4'
}
</script>

<template>
  <div class="flex justify-center items-center p-4">
    <div 
      class="animate-spin rounded-full border-blue-500 border-t-transparent"
      :class="sizeClasses[size]"
    ></div>
  </div>
</template>
