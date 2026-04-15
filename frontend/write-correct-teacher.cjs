const fs = require('fs');

const content = `<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Devoirs et Tâches</h1>
        <p class="text-gray-500 text-sm mt-1">Gérez les devoirs de vos modules</p>
      </div>
      <button 
        @click="showCreateModal = true"
        class="bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition-colors flex items-center shadow-lg shadow-blue-900/20 font-medium"
      >
        <PlusCircleIcon class="w-5 h-5 mr-2" />
        Nouveau Devoir
      </button>
    </div>

    <!-- Error/Success Messages -->
    <div v-if="error" class="bg-red-50 text-red-600 p-4 rounded-lg flex items-center">
      <ExclamationCircleIcon class="w-5 h-5 mr-2" />
      {{ error }}
    </div>
    
    <div v-if="success" class="bg-green-50 text-green-600 p-4 rounded-lg flex items-center shadow-sm">
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
        class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col h-full hover:shadow-md transition-shadow relative overflow-hidden group"
      >
        <div class="absolute top-0 left-0 w-full h-1" :class="getDueDateColorBand(hw.due_date)"></div>
        
        <div class="flex justify-between items-start mb-4 mt-2">
          <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full border border-indigo-100">
            {{ hw.module?.name }}
          </span>
          <div class="flex items-center text-xs font-semibold px-2.5 py-1 rounded-full" :class="getDueDateBadgeClass(hw.due_date)">
            <ClockIcon class="w-4 h-4 mr-1" />
            {{ formatDate(hw.due_date) }}
          </div>
        </div>
        
        <h3 class="text-lg font-bold text-gray-800 mb-2 leading-tight">{{ hw.title }}</h3>
        <p class="text-gray-500 text-sm mb-6 flex-grow line-clamp-3">{{ hw.description }}</p>
        
        <!-- Stats and Actions -->
        <div class="flex items-center justify-between border-t border-gray-100 pt-4 mt-auto">
          <div class="flex items-center text-sm">
            <div class="bg-gray-50 rounded-lg px-3 py-1.5 flex items-center border border-gray-100">
              <ClipboardDocumentCheckIcon class="w-4 h-4 text-gray-400 mr-1.5" />
              <span class="font-bold text-gray-800 mr-1">{{ hw.submissions_count }}</span> 
              <span class="text-gray-500">rendus</span>
            </div>
          </div>
          
          <router-link 
            :to="{ name: 'teacher.homeworkDetail', params: { id: hw.id }}"
            class="text-blue-600 hover:text-blue-700 font-semibold text-sm flex items-center group-hover:underline"
          >
            Consulter
            <ArrowRightIcon class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" />
          </router-link>
        </div>
      </div>
    </div>
    
    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
        <ClipboardDocumentListIcon class="w-8 h-8 text-gray-400" />
      </div>
      <h3 class="text-lg font-bold text-gray-800 mb-2">Aucun devoir créé</h3>
      <p class="text-gray-500 max-w-md mx-auto">Vous n'avez pas encore publié de devoirs pour vos étudiants. Cliquez sur le bouton "Nouveau Devoir" pour commencer.</p>
    </div>

    <!-- Create Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
      <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-100 bg-gray-50/50">
          <h2 class="text-xl font-bold text-gray-800">Ajouter un devoir</h2>
          <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-100">
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>
        
        <!-- Modal Body -->
        <form id="createHomeworkForm" @submit.prevent="submitCreate" class="p-6 overflow-y-auto space-y-5">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Module <span class="text-red-500">*</span></label>
            <select v-model="form.module_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors outline-none">
              <option value="" disabled>Sélectionner le module concerné</option>
              <option v-for="mod in modules" :key="mod.id" :value="mod.id">
                {{ mod.name }}
              </option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Titre du devoir <span class="text-red-500">*</span></label>
            <input v-model="form.title" type="text" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors outline-none" placeholder="Ex: Exercices pratiques sur les réseaux">
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description et consignes <span class="text-red-500">*</span></label>
            <textarea v-model="form.description" required rows="4" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors outline-none resize-none" placeholder="Décrivez ce que les étudiants doivent faire..."></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Date limite de rendu <span class="text-red-500">*</span></label>
            <input v-model="form.due_date" type="datetime-local" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors outline-none text-gray-700">
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Fichier joint <span class="text-sm font-normal text-gray-400">(Optionnel)</span></label>
            
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-300 rounded-xl hover:bg-gray-50 transition-colors"
                 :class="{'border-blue-600/50 bg-blue-50/50': form.file}">
              <div class="space-y-1 text-center flex flex-col items-center">
                <ArrowUpTrayIcon v-if="!form.file" class="w-8 h-8 text-gray-400 mb-2" />
                <SolidCheckCircleIcon v-else class="w-8 h-8 text-green-500 mb-2" />
                
                <div class="flex text-sm text-gray-600 justify-center">
                  <label for="file-upload" class="relative cursor-pointer font-medium text-blue-600 hover:text-blue-700 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-600">
                    <span>{{ form.file ? 'Changer de fichier' : 'Sélectionner un fichier' }}</span>
                    <input id="file-upload" name="file-upload" type="file" class="sr-only" @change="handleFileUpload">
                  </label>
                </div>
                <p class="text-xs text-gray-500 mt-2" v-if="!form.file">
                  PDF, DOC, ZIP (Max 10MB)
                </p>
                <p class="text-xs font-semibold text-gray-700 mt-2 truncate flex items-center justify-center gap-1" v-else>
                  {{ form.file.name }}
                </p>
              </div>
            </div>
          </div>
        </form>

        <!-- Modal Footer -->
        <div class="border-t border-gray-100 p-6 bg-gray-50 flex justify-end gap-3 rounded-b-2xl">
          <button type="button" @click="showCreateModal = false" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-xl transition-colors">
            Annuler
          </button>
          <button form="createHomeworkForm" type="submit" :disabled="submitting || !form.module_id" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors flex items-center shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
            <ArrowPathIcon v-if="submitting" class="w-4 h-4 animate-spin mr-2" />
            <PaperAirplaneIcon v-else class="w-4 h-4 mr-2" />
            Publier le devoir
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
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
    error.value = 'Erreur lors du chargement des devoirs'
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
    
    if (form.value.file) {
      formData.append('file', form.value.file)
    }

    const res = await teacherHomeworkApi.createHomework(formData)
    success.value = 'Devoir publié avec succès !'
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
  const formatted = new Date(dateString).toLocaleDateString('fr-FR', options)
  return formatted.replace(',', ' à')
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
  if (date < now) return 'bg-red-50 text-red-700 border-red-100'
  
  const diffTime = Math.abs(date - now);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
  if (diffDays <= 2) return 'bg-orange-50 text-orange-700 border-orange-100'
  
  return 'bg-emerald-50 text-emerald-700 border-emerald-100'
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
</style>`;

fs.writeFileSync('src/views/teacher/Homeworks.vue', content);
