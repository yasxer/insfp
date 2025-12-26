<script setup>
import { ref, onMounted } from 'vue'
import studentApi from '@/api/endpoints/student'
import Card from '@/components/common/Card.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { 
  DocumentTextIcon, 
  ArrowDownTrayIcon 
} from '@heroicons/vue/24/outline'

const loading = ref(true)
const documents = ref([])
const error = ref(null)

const loadDocuments = async () => {
  try {
    loading.value = true
    console.log('Loading documents...')
    const response = await studentApi.getDocuments()
    console.log('Documents response:', response)
    documents.value = response.data || []
    console.log('Documents loaded:', documents.value.length)
  } catch (err) {
    console.error('Failed to load documents:', err)
    error.value = 'Failed to load documents'
  } finally {
    loading.value = false
  }
}

const downloadDocument = async (doc) => {
  try {
    const response = await studentApi.downloadDocument(doc.id)
    
    // Create blob link to download
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', doc.file_name)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
    
    // Mark as viewed in list
    const index = documents.value.findIndex(d => d.id === doc.id)
    if (index !== -1) {
      documents.value[index].is_viewed = true
    }
  } catch (err) {
    console.error('Failed to download document:', err)
    alert('Failed to download file')
  }
}

onMounted(() => {
  loadDocuments()
})
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Documents</h1>

    <div v-if="loading" class="flex justify-center items-center h-64">
      <LoadingSpinner size="large" />
    </div>

    <div v-else-if="error" class="text-center py-12">
      <p class="text-red-600 dark:text-red-400">{{ error }}</p>
    </div>

    <Card v-else>
      <div v-if="documents.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div 
          v-for="doc in documents" 
          :key="doc.id"
          class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-md transition-shadow"
        >
          <div class="flex items-start justify-between mb-4">
            <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
              <DocumentTextIcon class="w-8 h-8 text-gray-600 dark:text-gray-300" />
            </div>
            <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
              {{ doc.created_at }}
            </span>
          </div>
          
          <h3 class="font-medium text-gray-900 dark:text-white mb-2 line-clamp-1" :title="doc.title">
            {{ doc.title }}
          </h3>
          
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 line-clamp-2 h-10">
            {{ doc.description || 'No description provided' }}
          </p>
          
          <button 
            @click="downloadDocument(doc)"
            class="w-full flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors"
          >
            <ArrowDownTrayIcon class="w-4 h-4" />
            Download
          </button>
        </div>
      </div>

      <div v-else class="text-center py-12 text-gray-500 dark:text-gray-400">
        <DocumentTextIcon class="w-16 h-16 mx-auto mb-4 opacity-30" />
        <p>No documents available yet.</p>
      </div>
    </Card>
  </div>
</template>
