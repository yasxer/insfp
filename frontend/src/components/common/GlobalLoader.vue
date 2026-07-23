<template>
  <Transition name="fade">
    <div v-if="loadingStore.isLoading" class="fixed top-0 left-0 right-0 z-[9998] h-1 overflow-hidden bg-transparent">
      <div class="h-full w-full bg-gradient-to-r from-indigo-500 via-blue-500 to-indigo-500 bg-[length:200%_100%] animate-loading-bar"></div>
    </div>
  </Transition>
</template>

<script setup>
import { useLoadingStore } from '@/stores/loading'

const loadingStore = useLoadingStore()
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@keyframes loading-bar {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

.animate-loading-bar {
  animation: loading-bar 1.2s linear infinite;
}
</style>
