<template>
  <div class="space-y-4">

    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div class="flex items-center space-x-3">
        <!-- Back -->
        <button
          @click="$emit('back')"
          class="p-1.5 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white leading-tight">
            {{ specialty.specialty_name }}
            <span class="text-indigo-500 dark:text-indigo-400"> · S{{ specialty.semester }}</span>
            <span v-if="group" class="text-indigo-500 dark:text-indigo-400"> · Groupe {{ group }}</span>
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
            {{ studyModeLabel(specialty.study_mode) }} · {{ specialty.students_count }} étudiant(s)
            <span v-if="isReadonly" class="ml-2 text-xs text-amber-600 dark:text-amber-400">(Archive – lecture seule)</span>
          </p>
        </div>
      </div>

      <!-- Actions (editable only) -->
      <div v-if="!isReadonly" class="flex items-center space-x-2">
        <button
          v-if="isPublished"
          @click="unpublish"
          :disabled="saving"
          class="px-3 py-1.5 text-sm font-medium text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/40 border border-amber-200 dark:border-amber-700 rounded-lg hover:bg-amber-100 transition-colors disabled:opacity-50"
        >
          ✎ Modifier
        </button>
        <button
          v-else
          @click="publish"
          :disabled="saving || schedules.length === 0"
          class="px-3 py-1.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ saving ? 'Sauvegarde...' : '✓ Finaliser' }}
        </button>
      </div>
    </div>

    <!-- Published notice -->
    <div
      v-if="isPublished && !isReadonly"
      class="rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 px-4 py-2.5 flex items-center space-x-2"
    >
      <svg class="h-4 w-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
      </svg>
      <p class="text-sm text-green-800 dark:text-green-200">
        Emploi du temps finalisé. Cliquez <strong>Modifier</strong> pour le rouvrir.
      </p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-10">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Timetable grid -->
    <div v-else class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-x-auto">
      <table class="min-w-full">
        <thead>
          <tr class="bg-gray-50 dark:bg-gray-700/60 border-b border-gray-200 dark:border-gray-700">
            <th class="w-20 px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Horaire
            </th>
            <th
              v-for="day in days"
              :key="day.key"
              class="px-2 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[110px]"
            >
              {{ day.label }}
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
          <tr
            v-for="slot in timeSlots"
            :key="slot.start"
            class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors"
          >
            <!-- Time column -->
            <td class="px-3 py-2 whitespace-nowrap">
              <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ slot.start }}</div>
              <div class="text-xs text-gray-400 dark:text-gray-500">{{ slot.end }}</div>
            </td>

            <!-- Day cells -->
            <td
              v-for="day in days"
              :key="`${day.key}-${slot.start}`"
              class="px-1.5 py-1.5 align-top"
              :class="{ 'cursor-pointer': canEdit }"
              @click="canEdit && !getSlot(day.key, slot.start) && openModal(day.key, slot)"
            >
              <!-- Filled slot -->
              <div v-if="getSlot(day.key, slot.start)" class="relative group h-full">
                <div class="bg-indigo-100 dark:bg-indigo-900/60 border border-indigo-200 dark:border-indigo-700 rounded-lg p-2 h-full min-h-[60px]">
                  <p class="font-semibold text-indigo-900 dark:text-indigo-100 text-xs leading-tight truncate">
                    {{ getSlot(day.key, slot.start).module.name }}
                  </p>
                  <p class="text-indigo-600 dark:text-indigo-300 text-xs mt-0.5 truncate">
                    {{ getSlot(day.key, slot.start).teacher.full_name }}
                  </p>
                  <p v-if="getSlot(day.key, slot.start).classroom" class="text-indigo-400 text-xs mt-0.5">
                    🏫 {{ getSlot(day.key, slot.start).classroom }}
                  </p>
                </div>
                <!-- Delete button (hover) -->
                <button
                  v-if="canEdit"
                  @click.stop="deleteEntry(getSlot(day.key, slot.start))"
                  class="absolute -top-1.5 -right-1.5 hidden group-hover:flex w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full items-center justify-center text-xs font-bold shadow"
                >×</button>
              </div>

              <!-- Empty slot (editable) -->
              <div
                v-else-if="canEdit"
                class="h-[60px] flex items-center justify-center rounded-lg border-2 border-dashed border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/10 transition-colors group"
              >
                <svg class="h-4 w-4 text-gray-300 dark:text-gray-600 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
              </div>

              <!-- Empty slot (readonly) -->
              <div v-else class="h-[60px]"></div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Entry Modal -->
    <ScheduleEntryModal
      v-if="entryModal.isOpen"
      :specialty="specialty"
      :group="group"
      :session="session"
      :day="entryModal.day"
      :start-time="entryModal.startTime"
      :end-time="entryModal.endTime"
      @close="entryModal.isOpen = false"
      @saved="onEntrySaved"
    />

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import schedulesApi from '@/api/endpoints/schedules'
import ScheduleEntryModal from './ScheduleEntryModal.vue'

// ── Props & Emits ─────────────────────────────────────────────────────────────
const props = defineProps({
  session:    { type: Object, required: true },
  specialty:  { type: Object, required: true },
  group:      { type: String, default: null },
  isReadonly: { type: Boolean, default: false },
})

const emit = defineEmits(['back', 'published'])

// ── State ─────────────────────────────────────────────────────────────────────
const loading     = ref(false)
const saving      = ref(false)
const schedules   = ref([])
const isPublished = ref(false)
const entryModal  = ref({ isOpen: false, day: null, startTime: null, endTime: null })

const canEdit = computed(() => !props.isReadonly && !isPublished.value)

// ── Constants ─────────────────────────────────────────────────────────────────
const days = [
  { key: 'saturday',  label: 'Sam' },
  { key: 'sunday',    label: 'Dim' },
  { key: 'monday',    label: 'Lun' },
  { key: 'tuesday',   label: 'Mar' },
  { key: 'wednesday', label: 'Mer' },
  { key: 'thursday',  label: 'Jeu' },
]

const timeSlots = [
  { start: '08:00', end: '09:30' },
  { start: '09:30', end: '11:00' },
  { start: '11:00', end: '12:30' },
  { start: '13:00', end: '14:30' },
  { start: '14:30', end: '16:00' },
  { start: '16:30', end: '18:00' },
  { start: '18:00', end: '19:30' },
]

// ── Data fetching ─────────────────────────────────────────────────────────────
onMounted(fetchAll)
watch([() => props.specialty, () => props.group], fetchAll)

async function fetchAll() {
  loading.value = true
  try {
    await Promise.all([fetchSchedules(), fetchStatus()])
  } finally {
    loading.value = false
  }
}

async function fetchSchedules() {
  const params = {
    session_id:   props.session.id,
    specialty_id: props.specialty.specialty_id,
    semester:     props.specialty.semester,
    study_mode:   props.specialty.study_mode,
  }
  if (props.group !== null) params.group = props.group

  const res = await schedulesApi.getSchedules(params)
  schedules.value = res.data.schedules || []
}

async function fetchStatus() {
  const res = await schedulesApi.getSessionSpecialties(props.session.id)
  const all  = res.data.specialties || []
  const mine = all.find(s =>
    s.specialty_id === props.specialty.specialty_id &&
    s.semester     === props.specialty.semester     &&
    s.study_mode   === props.specialty.study_mode
  )
  isPublished.value = mine?.is_published ?? false
}

// ── Grid helpers ──────────────────────────────────────────────────────────────
function getSlot(dayKey, startTime) {
  return schedules.value.find(s =>
    s.day === dayKey && s.start_time.substring(0, 5) === startTime
  ) ?? null
}

// ── Modal ─────────────────────────────────────────────────────────────────────
function openModal(day, slot) {
  entryModal.value = { isOpen: true, day, startTime: slot.start, endTime: slot.end }
}

async function onEntrySaved() {
  entryModal.value.isOpen = false
  await fetchSchedules()
}

// ── Delete ────────────────────────────────────────────────────────────────────
async function deleteEntry(schedule) {
  if (!confirm('Supprimer cette séance ?')) return
  try {
    await schedulesApi.deleteSchedule(schedule.id)
    await fetchSchedules()
  } catch (e) {
    alert(e.response?.data?.message || 'Erreur lors de la suppression.')
  }
}

// ── Publish / Unpublish ───────────────────────────────────────────────────────
async function publish() {
  saving.value = true
  try {
    await schedulesApi.publishSpecialty(props.session.id, {
      specialty_id: props.specialty.specialty_id,
      semester:     props.specialty.semester,
      study_mode:   props.specialty.study_mode,
      group:        props.group ?? null,
    })
    isPublished.value = true
    emit('published')
  } catch (e) {
    alert(e.response?.data?.message || 'Erreur lors de la finalisation.')
  } finally {
    saving.value = false
  }
}

async function unpublish() {
  saving.value = true
  try {
    await schedulesApi.unpublishSpecialty(props.session.id, {
      specialty_id: props.specialty.specialty_id,
      semester:     props.specialty.semester,
      study_mode:   props.specialty.study_mode,
      group:        props.group ?? null,
    })
    isPublished.value = false
  } catch (e) {
    alert(e.response?.data?.message || 'Erreur.')
  } finally {
    saving.value = false
  }
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function studyModeLabel(mode) {
  return { initial: 'Initial', alternance: 'Alternance', continue: 'Cours du soir' }[mode] || mode
}
</script>
