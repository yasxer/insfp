<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ t('teacher.homeworks.devoirs_taches') }}</h1>
        <p class="text-gray-500 text-sm mt-1 dark:text-gray-400">{{ t('teacher.homeworks.gerez_devoirs_modules') }}</p>
      </div>
      <button 
        @click="showCreateModal = true"
        class="bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition-colors flex items-center shadow-lg shadow-blue-900/20 font-medium"
      >
        <PlusCircleIcon class="w-5 h-5 mr-2" />
        {{ t('teacher.homeworks.nouveau_devoir') }}
      </button>
    </div>

    <!-- Error/Success Messages -->
    <div v-if="error" class="bg-red-50 text-red-600 p-4 rounded-lg flex items-center dark:bg-red-900/30 dark:text-red-300">
      <ExclamationCircleIcon class="w-5 h-5 mr-2" />
      {{ error }}
    </div>
    
    <div v-if="success" class="bg-green-50 text-green-600 p-4 rounded-lg flex items-center shadow-sm dark:bg-green-900/30 dark:text-green-300">
      <CheckCircleIcon class="w-5 h-5 mr-2" />
      {{ success }}
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center p-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
    </div>

    <!-- Homeworks List -->
    <div v-else-if="homeworks.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="hw in homeworks" 
        :key="hw.id"
        class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col h-full hover:shadow-md transition-shadow relative overflow-hidden group dark:bg-gray-900 dark:border-gray-800"
      >
        <div class="absolute top-0 left-0 w-full h-1" :class="getDueDateColorBand(hw.due_date)"></div>
        
        <div class="flex justify-between items-start mb-4 mt-2">
          <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full border border-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800">
            {{ hw.module?.name }}
          </span>
          <div class="flex items-center text-xs font-semibold px-2.5 py-1 rounded-full" :class="getDueDateBadgeClass(hw.due_date)">
            <ClockIcon class="w-4 h-4 mr-1" />
            {{ formatDate(hw.due_date) }}
          </div>
        </div>
        
        <h3 class="text-lg font-bold text-gray-800 mb-2 leading-tight dark:text-gray-100">{{ hw.title }}</h3>
        <p class="text-gray-500 text-sm mb-6 flex-grow line-clamp-3 dark:text-gray-400">{{ hw.description }}</p>
        
        <!-- Stats and Actions -->
        <div class="flex items-center justify-between border-t border-gray-100 pt-4 mt-auto dark:border-gray-800">
          <div class="flex items-center text-sm">
            <div class="bg-gray-50 rounded-lg px-3 py-1.5 flex items-center border border-gray-100 dark:bg-gray-800/60 dark:border-gray-800">
              <ClipboardDocumentCheckIcon class="w-4 h-4 text-gray-400 mr-1.5" />
              <span class="font-bold text-gray-800 mr-1 dark:text-gray-100">{{ hw.submissions_count }}</span> 
              <span class="text-gray-500 dark:text-gray-400">{{ t('teacher.homeworks.rendus') }}</span>
            </div>
          </div>
          
          <router-link 
            :to="{ name: 'teacher.homeworkDetail', params: { id: hw.id }}"
            class="text-blue-600 hover:text-blue-700 font-semibold text-sm flex items-center group-hover:underline dark:text-blue-300"
          >
            {{ t('teacher.homeworks.consulter') }}
            <ArrowRightIcon class="rtl:rotate-180 w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" />
          </router-link>
        </div>
      </div>
    </div>
    
    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center dark:bg-gray-900 dark:border-gray-800">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4 dark:bg-gray-800/60">
        <ClipboardDocumentListIcon class="w-8 h-8 text-gray-400" />
      </div>
      <h3 class="text-lg font-bold text-gray-800 mb-2 dark:text-gray-100">{{ t('teacher.homeworks.aucun_devoir_cree') }}</h3>
      <p class="text-gray-500 max-w-md mx-auto dark:text-gray-400">{{ t('teacher.homeworks.vous_n_avez_pas_encore') }}</p>
    </div>

    <!-- Create Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
      <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden flex flex-col max-h-[90vh] dark:bg-gray-900">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-100 bg-gray-50/50 dark:border-gray-800">
          <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ t('teacher.homeworks.ajouter_devoir') }}</h2>
          <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>
        
        <!-- Modal Body -->
        <form id="createHomeworkForm" @submit.prevent="submitCreate" class="p-6 overflow-y-auto space-y-5">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5 dark:text-gray-200">{{ t('common.module') }} <span class="text-red-500">*</span></label>
            <select v-model="form.module_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors outline-none dark:bg-gray-800/60 dark:border-gray-700 dark:focus:bg-gray-900">
              <option value="" disabled>{{ t('teacher.homeworks.selectionner_module_concerne') }}</option>
              <option v-for="mod in modules" :key="mod.id" :value="mod.id">
                {{ mod.name }}
              </option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5 dark:text-gray-200">{{ t('teacher.homeworks.titre_devoir') }} <span class="text-red-500">*</span></label>
            <input v-model="form.title" type="text" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors outline-none dark:bg-gray-800/60 dark:border-gray-700 dark:focus:bg-gray-900" :placeholder="t('teacher.homeworks.ex_exercices_pratiques_reseaux')">
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5 dark:text-gray-200">{{ t('teacher.homeworks.description_consignes') }} <span class="text-red-500">*</span></label>
            <textarea v-model="form.description" required rows="4" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors outline-none resize-none dark:bg-gray-800/60 dark:border-gray-700 dark:focus:bg-gray-900" :placeholder="t('teacher.homeworks.decrivez_que_stagiaires_doivent_faire')"></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5 dark:text-gray-200">{{ t('teacher.homeworks.date_limite_rendu') }} <span class="text-red-500">*</span></label>
            <input v-model="form.due_date" type="datetime-local" :min="nowDateTime()" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors outline-none text-gray-700 dark:bg-gray-800/60 dark:border-gray-700 dark:focus:bg-gray-900 dark:text-gray-200">
          </div>
          
          <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5 dark:text-gray-200">{{ t('teacher.homeworks.type_rendu') }} <span class="text-red-500">*</span></label>
              <div class="flex space-x-6">
                <label class="flex items-center cursor-pointer">
                  <input type="radio" value="online" v-model="form.submission_type" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-600 dark:text-blue-300 dark:border-gray-600">
                  <span class="ml-2 mt-1 px-3 py-1 bg-blue-50 text-blue-700 text-sm font-medium rounded-full cursor-pointer dark:bg-blue-900/30 dark:text-blue-300">{{ t('teacher.homeworks.ligne') }}</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input type="radio" value="in_person" v-model="form.submission_type" class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-600 dark:text-emerald-300 dark:border-gray-600">
                  <span class="ml-2 mt-1 px-3 py-1 bg-emerald-50 text-emerald-700 text-sm font-medium rounded-full cursor-pointer dark:bg-emerald-900/30 dark:text-emerald-300">{{ t('teacher.homeworks.presentiel') }}</span>
                </label>
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5 dark:text-gray-200">{{ t('teacher.homeworks.fichier_joint') }} <span class="text-sm font-normal text-gray-400">{{ t('teacher.homeworks.optionnel') }}</span></label>
            
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-300 rounded-xl hover:bg-gray-50 transition-colors dark:border-gray-600 dark:hover:bg-gray-800"
                 :class="{'border-blue-600/50 bg-blue-50/50': form.file}">
              <div class="space-y-1 text-center flex flex-col items-center">
                <ArrowUpTrayIcon v-if="!form.file" class="w-8 h-8 text-gray-400 mb-2" />
                <SolidCheckCircleIcon v-else class="w-8 h-8 text-green-500 mb-2" />
                
                <div class="flex text-sm text-gray-600 justify-center dark:text-gray-300">
                  <label for="file-upload" class="relative cursor-pointer font-medium text-blue-600 hover:text-blue-700 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-600 dark:text-blue-300">
                    <span>{{ form.file ? t('teacher.homeworks.changer_fichier') : t('teacher.homeworks.selectionner_fichier') }}</span>
                    <input id="file-upload" name="file-upload" type="file" class="sr-only" @change="handleFileUpload">
                  </label>
                </div>
                <p class="text-xs text-gray-500 mt-2 dark:text-gray-400" v-if="!form.file">
                  {{ t('teacher.homeworks.pdf_doc_zip_max_10mb') }}
                </p>
                <p class="text-xs font-semibold text-gray-700 mt-2 truncate flex items-center justify-center gap-1 dark:text-gray-200" v-else>
                  {{ form.file.name }}
                </p>
              </div>
            </div>
          </div>
        </form>

        <!-- Modal Footer -->
        <div class="border-t border-gray-100 p-6 bg-gray-50 flex justify-end gap-3 rounded-b-2xl dark:border-gray-800 dark:bg-gray-800/60">
          <button type="button" @click="showCreateModal = false" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-xl transition-colors dark:text-gray-200 dark:bg-gray-900 dark:border-gray-600 dark:hover:bg-gray-800">
            {{ t('common.cancel') }}
          </button>
          <button form="createHomeworkForm" type="submit" :disabled="submitting || !form.module_id" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors flex items-center shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
            <ArrowPathIcon v-if="submitting" class="w-4 h-4 animate-spin mr-2" />
            <PaperAirplaneIcon v-else class="w-4 h-4 mr-2" />
            {{ t('teacher.homeworks.publier_devoir') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import { dateLocale } from '@/i18n'
const { t } = useI18n()
import { ref, onMounted } from 'vue'
import { nowDateTime } from '@/utils/dates'
import { teacherHomeworkApi } from '@/api/endpoints/homework'
import teacherApi from '@/api/endpoints/teacherPortal'
import { 
  PlusCircleIcon, 
  ExclamationCircleIcon, 
  CheckCircleIcon, 
  ClockIcon, 
  ClipboardDocumentCheckIcon, 
  ArrowRightIcon, 
  ClipboardDocumentListIcon, 
  XMarkIcon, 
  ArrowUpTrayIcon, 
  ArrowPathIcon, 
  PaperAirplaneIcon 
} from '@heroicons/vue/24/outline'
import { CheckCircleIcon as SolidCheckCircleIcon } from '@heroicons/vue/24/solid'

const homeworks = ref([])
const modules = ref([])
const loading = ref(true)
const error = ref('')
const success = ref('')

const showCreateModal = ref(false)
const submitting = ref(false)
const form = ref({
  module_id: '',
  title: '',
  description: '',
  due_date: '',
  file: null
})

const fetchHomeworks = async () => {
  loading.value = true
  try {
    const response = await teacherHomeworkApi.getHomeworks()
    homeworks.value = response.data.data
  } catch (err) {
    error.value = t('teacher.homeworks.erreur_lors_chargement_devoirs')
    console.error(err)
  } finally {
    loading.value = false
  }
}

const fetchModules = async () => {
  try {
    const response = await teacherApi.getModules()
    modules.value = response.data
  } catch (err) {
    console.error('Failed to fetch modules', err)
  }
}

const handleFileUpload = (e) => {
  if (e.target.files.length > 0) {
    form.value.file = e.target.files[0]
  }
}

const submitCreate = async () => {
  submitting.value = true
  error.value = ''
  
  try {
    const formData = new FormData()
    formData.append('module_id', form.value.module_id)
    formData.append('title', form.value.title)
    formData.append('description', form.value.description)
    formData.append('due_date', form.value.due_date)
      formData.append('submission_type', form.value.submission_type)
    
    if (form.value.file) {
      formData.append('file', form.value.file)
    }

    const res = await teacherHomeworkApi.createHomework(formData)
    success.value = t('teacher.homeworks.devoir_publie_avec_succes')
    showCreateModal.value = false
    
    // Reset form
    form.value = { module_id: '', title: '', description: '', due_date: '', file: null }
    
    // Refresh list
    await fetchHomeworks()
    
    setTimeout(() => { success.value = '' }, 4000)
  } catch (err) {
    console.error(err)
    error.value = err.response?.data?.message || 'Erreur lors de la création du devoir'
  } finally {
    submitting.value = false
  }
}

const formatDate = (dateString) => {
  const options = { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' }
  const formatted = new Date(dateString).toLocaleDateString(dateLocale(), options)
  return formatted.replace(',', ' ·')
}

const getDueDateColorBand = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  if (date < now) return 'bg-red-500' // Past due
  
  const diffTime = Math.abs(date - now);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
  if (diffDays <= 2) return 'bg-orange-500' // Expiring soon
  
  return 'bg-emerald-500' // Normal/Safe
}

const getDueDateBadgeClass = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  if (date < now) return 'bg-red-50 text-red-700 border-red-100 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800'
  
  const diffTime = Math.abs(date - now);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
  if (diffDays <= 2) return 'bg-orange-50 text-orange-700 border-orange-100 dark:bg-orange-900/30 dark:text-orange-300 dark:border-orange-800'
  
  return 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800'
}

onMounted(() => {
  fetchHomeworks()
  fetchModules()
})
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.2s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
</style>




