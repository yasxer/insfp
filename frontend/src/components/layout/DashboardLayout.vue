<script setup>
import { ref, onMounted } from 'vue'
import Sidebar from './Sidebar.vue'
import Topbar from './Topbar.vue'

const sidebarOpen = ref(false)

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

// Open sidebar on desktop by default
onMounted(() => {
  if (window.innerWidth >= 1024) {
    sidebarOpen.value = true
  }
})
</script>

<template>
  <div class="flex h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
    <!-- Sidebar -->
    <Sidebar 
      :open="sidebarOpen" 
      @close="sidebarOpen = false" 
    />

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <Topbar @toggle-sidebar="toggleSidebar" />

      <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
