<template>
  <div class="space-y-6">
    <div class="flex items-center gap-4">
      <router-link :to="{ name: 'teacher.homeworks' }" class="text-gray-500 hover:text-gray-700 bg-white p-2 rounded-lg border border-gray-100 shadow-sm transition-colors dark:text-gray-400 dark:bg-gray-900 dark:border-gray-800">
        <ArrowLeftIcon class="rtl:rotate-180 w-5 h-5" />
      </router-link>
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ t('teacher.homework_detail.details_devoir') }}</h1>
        <p class="text-gray-500 text-sm mt-1 dark:text-gray-400" v-if="homework">{{ t('teacher.homework_detail.module', { p0: homework.module }) }}</p>
      </div>
    </div>

    <!-- Error/Success Messages -->
    <div v-if="error" class="bg-red-50 text-red-600 p-4 rounded-lg flex items-center dark:bg-red-900/30 dark:text-red-300">
      <ExclamationCircleIcon class="mr-2 w-5 h-5" />
      {{ error }}
    </div>
    
    <div v-if="success" class="bg-green-50 text-green-600 p-4 rounded-lg flex items-center shadow-sm dark:bg-green-900/30 dark:text-green-300">
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 dark:bg-gray-900 dark:border-gray-800">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ t('teacher.homework_detail.informations') }}</h2>
            <span class="bg-blue-50 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full border border-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800">
              {{ t('teacher.homework_detail.soumission_s', { p0: submissions.length }) }}
            </span>
          </div>
          
          <div class="space-y-4">
            <div>
              <p class="text-sm font-medium text-gray-500 mb-1 dark:text-gray-400">{{ t('common.title') }}</p>
              <p class="text-gray-800 font-semibold dark:text-gray-100">{{ homework.title }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1 dark:text-gray-400">{{ t('teacher.homework_detail.type_rendu') }}</p>
                <div class="inline-flex items-center text-xs font-bold px-2.5 py-1 rounded-full border"
                  :class="homework.submission_type === 'online' ? 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800' : 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800'">
                  {{ homework.submission_type === 'online' ? t('teacher.homework_detail.ligne_upload_fichier') : t('teacher.homework_detail.presentiel') }}
                </div>
              </div>

              <div>
                <p class="text-sm font-medium text-gray-500 mb-1 dark:text-gray-400">{{ t('teacher.homework_detail.date_limite') }}</p>
              <div class="flex items-center text-sm font-semibold" :class="getDueDateClass(homework.due_date)">
                <ClockIcon class="w-4 h-4 mr-1.5" />
                {{ formatDate(homework.due_date) }}
              </div>
            </div>
            
            <div>
              <p class="text-sm font-medium text-gray-500 mb-1 dark:text-gray-400">{{ t('common.description') }}</p>
              <p class="text-gray-700 text-sm whitespace-pre-wrap dark:text-gray-200">{{ homework.description }}</p>
            </div>
            
            <div v-if="homework.file_path" class="pt-4 border-t border-gray-100 dark:border-gray-800">
              <p class="text-sm font-medium text-gray-500 mb-3 dark:text-gray-400">{{ t('teacher.homework_detail.fichier_source') }}</p>
              <a :href="homework.file_path" target="_blank" class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors group dark:border-gray-700 dark:hover:bg-gray-800">
                <div class="bg-blue-100 p-2 rounded-md mr-3 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors dark:bg-blue-900/30 dark:text-blue-300">
                  <DocumentTextIcon class="w-5 h-5" />
                </div>
                <div class="flex-grow overflow-hidden">
                  <p class="text-sm font-medium text-gray-800 truncate dark:text-gray-100">{{ t('teacher.homework_detail.sujet_devoir_ext') }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ t('common.download') }}</p>
                </div>
                <ArrowDownTrayIcon class="text-gray-400 group-hover:text-red-600" />
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Submissions Panel -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden dark:bg-gray-900 dark:border-gray-800">
          <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 dark:border-gray-800">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ t('teacher.homework_detail.travaux_rendus') }}</h2>
            <div class="flex items-center bg-white px-3 py-1.5 rounded-lg border border-gray-200 dark:bg-gray-900 dark:border-gray-700">
              <MagnifyingGlassIcon class="text-gray-400 w-5 h-5 mr-2" />
              <input type="text" :placeholder="t('teacher.homework_detail.rechercher_stagiaire')" class="border-none outline-none text-sm w-48 text-gray-700 bg-transparent placeholder-gray-400 dark:text-gray-200 dark:placeholder-gray-500">
            </div>
          </div>

          <div v-if="submissions.length === 0" class="p-12 text-center">
            <InboxIcon class="w-10 h-10 text-gray-300 mx-auto mb-3" />
            <p class="text-gray-500 font-medium dark:text-gray-400">{{ t('teacher.homework_detail.aucun_travail_n_encore_ete') }}</p>
          </div>

          <div v-else class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto dark:divide-gray-800">
            <div v-for="sub in submissions" :key="sub.id" class="p-6 hover:bg-gray-50 transition-colors flex flex-col sm:flex-row gap-6 items-start dark:hover:bg-gray-800">
              
              <!-- Student Info -->
              <div class="flex items-center gap-3 min-w-[200px]">
                <div class="h-10 w-10 rounded-full bg-blue-600/10 flex items-center justify-center text-blue-600 font-bold text-sm dark:text-blue-300">
                  {{ sub.student.first_name[0] }}{{ sub.student.last_name[0] }}
                </div>
                <div>
                  <p class="font-bold text-gray-800 text-sm dark:text-gray-100">{{ sub.student.last_name }} {{ sub.student.first_name }}</p>
                  <p class="text-xs text-gray-500 font-mono mt-0.5 dark:text-gray-400">{{ sub.student.registration_number }}</p>
                </div>
              </div>
              
              <!-- Submission Details -->
              <div class="flex-grow space-y-3 w-full">
                <div class="flex items-center justify-between">
                  <div class="flex items-center text-xs text-gray-500 font-medium dark:text-gray-400">
                    <ClockIcon class="w-4 h-4 mr-1" />
                      {{ t('teacher.homework_detail.remis', { p0: formatDate(sub.submitted_at) }) }}
                  </div>
                  
                  <span v-if="sub.status === 'graded'" class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded border border-green-200 flex items-center dark:bg-green-900/30 dark:text-green-300 dark:border-green-800">
                    <CheckCircleIcon class="w-3 h-3 mr-1" /> {{ t('teacher.homework_detail.note') }}
                  </span>
                  <span v-else class="bg-yellow-100 text-yellow-700 text-xs font-bold px-2 py-0.5 rounded border border-yellow-200 flex items-center dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800">
                    <ClockIcon class="text-[12px] mr-1" /> {{ t('teacher.homework_detail.noter') }}
                  </span>
                </div>
                
                <div v-if="sub.submission_text" class="bg-gray-50 border border-gray-100 p-3 rounded-lg text-sm text-gray-700 line-clamp-2 dark:bg-gray-800/60 dark:border-gray-800 dark:text-gray-200">
                  "{{ sub.submission_text }}"
                </div>
                
                <div class="flex items-center gap-4 pt-1">
                  <a v-if="sub.file_path" :href="sub.file_path" target="_blank" class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline dark:text-blue-300">
                    <PaperClipIcon class="w-4 h-4 mr-1" />
                    {{ t('teacher.homework_detail.voir_piece_jointe') }}
                  </a>
                  <span v-else class="text-sm text-gray-400 italic">{{ t('teacher.homework_detail.aucune_piece_jointe') }}</span>
                  
                  <button
                    @click="openReview(sub)"
                    class="ml-auto inline-flex items-center px-4 py-1.5 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition-colors"
                  >
                    <EyeIcon class="w-4 h-4 me-1.5" />
                    {{ t('teacher.homework_detail.voir_travail') }}
                  </button>
                </div>
                
                <!-- Feedback Display -->
                <div v-if="sub.status === 'graded'" class="bg-green-50 border border-green-100 p-3 rounded-lg mt-3 flex items-start gap-3 dark:bg-green-900/30 dark:border-green-800">
                  <div class="bg-white px-2 py-1 tracking-tighter text-black border border-green-200 rounded font-black text-lg min-w-[3rem] text-center shadow-sm dark:bg-gray-900 dark:border-green-800">
                    {{ sub.grade }}
                  </div>
                  <div class="flex-grow">
                    <p class="text-xs font-bold text-green-800 uppercase tracking-wider mb-1 dark:text-green-300">{{ t('teacher.homework_detail.feedback') }}</p>
                    <p class="text-sm text-green-700 dark:text-green-300">{{ sub.feedback || t('teacher.homework_detail.aucun_commentaire') }}</p>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Review modal: the teacher reads the work first, then grades it -->
    <div v-if="showGradeModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in" @click.self="closeReview">
      <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[92vh] shadow-2xl overflow-hidden flex flex-col dark:bg-gray-900">
        <div class="flex justify-between items-start p-6 border-b border-gray-100 bg-gray-50/50 dark:border-gray-800">
          <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
              {{ reviewStep === 'grade' ? t('teacher.homework_detail.evaluation') : t('teacher.homework_detail.travail_rendu') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1 dark:text-gray-400">
              {{ t('teacher.homework_detail.stagiaire') }}
              <span class="font-semibold text-gray-700 dark:text-gray-200">{{ currentSubmission?.student.last_name }} {{ currentSubmission?.student.first_name }}</span>
            </p>
            <p class="flex flex-wrap items-center gap-2 text-xs text-gray-500 mt-1 dark:text-gray-400">
              <ClockIcon class="w-4 h-4" />
              {{ t('teacher.homework_detail.remis', { p0: formatDate(currentSubmission?.submitted_at) }) }}
              <span v-if="isLate(currentSubmission)" class="px-2 py-0.5 rounded bg-red-100 text-red-700 font-semibold dark:bg-red-900/30 dark:text-red-300">{{ t('teacher.homework_detail.rendu_en_retard') }}</span>
            </p>
          </div>
          <button @click="closeReview" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800" :aria-label="t('common.close')">
            <XMarkIcon class=" w-5 h-5" />
          </button>
        </div>

        <div class="overflow-y-auto p-6 space-y-5">
          <!-- 1. The student's work -->
          <section>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 dark:text-gray-400">{{ t('teacher.homework_detail.reponse_stagiaire') }}</p>
            <div v-if="currentSubmission?.submission_text" class="bg-gray-50 border border-gray-100 p-4 rounded-xl text-sm text-gray-800 whitespace-pre-line leading-relaxed dark:bg-gray-800/60 dark:border-gray-800 dark:text-gray-100">{{ currentSubmission.submission_text }}</div>
            <p v-else class="text-sm text-gray-400 italic">{{ t('teacher.homework_detail.aucun_texte') }}</p>
          </section>

          <section>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 dark:text-gray-400">{{ t('teacher.homework_detail.piece_jointe') }}</p>
            <template v-if="currentSubmission?.file_path">
              <iframe v-if="fileKind === 'pdf'" :src="currentSubmission.file_path" class="w-full h-80 rounded-xl border border-gray-200 dark:border-gray-700" :title="t('teacher.homework_detail.piece_jointe')"></iframe>
              <img v-else-if="fileKind === 'image'" :src="currentSubmission.file_path" alt="" class="max-h-80 rounded-xl border border-gray-200 dark:border-gray-700" />
              <a :href="currentSubmission.file_path" target="_blank" rel="noopener" class="mt-2 inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline dark:text-blue-300">
                <ArrowDownTrayIcon class="w-4 h-4 me-1" />
                {{ t('teacher.homework_detail.ouvrir_fichier') }}
              </a>
            </template>
            <p v-else class="text-sm text-gray-400 italic">{{ t('teacher.homework_detail.aucune_piece_jointe') }}</p>
          </section>

          <!-- Current mark, when the work was already graded -->
          <div v-if="reviewStep === 'review' && currentSubmission?.status === 'graded'" class="bg-green-50 border border-green-100 p-3 rounded-lg flex items-start gap-3 dark:bg-green-900/30 dark:border-green-800">
            <div class="bg-white px-2 py-1 text-black border border-green-200 rounded font-black text-lg min-w-[3rem] text-center shadow-sm dark:bg-gray-900 dark:text-white dark:border-green-800">{{ currentSubmission.grade }}</div>
            <p class="text-sm text-green-700 dark:text-green-300">{{ currentSubmission.feedback || t('teacher.homework_detail.aucun_commentaire') }}</p>
          </div>

          <!-- 2. Grading, only once the work has been read -->
          <form v-if="reviewStep === 'grade'" id="grade-form" @submit.prevent="submitGrade" class="space-y-5 pt-5 border-t border-gray-100 dark:border-gray-800">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5 dark:text-gray-200">{{ t('teacher.homework_detail.note_20') }} <span class="text-red-500">*</span></label>
              <input
                ref="gradeInput"
                v-model="gradeForm.grade"
                type="number"
                step="0.25"
                min="0"
                max="20"
                required
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors outline-none font-bold text-lg dark:bg-gray-800/60 dark:border-gray-700 dark:focus:bg-gray-900"
                placeholder="15.5"
              >
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5 dark:text-gray-200">{{ t('teacher.homework_detail.appreciation_commentaire') }} <span class="text-sm font-normal text-gray-400">{{ t('teacher.homework_detail.optionnel') }}</span></label>
              <textarea
                v-model="gradeForm.feedback"
                rows="3"
                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors outline-none resize-none dark:bg-gray-800/60 dark:border-gray-700 dark:focus:bg-gray-900"
                :placeholder="t('teacher.homework_detail.tres_bon_travail_attention')"
              ></textarea>
            </div>
          </form>
        </div>

        <div class="p-4 flex justify-end gap-3 border-t border-gray-100 bg-gray-50/50 dark:border-gray-800">
          <template v-if="reviewStep === 'review'">
            <button type="button" @click="closeReview" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-xl transition-colors dark:text-gray-200 dark:bg-gray-900 dark:border-gray-600 dark:hover:bg-gray-800">
              {{ t('common.close') }}
            </button>
            <button type="button" @click="startGrading" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors flex items-center shadow-md">
              <PencilSquareIcon class="h-[18px] w-[18px] me-2" />
              {{ currentSubmission?.status === 'graded' ? t('teacher.homework_detail.modifier_note') : t('teacher.homework_detail.evaluer_ce_travail') }}
            </button>
          </template>
          <template v-else>
            <button type="button" @click="reviewStep = 'review'" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-xl transition-colors dark:text-gray-200 dark:bg-gray-900 dark:border-gray-600 dark:hover:bg-gray-800">
              {{ t('teacher.homework_detail.retour_travail') }}
            </button>
            <button type="submit" form="grade-form" :disabled="submittingGrade" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors flex items-center shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
              <ArrowPathIcon v-if="submittingGrade" class="h-[18px] w-[18px] animate-spin me-2" />
              <CheckIcon v-else class="h-[18px] w-[18px] me-2" />
              {{ t('common.save') }}
            </button>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import { dateLocale } from '@/i18n'
const { t } = useI18n()
import { ArrowLeftIcon, ExclamationCircleIcon, CheckCircleIcon, ClockIcon, ArrowDownTrayIcon, PaperClipIcon, XMarkIcon, DocumentTextIcon, MagnifyingGlassIcon, InboxIcon, ArrowPathIcon, CheckIcon, EyeIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'
import { ref, computed, nextTick, onMounted } from 'vue'
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
// 'review': the work is shown first; 'grade': the form, reachable only from the review
const reviewStep = ref('review')
const gradeInput = ref(null)
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
    error.value = t('teacher.homework_detail.erreur_lors_chargement_details')
    console.error(err)
  } finally {
    loading.value = false
  }
}

const openReview = (sub) => {
  currentSubmission.value = sub
  reviewStep.value = 'review'
  showGradeModal.value = true
}

const startGrading = async () => {
  const sub = currentSubmission.value
  gradeForm.value = {
    grade: sub.grade !== null ? sub.grade : '',
    feedback: sub.feedback || ''
  }
  reviewStep.value = 'grade'
  await nextTick()
  gradeInput.value?.focus()
}

const closeReview = () => {
  showGradeModal.value = false
}

const fileKind = computed(() => {
  const path = (currentSubmission.value?.file_path || '').toLowerCase().split('?')[0]
  if (path.endsWith('.pdf')) return 'pdf'
  if (/\.(png|jpe?g|gif|webp)$/.test(path)) return 'image'
  return 'other'
})

const isLate = (sub) => !!(sub && homework.value?.due_date && new Date(sub.submitted_at) > new Date(homework.value.due_date))

const submitGrade = async () => {
  submittingGrade.value = true
  try {
    await teacherHomeworkApi.gradeSubmission(homeworkId, currentSubmission.value.id, gradeForm.value)
    success.value = t('teacher.homework_detail.note_enregistree_avec_succes')
    showGradeModal.value = false
    
    // Refresh
    await fetchDetails()
    
    setTimeout(() => { success.value = '' }, 3000)
  } catch (err) {
    console.error(err)
    error.value = t('teacher.homework_detail.erreur_lors_enregistrement_note')
  } finally {
    submittingGrade.value = false
  }
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const options = { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }
  const formatted = new Date(dateString).toLocaleDateString(dateLocale(), options)
  return formatted.replace(',', ' ·')
}

const getDueDateClass = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  if (date < now) return 'text-red-600 dark:text-red-300'
  
  const diffTime = Math.abs(date - now);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
  if (diffDays <= 2) return 'text-orange-500'
  
  return 'text-emerald-600 dark:text-emerald-300'
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



