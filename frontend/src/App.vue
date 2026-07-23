<script setup>
import { useThemeStore } from '@/stores/theme'
import ToastContainer from '@/components/common/ToastContainer.vue'
import GlobalLoader from '@/components/common/GlobalLoader.vue'

// Instantiating the store applies the persisted/system theme on app start
useThemeStore()
</script>

<template>
  <GlobalLoader />
  <router-view />
  <ToastContainer />
</template>
