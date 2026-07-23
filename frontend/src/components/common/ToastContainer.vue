<template>
  <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none">
    <TransitionGroup name="toast">
      <ToastNotification
        v-for="toast in toastStore.toasts"
        :key="toast.id"
        :toast="toast"
        @close="toastStore.remove"
      />
    </TransitionGroup>
  </div>
</template>

<script setup>
import { useToastStore } from '@/stores/toast'
import ToastNotification from './ToastNotification.vue'

const toastStore = useToastStore()
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.25s ease;
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(2rem);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(2rem);
}
</style>
