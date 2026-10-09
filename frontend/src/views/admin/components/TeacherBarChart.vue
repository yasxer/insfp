<script setup>
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend } from 'chart.js'
import { storeToRefs } from 'pinia'
import { useThemeStore } from '@/stores/theme'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)
ChartJS.defaults.font.family = "'IBM Plex Sans', 'Segoe UI', sans-serif"

const props = defineProps({
  data: {
    type: Array,
    default: () => []
  }
})

const { isDark } = storeToRefs(useThemeStore())
const chartKey = ref(0)

// Force chart re-render when theme changes
watch(isDark, () => {
  chartKey.value++
})

// Trigger animation when data changes
watch(() => props.data, (data) => {
  if (data && data.length > 0) {
    chartKey.value++
  }
})

const chartData = computed(() => {
  return {
    labels: props.data.map(item => item.name),
    datasets: [{
      label: t('labels.charts.teachers'),
      data: props.data.map(item => item.count),
      backgroundColor: isDark.value ? '#3fa5a1' : '#0e7c7b',
      borderRadius: 8,
      barThickness: 40,
      hoverBackgroundColor: isDark.value ? '#72c1bd' : '#178c88'
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
      backgroundColor: isDark.value ? '#111a27' : '#ffffff',
      titleColor: isDark.value ? '#f3f4f6' : '#111827',
      bodyColor: isDark.value ? '#d1d5db' : '#4b5563',
      borderColor: isDark.value ? '#3c4757' : '#e1e6ed',
      borderWidth: 1,
      padding: 12,
      displayColors: false
    }
  },
  scales: {
    x: { 
      beginAtZero: true,
      grid: {
        color: isDark.value ? '#3c4757' : '#e1e6ed',
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
