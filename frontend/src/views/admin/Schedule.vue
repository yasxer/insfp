<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Emplois du Temps</h2>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gérer les emplois du temps par spécialité et par session</p>
    </div>

    <!-- Loading sessions -->
    <div v-if="loadingSessions" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <template v-else>
      <!-- Session Tabs -->
      <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-1 overflow-x-auto">
          <button
            v-for="session in sessions"
            :key="session.id"
            @click="selectSession(session)"
            class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm transition-colors rounded-t"
            :class="selectedSession?.id === session.id
              ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
              : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
          >
            {{ session.name }}
            <span
              class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium"
              :class="{
                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': session.status === 'active',
                'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200': session.status === 'pending',
                'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400': session.status === 'archived',
              }"
            >
              {{ session.status === 'active' ? 'Actuel' : session.status === 'pending' ? 'En attente' : 'Archive' }}
            </span>
          </button>
        </nav>
      </div>

      <!-- No sessions -->
      <div v-if="sessions.length === 0" class="text-center py-16">
        <p class="text-gray-500 dark:text-gray-400">Aucune session trouvée. Créez d'abord une session.</p>
      </div>

      <!-- Session content -->
      <template v-else-if="selectedSession">
        <!-- Loading specialties -->
        <div v-if="loadingSpecialties" class="flex justify-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        </div>

        <!-- Timetable view (specialty selected) -->
        <SpecialtyTimetable
          v-else-if="selectedSpecialty"
          :session="selectedSession"
          :specialty="selectedSpecialty"
          :group="selectedGroup"
          :is-readonly="isArchived"
          @back="clearSpecialtySelection"
          @published="onSpecialtyPublished"
        />

        <!-- Full combined timetable: always for archives, for active/pending when all published -->
        <FullSessionTimetable
          v-else-if="isArchived || (allPublished && specialties.length > 0 && viewMode === 'timetable')"
          :session="selectedSession"
          @edit="enterEditMode"
          @notify="showNotifyDialog = true"
        />

        <!-- Specialties list (cards view) -->
        <template v-else>
          <!-- All published + notify banner -->
          <div
            v-if="isSessionActive && allPublished && specialties.length > 0"
            class="flex items-center justify-between rounded-lg bg-green-50 dark:bg-green-900/40 border border-green-200 dark:border-green-700 p-4"
          >
            <div class="flex items-center space-x-2">
              <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <p class="text-sm font-medium text-green-800 dark:text-green-200">
                Tous les emplois du temps sont finalisés !
              </p>
            </div>
            <button
              @click="showNotifyDialog = true"
              class="ml-4 shrink-0 px-3 py-1.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-colors"
            >
              Notifier étudiants &amp; enseignants
            </button>
          </div>

          <!-- No specialties -->
          <div v-if="specialties.length === 0" class="text-center py-12">
            <p class="text-gray-500 dark:text-gray-400">Aucune spécialité avec des étudiants actifs dans cette session.</p>
          </div>

          <template v-else>
            <!-- Projection notice for a pending (draft) session -->
            <div
              v-if="selectedSession.status === 'pending'"
              class="flex items-start gap-2 rounded-lg border border-amber-300 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/30 p-3 text-sm text-amber-800 dark:text-amber-200"
            >
              <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>
                Brouillon (projection) : les cohortes sont affichées <strong>au semestre qu'elles auront après activation</strong> (+1), et la nouvelle promotion apparaît en <strong>S1</strong>. L'emploi du temps préparé ici deviendra l'emploi du temps actif une fois la session activée.
              </span>
            </div>

            <!-- Filters: semester + study mode -->
            <div class="flex flex-wrap items-center gap-3">
              <div class="flex items-center gap-2">
                <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Semestre :</label>
                <select
                  v-model="filterSemester"
                  class="text-sm py-1.5 px-3 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                  <option value="">Tous</option>
                  <option v-for="s in availableSemesters" :key="s" :value="s">S{{ s }}</option>
                </select>
              </div>
              <div class="flex items-center gap-2">
                <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Mode d'étude :</label>
                <select
                  v-model="filterStudyMode"
                  class="text-sm py-1.5 px-3 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                  <option value="">Tous</option>
                  <option v-for="m in availableStudyModes" :key="m" :value="m">{{ studyModeLabel(m) }}</option>
                </select>
              </div>
              <button
                v-if="filterSemester || filterStudyMode"
                @click="filterSemester = ''; filterStudyMode = ''"
                class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline"
              >
                Réinitialiser
              </button>
              <span class="ml-auto text-xs text-gray-400 dark:text-gray-500">
                {{ filteredSpecialties.length }} / {{ specialties.length }} affichée(s)
              </span>
            </div>

            <!-- No results after filtering -->
            <div v-if="filteredSpecialties.length === 0" class="text-center py-12">
              <p class="text-gray-500 dark:text-gray-400">Aucune spécialité ne correspond aux filtres.</p>
            </div>

            <!-- Specialty cards grid -->
            <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
              v-for="item in filteredSpecialties"
              :key="item.id"
              class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex flex-col"
            >
              <!-- Card header -->
              <div class="flex items-start justify-between mb-2">
                <div class="min-w-0">
                  <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                    {{ item.specialty_name }}
                    <span v-if="item.is_new" class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 align-middle">
                      Nouvelle promo
                    </span>
                  </h3>
                  <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ item.specialty_code }} · S{{ item.semester }}</p>
                </div>
                <!-- Status badge -->
                <span
                  class="ml-2 shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                  :class="{
                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': item.is_published,
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': item.is_partial && !item.is_published,
                    'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400': !item.is_published && !item.is_partial,
                  }"
                >
                  {{ item.is_published ? '✓ Finalisé' : item.is_partial ? '⏳ En cours' : '○ À faire' }}
                </span>
              </div>

              <!-- Info row -->
              <div class="flex flex-wrap gap-x-3 text-xs text-gray-500 dark:text-gray-400 mb-2">
                <span>{{ studyModeLabel(item.study_mode) }}</span>
                <span>{{ item.students_count }} étudiant(s)</span>
                <span v-if="item.groups_count > 0">{{ item.groups_count }} groupe(s)</span>
              </div>

              <!-- Schedules count -->
              <p class="text-xs text-indigo-500 dark:text-indigo-400 mb-3">
                {{ item.schedules_count }} séance(s) planifiée(s)
              </p>

              <!-- Group buttons (when has groups) -->
              <div v-if="item.groups_count > 0" class="mb-3">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                  {{ !isArchived ? 'Gérer par groupe :' : 'Voir par groupe :' }}
                </p>
                <div class="flex flex-wrap gap-1.5">
                  <button
                    v-for="grp in item.groups"
                    :key="grp"
                    @click="openTimetable(item, grp)"
                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium transition-colors"
                    :class="!isArchived
                      ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900'
                      : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200'"
                  >
                    Groupe {{ grp }}
                  </button>
                </div>
              </div>

              <!-- Single action button (no groups) -->
              <div v-else class="mt-auto pt-2">
                <button
                  @click="openTimetable(item, null)"
                  class="w-full py-1.5 text-sm font-medium rounded-md transition-colors"
                  :class="!isArchived
                    ? item.is_published
                      ? 'text-amber-700 bg-amber-50 dark:bg-amber-900/30 dark:text-amber-300 hover:bg-amber-100'
                      : 'text-indigo-700 bg-indigo-50 dark:bg-indigo-900/30 dark:text-indigo-300 hover:bg-indigo-100'
                    : 'text-gray-600 bg-gray-100 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200'"
                >
                  {{ !isArchived
                      ? (item.is_published ? '✎ Modifier' : '+ Éditer')
                      : '👁 Voir' }}
                </button>
              </div>
            </div>
            </div>
          </template>
        </template>
      </template>
    </template>

    <!-- Notify dialog -->
    <Teleport to="body">
      <div v-if="showNotifyDialog" class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-gray-900/60" @click="showNotifyDialog = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 max-w-md w-full">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Notifier tout le monde ?</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
            Un message sera envoyé à tous les étudiants et enseignants pour les informer que les emplois du temps de la session <strong>{{ selectedSession?.name }}</strong> sont disponibles.
          </p>
          <div class="flex justify-end space-x-3">
            <button
              @click="showNotifyDialog = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50"
            >
              Annuler
            </button>
            <button
              @click="sendNotification"
              :disabled="notifying"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50"
            >
              {{ notifying ? 'Envoi...' : 'Oui, notifier' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import schedulesApi from '@/api/endpoints/schedules'
import SpecialtyTimetable from '@/components/admin/schedules/SpecialtyTimetable.vue'
import FullSessionTimetable from '@/components/admin/schedules/FullSessionTimetable.vue'
import axios from '@/api/axios'
import { useToastStore } from '@/stores/toast'

const toastStore = useToastStore()

// ── State ────────────────────────────────────────────────────────────────────
const loadingSessions    = ref(false)
const loadingSpecialties = ref(false)
const sessions           = ref([])
const selectedSession    = ref(null)
const specialties        = ref([])
const selectedSpecialty  = ref(null)
const selectedGroup      = ref(null)
const showNotifyDialog   = ref(false)
const notifying          = ref(false)
const viewMode           = ref('timetable') // 'timetable' | 'cards'
const filterSemester     = ref('')
const filterStudyMode    = ref('')

// Distinct semesters / study modes present in the current session's cards
const availableSemesters = computed(() =>
  [...new Set(specialties.value.map(s => s.semester))].sort((a, b) => a - b)
)
const availableStudyModes = computed(() =>
  [...new Set(specialties.value.map(s => s.study_mode))]
)

const filteredSpecialties = computed(() =>
  specialties.value.filter(s =>
    (filterSemester.value === '' || s.semester === Number(filterSemester.value)) &&
    (filterStudyMode.value === '' || s.study_mode === filterStudyMode.value)
  )
)

const allPublished = computed(() =>
  specialties.value.length > 0 && specialties.value.every(s => s.is_published)
)

// Only a truly archived session is read-only — a pending (not-yet-activated)
// session must remain fully editable just like the active one.
const isArchived = computed(() => selectedSession.value?.status === 'archived')
const isSessionActive = computed(() => selectedSession.value?.status === 'active')

// ── Init ─────────────────────────────────────────────────────────────────────
onMounted(fetchSessions)

async function fetchSessions() {
  loadingSessions.value = true
  try {
    const res = await schedulesApi.getSessions()
    sessions.value = res.data.sessions || []
    // Auto-select the active session, falling back to the next pending one
    const current = sessions.value.find(s => s.status === 'active') || sessions.value.find(s => s.status === 'pending')
    if (current) await selectSession(current)
    else if (sessions.value.length > 0) await selectSession(sessions.value[0])
  } catch (e) {
    console.error('Failed to load sessions:', e)
  } finally {
    loadingSessions.value = false
  }
}

async function selectSession(session) {
  selectedSession.value  = session
  selectedSpecialty.value = null
  selectedGroup.value    = null
  viewMode.value         = 'timetable'
  filterSemester.value   = ''
  filterStudyMode.value  = ''
  await fetchSpecialties(session.id)
}

async function fetchSpecialties(sessionId) {
  loadingSpecialties.value = true
  try {
    const res = await schedulesApi.getSessionSpecialties(sessionId)
    specialties.value = res.data.specialties || []
  } catch (e) {
    console.error('Failed to load specialties:', e)
  } finally {
    loadingSpecialties.value = false
  }
}

// ── Navigation ───────────────────────────────────────────────────────────────
function openTimetable(specialty, group) {
  selectedSpecialty.value = specialty
  selectedGroup.value     = group
}

function clearSpecialtySelection() {
  selectedSpecialty.value = null
  selectedGroup.value     = null
  if (selectedSession.value) fetchSpecialties(selectedSession.value.id)
}

function onSpecialtyPublished() {
  selectedSpecialty.value = null
  selectedGroup.value     = null
  if (selectedSession.value) {
    fetchSpecialties(selectedSession.value.id).then(() => {
      // Auto-switch to full timetable when everything is finalised
      if (allPublished.value) viewMode.value = 'timetable'
    })
  }
}

function enterEditMode() {
  viewMode.value          = 'cards'
  selectedSpecialty.value = null
  selectedGroup.value     = null
}

// ── Notifications ─────────────────────────────────────────────────────────────
async function sendNotification() {
  notifying.value = true
  try {
    await axios.post('/api/admin/messages/send', {
      subject:        `Emploi du temps – ${selectedSession.value.name}`,
      message:        `Les emplois du temps de la session ${selectedSession.value.name} sont désormais disponibles. Connectez-vous pour consulter votre planning.`,
      recipient_type: 'all',
    })
    showNotifyDialog.value = false
    toastStore.success('Notifications envoyées avec succès !')
  } catch (e) {
    toastStore.error('Erreur lors de l\'envoi des notifications.')
  } finally {
    notifying.value = false
  }
}

// ── Helpers ──────────────────────────────────────────────────────────────────
function studyModeLabel(mode) {
  return { initial: 'Initial', alternance: 'Alternance', continue: 'Cours du soir' }[mode] || mode
}
</script>
