<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Mes Devoirs</h1>
        <p class="text-gray-500 text-sm mt-1">Consultez et soumettez vos travaux</p>
      </div>
    </div>

    <!-- Error/Success Messages -->
    <div v-if="error" class="bg-red-50 text-red-600 p-4 rounded-lg flex items-center">
      <ExclamationCircleIcon class="mr-2 w-5 h-5" />
      {{ error }}
    </div>
    
    <div v-if="success" class="bg-green-50 text-green-600 p-4 rounded-lg flex items-center shadow-sm">
      <CheckCircleIcon class="mr-2 w-5 h-5" />
      {{ success }}
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6" v-if="!loading && homeworks.length > 0">
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
          <ClipboardDocumentListIcon class="text-2xl" />
        </div>
        <div>
          <p class="text-sm font-medium text-gray-500">Total des devoirs</p>
          <p class="text-xl font-bold text-gray-800">{{ homeworks.length }}</p>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="p-3 bg-red-50 text-red-600 rounded-lg">
          <ClockIcon class="text-2xl" />
        </div>
        <div>
          <p class="text-sm font-medium text-gray-500">À rendre</p>
          <p class="text-xl font-bold text-gray-800">{{ pendingCount }}</p>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="p-3 bg-green-50 text-green-600 rounded-lg">
          <CheckCircleIcon class="w-6 h-6" />
        </div>
        <div>
          <p class="text-sm font-medium text-gray-500">Terminés</p>
          <p class="text-xl font-bold text-gray-800">{{ completedCount }}</p>
        </div>
      </div>
    </div>

    <!-- Filter/Sort Tabs -->
    <div class="flex space-x-1 bg-white p-1 rounded-lg border border-gray-200 mt-6" style="width: fit-content;" v-if="!loading && homeworks.length > 0">
      <button @click="currentFilter = 'all'" :class="currentFilter === 'all' ? 'bg-blue-600 text-white shadow' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'" class="px-4 py-2 rounded-md text-sm font-medium transition-colors">
        Tous
      </button>
      <button @click="currentFilter = 'pending'" :class="currentFilter === 'pending' ? 'bg-blue-600 text-white shadow' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'" class="px-4 py-2 rounded-md text-sm font-medium transition-colors">
        À rendre
      </button>
      <button @click="currentFilter = 'submitted'" :class="currentFilter === 'submitted' ? 'bg-blue-600 text-white shadow' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'" class="px-4 py-2 rounded-md text-sm font-medium transition-colors border-none">
        Terminés
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center p-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[blue-600]"></div>
    </div>

    <div v-else-if="filteredHomeworks.length > 0" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <!-- Homework Card -->
      <div 
        v-for="hw in filteredHomeworks" 
        :key="hw.id"
        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group relative"
      >
        <!-- Status Indicator Band -->
        <div class="absolute top-0 left-0 w-1.5 h-full" :class="getStatusColorBand(hw)"></div>
        
        <div class="p-6 pl-8 flex-grow">
          <div class="flex justify-between items-start mb-3">
            <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1.5 rounded-full border border-indigo-100">
              {{ hw.module }}
            </span>
            <div class="flex flex-col items-end">
              <span class="flex items-center text-xs font-bold px-2 py-1 rounded" :class="getStatusBadge(hw)">
                <component :is="getStatusIcon(hw)" class="w-4 h-4 mr-1" />
                {{ getStatusText(hw) }}
              </span>
            </div>
          </div>
          
          <h2 class="text-xl font-bold text-gray-800 mb-2 mt-4">{{ hw.title }}</h2>
          <p class="text-sm font-medium text-gray-500 mb-3">Par: M/Mme {{ hw.teacher }}</p>
          <div class="bg-gray-50 p-4 rounded-lg text-sm text-gray-700 mb-4 whitespace-pre-wrap leading-relaxed border border-gray-100">
            {{ hw.description }}
          </div>
          
          <div v-if="hw.file_path" class="mb-4">
            <a :href="hw.file_path" target="_blank" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors bg-blue-50 px-3 py-2 rounded-lg hover:bg-blue-100">
              <ArrowDownTrayIcon class="w-5 h-5 mr-1.5" />
              Télécharger la pièce jointe
            </a>
          </div>
          
          <div class="flex items-center text-sm mt-6 pt-4 border-t border-gray-100 font-semibold" :class="getDueDateClass(hw.due_date)">
            <ClockIcon class="mr-1.5 w-5 h-5" />
            À rendre pour le: {{ formatDate(hw.due_date) }}
          </div>
        </div>

        <!-- Grade Display -->
        <div v-if="hw.status === 'graded'" class="bg-green-50 border-t border-green-100 p-4 pl-8 flex items-start gap-4">
          <div class="bg-white border-2 border-green-300 text-green-700 font-black text-xl px-3 py-1.5 rounded-lg shadow-sm">
            {{ hw.submission.grade }}/20
          </div>
          <div>
            <p class="text-xs font-bold text-green-800 uppercase tracking-wider mb-0.5">Commentaire du professeur</p>
            <p class="text-sm text-green-700">{{ hw.submission.feedback || 'Aucun commentaire détaillé.' }}</p>
          </div>
        </div>

        <!-- Submission Display -->
          <div v-if="hw.submission_type === 'in_person'" class="bg-emerald-50 border-t border-emerald-100 p-4 pl-8 flex justify-between items-center">
            <div class="flex items-center text-sm text-emerald-700 font-medium">
              <CheckCircleIcon class="text-emerald-500 w-5 h-5 mr-2" />
              Devoir à rendre en présentiel
            </div>
          </div>
        <div v-else-if="hw.submission_type !== 'in_person' && hw.status === 'submitted'" class="bg-gray-50 border-t border-gray-100 p-4 pl-8 flex justify-between items-center">
          <div class="flex items-center text-sm text-gray-600 font-medium">
            <CheckCircleIcon class="text-green-500 mr-2" />
            Devoir remis le {{ formatDate(hw.submission.submitted_at) }}
          </div>
          <button @click="openSubmitModal(hw)" class="text-sm text-blue-600 hover:text-blue-800 font-medium hover:underline">
            Modifier ma remise
          </button>
        </div>

        <!-- Submit Button -->
        <div v-else-if="hw.submission_type !== 'in_person'" class="bg-red-50/50 border-t border-red-100 p-4 pl-8">
          <button 
            @click="openSubmitModal(hw)"
            class="w-full py-2.5 bg-white border border-[blue-600] text-blue-600 font-semibold rounded-lg hover:bg-blue-600 hover:text-white transition-colors flex justify-center items-center group"
          >
            <ArrowUpTrayIcon class="mr-2 w-5 h-5 group-hover:-translate-y-1 transition-transform" />
            Remettre mon travail
          </button>
        </div>
        
      </div>
      
    </div>
    
    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="text-gray-300 mb-4 inline-flex p-4 rounded-full bg-gray-50">
        <InboxIcon class="w-10 h-10" />
      </div>
      <h3 class="text-lg font-bold text-gray-800">Aucun devoir</h3>
      <p class="text-gray-500 mt-2 max-w-sm mx-auto">Vous n'avez pas de devoirs correspondant à ce critère dans vos modules actuels.</p>
    </div>

    <!-- Submit Modal -->
    <div v-if="showSubmitModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
      <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-6 border-b border-gray-100 bg-gray-50/50">
          <div>
            <h2 class="text-xl font-bold text-gray-800">Remettre un travail</h2>
            <p class="text-sm text-gray-500 mt-1 font-medium">{{ activeHomework?.title }}</p>
          </div>
          <button @click="showSubmitModal = false" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-100">
            <XMarkIcon class=" w-5 h-5" />
          </button>
        </div>
        
        <form @submit.prevent="submitWork" class="p-6 space-y-5">
          <div v-if="activeHomework?.submission" class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-3 rounded-lg text-sm mb-4 flex items-start">
            <InformationCircleIcon class="text-yellow-600 mr-2 w-5 h-5" />
            Vous avez déjà remis ce devoir. Vous pouvez le modifier en renvoyant le formulaire.
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Votre réponse / texte</label>
            <textarea 
              v-model="form.submission_text" 
              rows="5" 
              class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors outline-none resize-y" 
              placeholder="Tapez votre réponse, vos remarques, ou un lien externe ici..."
            ></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pièce jointe</label>
            
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-300 rounded-xl hover:bg-gray-50 transition-colors"
                 :class="{'border-blue-600/50 bg-blue-50/50': form.file}">
              <div class="space-y-1 text-center">
                <span v-if="!form.file" class="material-icons text-gray-400 text-3xl mb-2">upload_file</span>
                <span v-else class="material-icons text-green-500 text-3xl mb-2">check_circle</span>
                
                <div class="flex text-sm text-gray-600 justify-center">
                  <label for="student-file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-700 focus-within:outline-none">
                    <span>{{ form.file ? 'Changer le fichier' : 'Parcourir les fichiers' }}</span>
                    <input id="student-file" type="file" class="sr-only" @change="handleFileUpload">
                  </label>
                </div>
                <p class="text-xs text-gray-500 mt-2" v-if="!form.file">
                  PDF, DOC, ZIP, Images (Max 10MB)
                </p>
                <p class="text-xs font-semibold text-gray-700 mt-2 flex items-center justify-center gap-1 truncate w-48 mx-auto" v-else>
                  {{ form.file.name }}
                </p>
              </div>
            </div>
            
            <div v-if="activeHomework?.submission?.file_path" class="mt-3 flex items-center gap-2 text-sm">
              <span class="text-gray-500">Fichier actuel:</span>
              <a :href="activeHomework.submission.file_path" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center">
                <LinkIcon class="w-4 h-4 mr-1" />
                  Voir la pièce
              </a>
            </div>
          </div>
          
          <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
            <button type="button" @click="showSubmitModal = false" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-xl transition-colors">
              Annuler
            </button>
            <button type="submit" :disabled="submitting || (!form.submission_text && !form.file && !activeHomework?.submission)" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors flex items-center shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
              <span v-if="submitting" class="material-icons animate-spin mr-2 text-[18px]">autorenew</span>
              <span v-else class="material-icons mr-2 text-[18px]">send</span>
              Envoyer mon travail
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ExclamationCircleIcon, CheckCircleIcon, ClipboardDocumentListIcon, ClockIcon, ArrowDownTrayIcon, XMarkIcon, InformationCircleIcon, ArrowUpTrayIcon, InboxIcon, LinkIcon } from '@heroicons/vue/24/outline'
import { ref, onMounted, computed } from 'vue'
import { studentHomeworkApi } from '@/api/endpoints/homework'

const homeworks = ref([])
const loading = ref(true)
const error = ref('')
const success = ref('')
const currentFilter = ref('all')

const pendingCount = computed(() => homeworks.value.filter(h => h.status === 'pending').length)
const completedCount = computed(() => homeworks.value.filter(h => h.status !== 'pending').length)

const filteredHomeworks = computed(() => {
  if (currentFilter.value === 'pending') {
    return homeworks.value.filter(hw => hw.status === 'pending')
  }
  if (currentFilter.value === 'submitted') {
    return homeworks.value.filter(hw => hw.status !== 'pending')
  }
  return homeworks.value
})

const showSubmitModal = ref(false)
const activeHomework = ref(null)
const submitting = ref(false)
const form = ref({
  submission_text: '',
  file: null
})

const fetchHomeworks = async () => {
  loading.value = true
  try {
    const response = await studentHomeworkApi.getHomeworks()
    homeworks.value = response.data.data
  } catch (err) {
    error.value = 'Erreur lors du chargement des devoirs'
    console.error(err)
  } finally {
    loading.value = false
  }
}

const openSubmitModal = (hw) => {
  activeHomework.value = hw
  form.value = {
    submission_text: hw.submission?.submission_text || '',
    file: null
  }
  showSubmitModal.value = true
}

const handleFileUpload = (e) => {
  if (e.target.files.length > 0) {
    form.value.file = e.target.files[0]
  }
}

const submitWork = async () => {
  submitting.value = true
  error.value = ''
  success.value = ''
  
  try {
    const formData = new FormData()
    if (form.value.submission_text) {
      formData.append('submission_text', form.value.submission_text)
    }
    if (form.value.file) {
      formData.append('file', form.value.file)
    }

    const res = await studentHomeworkApi.submitHomework(activeHomework.value.id, formData)
    
    success.value = 'Devoir remis avec succès !'
    showSubmitModal.value = false
    
    // Refresh list
    await fetchHomeworks()
    
    setTimeout(() => { success.value = '' }, 3000)
  } catch (err) {
    console.error(err)
    error.value = err.response?.data?.message || 'Erreur lors de la remise du devoir'
    showSubmitModal.value = false
  } finally {
    submitting.value = false
  }
}

const formatDate = (dateString, withTime = true) => {
  if (!dateString) return ''
  const options = { day: 'numeric', month: 'short', year: 'numeric' }
  if (withTime) {
    options.hour = '2-digit'
    options.minute = '2-digit'
  }
  return new Date(dateString).toLocaleDateString('fr-FR', options).replace(',', ' à')
}

const getDueDateClass = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  if (date < now) return 'text-red-600'
  
  const diffTime = Math.abs(date - now);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
  if (diffDays <= 2) return 'text-orange-500'
  
  return 'text-emerald-600'
}

const getStatusColorBand = (hw) => {
  if (hw.status === 'graded') return 'bg-green-500'
  if (hw.status === 'submitted') return 'bg-blue-500'
  
  const date = new Date(hw.due_date)
  if (date < new Date()) return 'bg-red-600'
  return 'bg-amber-400'
}

const getStatusBadge = (hw) => {
  if (hw.status === 'graded') return 'bg-green-100 text-green-700 border-green-200'
  if (hw.status === 'submitted') return 'bg-blue-100 text-blue-700 border-blue-200'
  
  const date = new Date(hw.due_date)
  if (date < new Date()) return 'bg-red-100 text-red-700 border-red-200'
  return 'bg-amber-100 text-amber-700 border-amber-200'
}

const getStatusText = (hw) => {
  if (hw.status === 'graded') return 'Noté'
  if (hw.status === 'submitted') return 'Remis'
  
  const date = new Date(hw.due_date)
  if (date < new Date()) return 'En retard'
  return 'À rendre'
}

const getStatusIcon = (hw) => {
  if (hw.status === 'graded') return 'done_all'
  if (hw.status === 'submitted') return 'done'
  return 'pending_actions'
}

onMounted(() => {
  fetchHomeworks()
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


