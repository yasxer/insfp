<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
import { ref, computed, onMounted, watch } from 'vue'
import apiClient from '@/api/axios'
import Card from '@/components/common/Card.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { 
  DocumentTextIcon, 
  ArrowUpTrayIcon,
  TrashIcon,
  ArrowDownTrayIcon,
  XMarkIcon,
  UserGroupIcon,
  AcademicCapIcon
} from '@heroicons/vue/24/outline'

// State
const loading = ref(true)
const uploading = ref(false)
const documents = ref([])
const showUploadModal = ref(false)
const error = ref(null)
const successMessage = ref(null)

// Sessions and Specialties for dropdown
const sessions = ref([])
const specialties = ref([])
const loadingSpecialties = ref(false)

// Upload form data
const uploadForm = ref({
  file: null,
  title: '',
  description: '',
  targetType: 'all_students', // all_teachers, all_students, session_students, specialty_students
  sessionId: null,
  specialtyIds: []
})

// Computed
const targetTypeOptions = [
  { value: 'all_teachers', label: 'Tous les formateurs', icon: UserGroupIcon },
  { value: 'all_students', label: 'Tous les stagiaires', icon: AcademicCapIcon },
  { value: 'session_students', label: 'Une session (toutes les spécialités)', icon: AcademicCapIcon },
  { value: 'specialty_students', label: 'Des spécialités précises', icon: AcademicCapIcon },
]

const showSessionSelect = computed(() => {
  return ['session_students', 'specialty_students'].includes(uploadForm.value.targetType)
})

const showSpecialtySelect = computed(() => {
  return uploadForm.value.targetType === 'specialty_students'
})

const canSubmit = computed(() => {
  if (!uploadForm.value.file || !uploadForm.value.title) return false
  if (showSessionSelect.value && !uploadForm.value.sessionId) return false
  if (showSpecialtySelect.value && uploadForm.value.specialtyIds.length === 0) return false
  return true
})

// Watch for session changes to load specialties
watch(() => uploadForm.value.sessionId, async (newSessionId) => {
  if (newSessionId && showSpecialtySelect.value) {
    await loadSpecialties(newSessionId)
  } else {
    specialties.value = []
    uploadForm.value.specialtyIds = []
  }
})

// Watch for target type changes
watch(() => uploadForm.value.targetType, () => {
  uploadForm.value.sessionId = null
  uploadForm.value.specialtyIds = []
  specialties.value = []
})

// Methods
const loadDocuments = async () => {
  try {
    loading.value = true
    const response = await apiClient.get('/api/admin/documents')
    documents.value = response.data.data || []
  } catch (err) {
    console.error('Failed to load documents:', err)
    error.value = t('admin.files.failed_to_load_documents')
  } finally {
    loading.value = false
  }
}

const loadSessions = async () => {
  try {
    const response = await apiClient.get('/api/admin/documents/sessions')
    sessions.value = response.data || []
  } catch (err) {
    console.error('Failed to load sessions:', err)
  }
}

const loadSpecialties = async (sessionId) => {
  try {
    loadingSpecialties.value = true
    const response = await apiClient.get(`/api/admin/documents/sessions/${sessionId}/specialties`)
    specialties.value = response.data || []
  } catch (err) {
    console.error('Failed to load specialties:', err)
    specialties.value = []
  } finally {
    loadingSpecialties.value = false
  }
}

const handleFileChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    uploadForm.value.file = file
    // Auto-fill title from filename if empty
    if (!uploadForm.value.title) {
      uploadForm.value.title = file.name.replace(/\.[^/.]+$/, '')
    }
  }
}

const openUploadModal = () => {
  resetForm()
  showUploadModal.value = true
}

const closeUploadModal = () => {
  showUploadModal.value = false
  resetForm()
}

const resetForm = () => {
  uploadForm.value = {
    file: null,
    title: '',
    description: '',
    targetType: 'all_students',
    sessionId: null,
    specialtyIds: []
  }
  specialties.value = []
}

const uploadDocument = async () => {
  if (!canSubmit.value) return

  try {
    uploading.value = true
    error.value = null

    const formData = new FormData()
    formData.append('file', uploadForm.value.file)
    formData.append('title', uploadForm.value.title)
    formData.append('description', uploadForm.value.description || '')
    formData.append('target_type', uploadForm.value.targetType)
    
    if (uploadForm.value.sessionId) {
      formData.append('session_id', uploadForm.value.sessionId)
    }
    
    if (uploadForm.value.specialtyIds.length > 0) {
      // Send specialty_ids as JSON string
      formData.append('specialty_ids', JSON.stringify(uploadForm.value.specialtyIds))
    }

    await apiClient.post('/api/admin/documents', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    successMessage.value = t('admin.files.document_uploaded_successfully')
    setTimeout(() => successMessage.value = null, 3000)
    
    closeUploadModal()
    await loadDocuments()
  } catch (err) {
    console.error('Failed to upload document:', err)
    error.value = err.response?.data?.message || 'Failed to upload document'
  } finally {
    uploading.value = false
  }
}

const deleteDocument = async (doc) => {
  if (!confirm(`Supprimer « ${doc.title} » ?`)) return

  try {
    await apiClient.delete(`/api/admin/documents/${doc.id}`)
    successMessage.value = t('admin.files.document_deleted_successfully')
    setTimeout(() => successMessage.value = null, 3000)
    await loadDocuments()
  } catch (err) {
    console.error('Failed to delete document:', err)
    error.value = t('admin.files.failed_to_delete_document')
  }
}

const downloadDocument = async (doc) => {
  try {
    const response = await apiClient.get(`/api/admin/documents/${doc.id}/download`, {
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', doc.file_name)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (err) {
    console.error('Failed to download document:', err)
    error.value = t('admin.files.failed_to_download_document')
  }
}

const getTargetBadgeClass = (targetType) => {
  switch (targetType) {
    case 'all_teachers':
      return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'
    case 'all_students':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
    case 'session_students':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
    case 'specialty_students':
      return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300'
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
  }
}

const toggleSpecialty = (specialtyId) => {
  const index = uploadForm.value.specialtyIds.indexOf(specialtyId)
  if (index === -1) {
    uploadForm.value.specialtyIds.push(specialtyId)
  } else {
    uploadForm.value.specialtyIds.splice(index, 1)
  }
}

const selectAllSpecialties = () => {
  uploadForm.value.specialtyIds = specialties.value.map(s => s.id)
}

const deselectAllSpecialties = () => {
  uploadForm.value.specialtyIds = []
}

onMounted(async () => {
  await Promise.all([loadDocuments(), loadSessions()])
})
</script>

<template>
  <div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ t('admin.files.gestion_fichiers') }}</h1>
      <button 
        @click="openUploadModal"
        class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
      >
        <ArrowUpTrayIcon class="w-5 h-5" />
        {{ t('admin.files.deposer_fichier') }}
      </button>
    </div>

    <!-- Success Message -->
    <div v-if="successMessage" class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-lg">
      {{ successMessage }}
    </div>

    <!-- Error Message -->
    <div v-if="error" class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-lg">
      {{ error }}
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center h-64">
      <LoadingSpinner size="large" />
    </div>

    <!-- Documents Table -->
    <Card v-else>
      <div v-if="documents.length > 0" class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700">
              <th class="text-left py-3 px-4 font-medium text-gray-600 dark:text-gray-400">{{ t('admin.files.nom_fichier') }}</th>
              <th class="text-left py-3 px-4 font-medium text-gray-600 dark:text-gray-400">{{ t('admin.files.destinataires') }}</th>
              <th class="text-left py-3 px-4 font-medium text-gray-600 dark:text-gray-400">{{ t('common.date') }}</th>
              <th class="text-right py-3 px-4 font-medium text-gray-600 dark:text-gray-400">{{ t('common.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr 
              v-for="doc in documents" 
              :key="doc.id"
              class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50"
            >
              <td class="py-3 px-4">
                <div class="flex items-center gap-3">
                  <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded">
                    <DocumentTextIcon class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                  </div>
                  <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ doc.title }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ doc.file_name }}</p>
                  </div>
                </div>
              </td>
              <td class="py-3 px-4">
                <span 
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="getTargetBadgeClass(doc.target_type)"
                >
                  {{ doc.target_description }}
                </span>
              </td>
              <td class="py-3 px-4 text-gray-600 dark:text-gray-400">
                {{ doc.created_at }}
              </td>
              <td class="py-3 px-4">
                <div class="flex items-center justify-end gap-2">
                  <button 
                    @click="downloadDocument(doc)"
                    class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                    :title="t('common.download')"
                  >
                    <ArrowDownTrayIcon class="w-5 h-5" />
                  </button>
                  <button 
                    @click="deleteDocument(doc)"
                    class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                    :title="t('common.delete')"
                  >
                    <TrashIcon class="w-5 h-5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="text-center py-12 text-gray-500 dark:text-gray-400">
        <DocumentTextIcon class="w-16 h-16 mx-auto mb-4 opacity-30" />
        <p>{{ t('admin.files.aucun_document_depose_moment') }}</p>
        <p class="text-sm mt-2">{{ t('admin.files.cliquez_deposer_fichier_ajouter_document') }}</p>
      </div>
    </Card>

    <!-- Upload Modal -->
    <div v-if="showUploadModal" class="fixed inset-0 z-50 flex items-center justify-center">
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-black/50" @click="closeUploadModal"></div>
      
      <!-- Modal Content -->
      <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ t('admin.files.deposer_fichier') }}</h2>
          <button 
            @click="closeUploadModal"
            class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
          >
            <XMarkIcon class="w-5 h-5 text-gray-500" />
          </button>
        </div>

        <!-- Body -->
        <div class="p-4 space-y-4">
          <!-- File Input -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ t('admin.files.fichier') }}
            </label>
            <input 
              type="file" 
              @change="handleFileChange"
              class="w-full text-sm text-gray-500 dark:text-gray-400
                file:mr-4 file:py-2 file:px-4
                file:rounded-lg file:border-0
                file:text-sm file:font-medium
                file:bg-blue-50 file:text-blue-700
                dark:file:bg-blue-900 dark:file:text-blue-300
                hover:file:bg-blue-100 dark:hover:file:bg-blue-800
                cursor-pointer"
            />
          </div>

          <!-- Title -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ t('admin.files.title') }}
            </label>
            <input 
              v-model="uploadForm.title"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :placeholder="t('admin.files.enter_document_title')"
            />
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ t('common.description') }}
            </label>
            <textarea 
              v-model="uploadForm.description"
              rows="2"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :placeholder="t('admin.files.description_facultatif')"
            ></textarea>
          </div>

          <!-- Target Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ t('admin.files.destinataires_2') }}
            </label>
            <div class="space-y-2">
              <label 
                v-for="option in targetTypeOptions" 
                :key="option.value"
                class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer
                  hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                :class="{ 'border-blue-500 bg-blue-50 dark:bg-blue-900/30': uploadForm.targetType === option.value }"
              >
                <input 
                  type="radio" 
                  v-model="uploadForm.targetType" 
                  :value="option.value"
                  class="sr-only"
                />
                <component :is="option.icon" class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400" />
                <span class="text-gray-900 dark:text-white">{{ option.label }}</span>
              </label>
            </div>
          </div>

          <!-- Session Select -->
          <div v-if="showSessionSelect">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ t('admin.files.session') }}
            </label>
            <select 
              v-model="uploadForm.sessionId"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg 
                bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option :value="null">{{ t('admin.files.choisir_session') }}</option>
              <option v-for="session in sessions" :key="session.id" :value="session.id">
                {{ session.name }}
              </option>
            </select>
          </div>

          <!-- Specialty Multi-Select -->
          <div v-if="showSpecialtySelect && uploadForm.sessionId">
            <div class="flex items-center justify-between mb-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ t('admin.files.specialites') }}
              </label>
              <div class="flex gap-2 text-xs">
                <button 
                  @click="selectAllSpecialties"
                  class="text-blue-600 hover:text-blue-700"
                >
                  {{ t('admin.files.tout_selectionner') }}
                </button>
                <span class="text-gray-400">|</span>
                <button 
                  @click="deselectAllSpecialties"
                  class="text-gray-600 hover:text-gray-700 dark:text-gray-400"
                >
                  {{ t('common.clear') }}
                </button>
              </div>
            </div>
            
            <div v-if="loadingSpecialties" class="text-center py-4">
              <LoadingSpinner size="small" />
            </div>
            
            <div v-else-if="specialties.length > 0" class="space-y-2 max-h-48 overflow-y-auto">
              <label 
                v-for="specialty in specialties" 
                :key="specialty.id"
                class="flex items-center p-2 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer
                  hover:bg-gray-50 dark:hover:bg-gray-700/50"
                :class="{ 'border-blue-500 bg-blue-50 dark:bg-blue-900/30': uploadForm.specialtyIds.includes(specialty.id) }"
              >
                <input 
                  type="checkbox" 
                  :checked="uploadForm.specialtyIds.includes(specialty.id)"
                  @change="toggleSpecialty(specialty.id)"
                  class="mr-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <div>
                  <span class="text-gray-900 dark:text-white">{{ specialty.name }}</span>
                  <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">({{ specialty.code }})</span>
                </div>
              </label>
            </div>
            
            <div v-else class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm">
              {{ t('admin.files.aucune_specialite_dans_session') }}
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
          <button 
            @click="closeUploadModal"
            class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
          >
            {{ t('common.cancel') }}
          </button>
          <button 
            @click="uploadDocument"
            :disabled="!canSubmit || uploading"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white rounded-lg transition-colors"
          >
            <LoadingSpinner v-if="uploading" size="small" class="text-white" />
            <ArrowUpTrayIcon v-else class="w-5 h-5" />
            {{ uploading ? t('admin.files.envoi') : t('admin.files.deposer') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
