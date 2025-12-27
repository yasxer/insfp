<script setup>
import { computed } from 'vue'
import { Doughnut } from 'vue-chartjs'
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { useTheme } from '@/composables/useTheme'

ChartJS.register(ArcElement, Tooltip, Legend)

const props = defineProps({
  data: {
    type: Object, // Expecting { labels: [], values: [] } or similar, but prompt implies hardcoded or specific structure. 
                  // I'll make it flexible or use the example data structure if provided.
                  // The prompt example uses hardcoded data. I'll make it accept props.
    default: () => ({
      labels: ['1ère Année', '2ème Année', '3ème Année', '4ème Année'],
      values: [35, 30, 20, 15]
    })
  }
})

const { isDark } = useTheme()

const chartData = computed(() => {
  return {
    labels: props.data.labels,
    datasets: [{
      data: props.data.values,
      backgroundColor: [
        'rgba(59, 130, 246, 0.8)',
        'rgba(16, 185, 129, 0.8)',
        'rgba(245, 158, 11, 0.8)',
        'rgba(239, 68, 68, 0.8)'
      ],
      borderColor: [
        'rgba(59, 130, 246, 1)',
        'rgba(16, 185, 129, 1)',
        'rgba(245, 158, 11, 1)',
        'rgba(239, 68, 68, 1)'
      ],
      borderWidth: 2,
      hoverOffset: 4
    }]
  }
})

const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { 
      position: 'right',
      labels: {
        color: isDark.value ? '#d1d5db' : '#374151',
        padding: 20,
        font: {
          family: "'Inter', sans-serif",
          size: 12
        },
        usePointStyle: true,
        pointStyle: 'circle'
      }
    },
    tooltip: {
      backgroundColor: isDark.value ? '#1f2937' : '#ffffff',
      titleColor: isDark.value ? '#f3f4f6' : '#111827',
      bodyColor: isDark.value ? '#d1d5db' : '#4b5563',
      borderColor: isDark.value ? '#374151' : '#e5e7eb',
      borderWidth: 1,
      padding: 12,
      callbacks: {
        label: function(context) {
          return ` ${context.label}: ${context.raw}%`;
        }
      }
    }
  },
  cutout: '65%'
}))
</script>

<template>
  <div class="h-80 w-full flex items-center justify-center">
    <Doughnut :data="chartData" :options="chartOptions" />
  </div>
</template>
