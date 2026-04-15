<template>
  <div class="space-y-6">
    <div class="flex items-center gap-4">
      <router-link :to="{ name: 'teacher.homeworks' }" class="text-gray-500 hover:text-gray-700 bg-white p-2 rounded-lg border border-gray-100 shadow-sm transition-colors">
        <ArrowLeftIcon class=" w-5 h-5" />
      </router-link>
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Détails du Devoir</h1>
        <p class="text-gray-500 text-sm mt-1" v-if="homework">Module: {{ homework.module }}</p>
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

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center p-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[blue-600]"></div>
    </div>

    <div v-else-if="homework" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Homework Details Panel -->
      <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800">Informations</h2>
            <span class="bg-blue-50 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full border border-blue-100">
              {{ submissions.length }} Soumission(s)
            </span>
          </div>
          
          <div class="space-y-4">
            <div>
              <p class="text-sm font-medium text-gray-500 mb-1">Titre</p>
              <p class="text-gray-800 font-semibold">{{ homework.title }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Type de rendu</p>
                <div class="inline-flex items-center text-xs font-bold px-2.5 py-1 rounded-full border"
                  :class="homework.submission_type === 'online' ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100'">
                  {{ homework.submission_type === 'online' ? 'En ligne (Upload fichier)' : 'Présentiel' }}
                </div>
              </div>

              <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Date limite</p>
              <div class="flex items-center text-sm font-semibold" :class="getDueDateClass(homework.due_date)">
                <ClockIcon class="w-4 h-4 mr-1.5" />
                {{ formatDate(homework.due_date) }}
              </div>
            </div>
            
            <div>
              <p class="text-sm font-medium text-gray-500 mb-1">Description</p>
              <p class="text-gray-700 text-sm whitespace-pre-wrap">{{ homework.description }}</p>
            </div>
            
            <div v-if="homework.file_path" class="pt-4 border-t border-gray-100">
              <p class="text-sm font-medium text-gray-500 mb-3">Fichier source</p>
              <a :href="homework.file_path" target="_blank" class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors group">
                <div class="bg-blue-100 p-2 rounded-md mr-3 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                  <DocumentTextIcon class="w-5 h-5" />
                </div>
                <div class="flex-grow overflow-hidden">
                  <p class="text-sm font-medium text-gray-800 truncate">Sujet_Devoir.ext</p>
                  <p class="text-xs text-gray-500">Télécharger</p>
                </div>
                <ArrowDownTrayIcon class="text-gray-400 group-hover:text-red-600" />
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Submissions Panel -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-800">Travaux rendus</h2>
            <div class="flex items-center bg-white px-3 py-1.5 rounded-lg border border-gray-200">
              <MagnifyingGlassIcon class="text-gray-400 w-5 h-5 mr-2" />
              <input type="text" placeholder="Rechercher un étudiant..." class="border-none outline-none text-sm w-48 text-gray-700 bg-transparent placeholder-gray-400">
            </div>
          </div>

          <div v-if="submissions.length === 0" class="p-12 text-center">
            <InboxIcon class="w-10 h-10 text-gray-300 mx-auto mb-3" />
            <p class="text-gray-500 font-medium">Aucun travail n'a encore été rendu.</p>
          </div>

          <div v-else class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
            <div v-for="sub in submissions" :key="sub.id" class="p-6 hover:bg-gray-50 transition-colors flex flex-col sm:flex-row gap-6 items-start">
              
              <!-- Student Info -->
              <div class="flex items-center gap-3 min-w-[200px]">
                <div class="h-10 w-10 rounded-full bg-blue-600/10 flex items-center justify-center text-blue-600 font-bold text-sm">
                  {{ sub.student.first_name[0] }}{{ sub.student.last_name[0] }}
                </div>
                <div>
                  <p class="font-bold text-gray-800 text-sm">{{ sub.student.last_name }} {{ sub.student.first_name }}</p>
                  <p class="text-xs text-gray-500 font-mono mt-0.5">{{ sub.student.registration_number }}</p>
                </div>
              </div>
              
              <!-- Submission Details -->
              <div class="flex-grow space-y-3 w-full">
                <div class="flex items-center justify-between">
                  <div class="flex items-center text-xs text-gray-500 font-medium">
                    <ClockIcon class="w-4 h-4 mr-1" />
                      Remis le: {{ formatDate(sub.submitted_at) }}
                  </div>
                  
                  <span v-if="sub.status === 'graded'" class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded border border-green-200 flex items-center">
                    <CheckCircleIcon class="w-3 h-3 mr-1" /> Noté
                  </span>
                  <span v-else class="bg-yellow-100 text-yellow-700 text-xs font-bold px-2 py-0.5 rounded border border-yellow-200 flex items-center">
                    <ClockIcon class="text-[12px] mr-1" /> À noter
                  </span>
                </div>
                
                <div v-if="sub.submission_text" class="bg-gray-50 border border-gray-100 p-3 rounded-lg text-sm text-gray-700 line-clamp-2">
                  "{{ sub.submission_text }}"
                </div>
                
                <div class="flex items-center gap-4 pt-1">
                  <a v-if="sub.file_path" :href="sub.file_path" target="_blank" class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                    <PaperClipIcon class="w-4 h-4 mr-1" />
                    Voir la pièce jointe
                  </a>
                  <span v-else class="text-sm text-gray-400 italic">Aucune pièce jointe</span>
                  
                  <button 
                    @click="openGradeModal(sub)"
                    class="ml-auto inline-flex items-center px-4 py-1.5 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition-colors"
                  >
                    {{ sub.status === 'graded' ? 'Modifier la note' : 'Évaluer' }}
                  </button>
                </div>
                
                <!-- Feedback Display -->
                <div v-if="sub.status === 'graded'" class="bg-green-50 border border-green-100 p-3 rounded-lg mt-3 flex items-start gap-3">
                  <div class="bg-white px-2 py-1 tracking-tighter text-black border border-green-200 rounded font-black text-lg min-w-[3rem] text-center shadow-sm">
                    {{ sub.grade }}
                  </div>
                  <div class="flex-grow">
                    <p class="text-xs font-bold text-green-800 uppercase tracking-wider mb-1">Feedback</p>
                    <p class="text-sm text-green-700">{{ sub.feedback || 'Aucun commentaire.' }}</p>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Grade Modal -->
    <div v-if="showGradeModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
      <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-6 border-b border-gray-100 bg-gray-50/50">
          <div>
            <h2 class="text-xl font-bold text-gray-800">Évaluation</h2>
            <p class="text-sm text-gray-500 mt-1">Étudiant: <span class="font-semibold text-gray-700">{{ currentSubmission?.student.last_name }} {{ currentSubmission?.student.first_name }}</span></p>
          </div>
          <button @click="showGradeModal = false" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-100">
            <XMarkIcon class=" w-5 h-5" />
          </button>
        </div>
        
        <form @submit.prevent="submitGrade" class="p-6 space-y-5">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Note (sur 20) <span class="text-red-500">*</span></label>
            <input 
              v-model="gradeForm.grade" 
              type="number" 
              step="0.25" 
              min="0" 
              max="20" 
              required 
              class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors outline-none font-bold text-lg"
              placeholder="15.5"
            >
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Appréciation / Commentaire <span class="text-sm font-normal text-gray-400">(Optionnel)</span></label>
            <textarea 
              v-model="gradeForm.feedback" 
              rows="4" 
              class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors outline-none resize-none" 
              placeholder="Très bon travail, attention à..."
            ></textarea>
          </div>
          
          <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
            <button type="button" @click="showGradeModal = false" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-xl transition-colors">
              Annuler
            </button>
            <button type="submit" :disabled="submittingGrade" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors flex items-center shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
              <span v-if="submittingGrade" class="material-icons animate-spin mr-2 text-[18px]">autorenew</span>
              <span v-else class="material-icons mr-2 text-[18px]">done</span>
              Enregistrer
            </button>
          </div>
        </form>
      </div>
    </div>
    
  </div>
</template>

<script setup>
import { ArrowLeftIcon, ExclamationCircleIcon, CheckCircleIcon, ClockIcon, ArrowDownTrayIcon, PaperClipIcon, XMarkIcon, DocumentTextIcon, MagnifyingGlassIcon, InboxIcon } from '@heroicons/vue/24/outline'
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { teacherHomeworkApi } from '@/api/endpoints/homework'

const route = useRoute()
const homeworkId = route.params.id

const homework = ref(null)
const submissions = ref([])
const loading = ref(true)
const error = ref('')
const success = ref('')

const showGradeModal = ref(false)
const currentSubmission = ref(null)
const submittingGrade = ref(false)
const gradeForm = ref({
  grade: '',
  feedback: ''
})

const fetchDetails = async () => {
  loading.value = true
  try {
    const response = await teacherHomeworkApi.getHomeworkDetails(homeworkId)
    homework.value = response.data.homework
    submissions.value = response.data.submissions
  } catch (err) {
    error.value = 'Erreur lors du chargement des détails'
    console.error(err)
  } finally {
    loading.value = false
  }
}

const openGradeModal = (sub) => {
  currentSubmission.value = sub
  gradeForm.value = {
    grade: sub.grade !== null ? sub.grade : '',
    feedback: sub.feedback || ''
  }
  showGradeModal.value = true
}

const submitGrade = async () => {
  submittingGrade.value = true
  try {
    await teacherHomeworkApi.gradeSubmission(homeworkId, currentSubmission.value.id, gradeForm.value)
    success.value = 'Note enregistrée avec succès'
    showGradeModal.value = false
    
    // Refresh
    await fetchDetails()
    
    setTimeout(() => { success.value = '' }, 3000)
  } catch (err) {
    console.error(err)
    error.value = 'Erreur lors de l\'enregistrement de la note'
  } finally {
    submittingGrade.value = false
  }
}

const formatDate = (dateString) => {
  const options = { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }
  const formatted = new Date(dateString).toLocaleDateString('fr-FR', options)
  return formatted.replace(',', ' à')
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

onMounted(() => {
  fetchDetails()
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



