<script setup>
import { computed, ref, watchEffect } from 'vue'
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend } from 'chart.js'
import { useTheme } from '@/composables/useTheme'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

const props = defineProps({
  data: {
    type: Array,
    default: () => []
  }
})

const { isDark } = useTheme()
const chartKey = ref(0)

// Force chart re-render when theme changes
watchEffect(() => {
  if (isDark.value !== undefined) {
    chartKey.value++
  }
})

// Trigger animation when data changes
watchEffect(() => {
  if (props.data && props.data.length > 0) {
    chartKey.value++
  }
})

const chartData = computed(() => {
  return {
    labels: props.data.map(item => item.name),
    datasets: [{
      label: 'Enseignants',
      data: props.data.map(item => item.count),
      backgroundColor: isDark.value ? '#60a5fa' : '#3b82f6',
      borderRadius: 8,
      barThickness: 40,
      hoverBackgroundColor: isDark.value ? '#93c5fd' : '#2563eb'
    }]
  }
})

const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  animation: {
    duration: 750,
    easing: 'easeInOutQuart'
  },
  plugins: {
    legend: { display: false },
    title: { 
      display: false, 
      text: 'Enseignants par Spécialité' 
    },
    tooltip: {
      backgroundColor: isDark.value ? '#1f2937' : '#ffffff',
      titleColor: isDark.value ? '#f3f4f6' : '#111827',
      bodyColor: isDark.value ? '#d1d5db' : '#4b5563',
      borderColor: isDark.value ? '#374151' : '#e5e7eb',
      borderWidth: 1,
      padding: 12,
      displayColors: false
    }
  },
  scales: {
    x: { 
      beginAtZero: true,
      grid: {
        color: isDark.value ? '#374151' : '#e5e7eb',
        drawBorder: false
      },
      ticks: {
        color: isDark.value ? '#9ca3af' : '#6b7280'
      }
    },
    y: {
      grid: {
        display: false
      },
      ticks: {
        color: isDark.value ? '#9ca3af' : '#6b7280'
      }
    }
  }
}))
</script>

<template>
  <div :key="chartKey" class="h-80 w-full">
    <Bar :data="chartData" :options="chartOptions" />
  </div>
</template>
