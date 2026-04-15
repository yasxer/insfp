<script setup>
import { ref, onMounted } from 'vue'
import teacherApi from '@/api/endpoints/teacherPortal'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { 
  DocumentTextIcon, 
  ArrowDownTrayIcon,
  TagIcon,
  CalendarIcon
} from '@heroicons/vue/24/outline'

const loading = ref(true)
const documents = ref([])
const error = ref(null)

const loadDocuments = async () => {
  try {
    loading.value = true
    const response = await teacherApi.getDocuments()
    documents.value = response.data || []
  } catch (err) {
    console.error('Failed to load documents:', err)
    error.value = 'Erreur lors du chargement des documents'
  } finally {
    loading.value = false
  }
}

const handleDownload = async (doc) => {
  try {
    const blob = await teacherApi.downloadDocument(doc.id)
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    
    // Set the filename from API data or fallback
    const filename = doc.original_filename || doc.file_name || doc.title || 'document'
    link.setAttribute('download', filename)
    
    document.body.appendChild(link)
    link.click()
    
    // Cleanup
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
  } catch (err) {
    console.error('Failed to download document:', err)
    error.value = 'Erreur lors du téléchargement du document'
    // Clear error after 3 seconds
    setTimeout(() => { error.value = null }, 3000)
  }
}

const getCategoryColor = (category) => {
  const colors = {
    'course': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    'exam': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    'administrative': 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
    'other': 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400'
  }
  return colors[category] || colors.other
}

const getCategoryLabel = (category) => {
  const labels = {
    'course': 'Cours',
    'exam': 'Examen',
    'administrative': 'Administratif',
    'other': 'Autre'
  }
  return labels[category] || category
}

const formatSize = (bytes) => {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

onMounted(() => {
  loadDocuments() // Mark as viewed handled gracefully if you implement a view logic later
})
</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Fichiers & Documents</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Documents partagés par l'administration
        </p>
      </div>
    </div>

    <!-- Stats/Filters bar could go here if needed -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
      <div class="flex items-center gap-4 text-sm font-medium text-gray-700 dark:text-gray-300">
        <DocumentTextIcon class="w-5 h-5 text-indigo-500" />
        <span>{{ documents.length }} Documents disponibles</span>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center items-center h-64">
      <LoadingSpinner size="lg" />
    </div>

    <div v-else-if="error" class="text-center py-12">
      <p class="text-red-600 dark:text-red-400">{{ error }}</p>
      <button @click="loadDocuments" class="mt-4 text-indigo-600 hover:underline">Réessayer</button>
    </div>

    <template v-else>
      <div v-if="documents.length === 0" class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <DocumentTextIcon class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" />
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Aucun document</h3>
        <p class="text-gray-500 mt-1">L'administration n'a pas encore partagé de fichiers avec vous.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="doc in documents" 
          :key="doc.id"
          class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow relative overflow-hidden group flex flex-col h-full"
        >
          <!-- Accent Line -->
          <div class="h-1.5 w-full bg-indigo-500 absolute top-0 left-0"></div>

          <div class="p-5 flex-1">
            <div class="flex items-start justify-between mb-4">
              <span :class="['px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider', getCategoryColor(doc.category)]">
                {{ getCategoryLabel(doc.category) }}
              </span>
              <div v-if="doc.is_new" class="flex items-center text-[10px] font-bold px-2 py-0.5 bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 rounded">
                NOUVEAU
              </div>
            </div>

            <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
              {{ doc.title }}
            </h3>

            <p v-if="doc.description" class="text-sm text-gray-500 dark:text-gray-400 mb-4 line-clamp-2">
              {{ doc.description }}
            </p>

            <div class="mt-auto space-y-2">
              <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                <TagIcon class="w-4 h-4 mr-2" />
                <span class="truncate">{{ doc.original_filename }}</span>
              </div>
              <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                <span class="flex items-center">
                  <DocumentTextIcon class="w-4 h-4 mr-1" />
                  {{ formatSize(doc.file_size) }}
                </span>
                <span class="flex items-center">
                  <CalendarIcon class="w-4 h-4 mr-1" />
                  {{ new Date(doc.created_at).toLocaleDateString('fr-FR') }}
                </span>
              </div>
            </div>
          </div>

          <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
            <button
              @click="handleDownload(doc)"
              class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors font-medium text-sm"
            >
              <ArrowDownTrayIcon class="w-5 h-5" />
              Télécharger
            </button>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>