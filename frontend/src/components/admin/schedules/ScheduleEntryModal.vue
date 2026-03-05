<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center px-4">
      <div class="absolute inset-0 bg-gray-900/60" @click="$emit('close')"></div>

      <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md max-h-[90vh] flex flex-col">

        <!-- Header -->
        <div class="px-5 pt-5 pb-4 border-b border-gray-100 dark:border-gray-700">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Ajouter une séance</h3>
          <div class="mt-1 flex flex-wrap gap-x-3 text-sm text-gray-500 dark:text-gray-400">
            <span class="text-indigo-600 dark:text-indigo-400 font-medium">{{ dayLabel }} · {{ startTime }} – {{ endTime }}</span>
            <span>{{ specialty.specialty_name }} · S{{ specialty.semester }}</span>
            <span v-if="group">Groupe {{ group }}</span>
          </div>
        </div>

        <!-- Body -->
        <div class="px-5 py-4 overflow-y-auto flex-1 space-y-4">

          <!-- Module selection -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Module</label>

            <div v-if="loadingModules" class="flex justify-center py-4">
              <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"></div>
            </div>

            <div v-else-if="modules.length === 0" class="text-sm text-gray-400 italic py-2">
              Aucun module disponible pour ce semestre.
            </div>

            <div v-else class="space-y-1 max-h-44 overflow-y-auto pr-1">
              <label
                v-for="mod in modules"
                :key="mod.id"
                class="flex items-center gap-2.5 px-3 py-2.5 border rounded-lg cursor-pointer transition-colors"
                :class="form.module_id === mod.id
                  ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/40'
                  : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'"
              >
                <input
                  type="radio"
                  :value="mod.id"
                  v-model="form.module_id"
                  @change="onModuleChange"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                />
                <span class="text-sm text-gray-900 dark:text-white flex-1 min-w-0">
                  {{ mod.name }}
                  <span class="text-gray-400 text-xs ml-1">({{ mod.code }})</span>
                </span>
              </label>
            </div>
            <p v-if="errors.module_id" class="mt-1 text-xs text-red-600">{{ errors.module_id }}</p>
          </div>

          <!-- Teacher selection -->
          <div v-if="form.module_id">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Enseignant</label>

            <div v-if="loadingTeachers" class="flex justify-center py-3">
              <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-indigo-600"></div>
            </div>

            <div v-else-if="moduleTeachers.length === 0" class="text-sm text-gray-400 italic py-2">
              Aucun enseignant assigné à ce module.
            </div>

            <select
              v-else
              v-model="form.teacher_id"
              class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="">Sélectionner un enseignant</option>
              <option v-for="t in moduleTeachers" :key="t.id" :value="t.id">{{ t.full_name }}</option>
            </select>
            <p v-if="errors.teacher_id" class="mt-1 text-xs text-red-600">{{ errors.teacher_id }}</p>
          </div>

          <!-- Classroom -->
          <div v-if="form.module_id">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
              Salle <span class="text-gray-400 font-normal">(optionnel)</span>
            </label>
            <input
              v-model="form.classroom"
              type="text"
              placeholder="ex : B12, Amphi A"
              class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
          </div>

          <!-- Conflict error -->
          <div v-if="conflictError" class="rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 px-3 py-2.5">
            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ conflictError }}</p>
            <p v-if="conflictDetail" class="text-xs text-red-600 dark:text-red-400 mt-0.5">{{ conflictDetail }}</p>
          </div>

        </div>

        <!-- Footer -->
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 flex justify-end space-x-3">
          <button
            @click="$emit('close')"
            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
          >
            Annuler
          </button>
          <button
            @click="submit"
            :disabled="loading || !canSubmit"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Enregistrement...' : 'Enregistrer' }}
          </button>
        </div>

      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import axios from '@/api/axios'
import schedulesApi from '@/api/endpoints/schedules'

// ── Props & Emits ─────────────────────────────────────────────────────────────
const props = defineProps({
  specialty:  { type: Object, required: true },
  group:      { type: String, default: null },
  session:    { type: Object, required: true },
  day:        { type: String, required: true },
  startTime:  { type: String, required: true },
  endTime:    { type: String, required: true },
})

const emit = defineEmits(['close', 'saved'])

// ── State ─────────────────────────────────────────────────────────────────────
const loading        = ref(false)
const loadingModules = ref(false)
const loadingTeachers= ref(false)
const modules        = ref([])
const moduleTeachers = ref([])
const errors         = ref({})
const conflictError  = ref('')
const conflictDetail = ref('')

const form = reactive({ module_id: '', teacher_id: '', classroom: '' })

const canSubmit = computed(() => form.module_id && form.teacher_id)

// ── Day label ─────────────────────────────────────────────────────────────────
const dayLabels = {
  saturday: 'Samedi', sunday: 'Dimanche', monday: 'Lundi',
  tuesday: 'Mardi', wednesday: 'Mercredi', thursday: 'Jeudi',
}
const dayLabel = computed(() => dayLabels[props.day] || props.day)

// ── Load modules on mount ─────────────────────────────────────────────────────
onMounted(async () => {
  loadingModules.value = true
  try {
    const res = await axios.get('/api/admin/modules', {
      params: {
        specialty_id: props.specialty.specialty_id,
        semester:     props.specialty.semester,
      },
    })
    // Keep only modules matching this semester
    modules.value = (res.data.modules || []).filter(m => m.semester === props.specialty.semester)
  } catch (e) {
    console.error('Failed to load modules:', e)
  } finally {
    loadingModules.value = false
  }
})

// ── Module changed → load teachers ────────────────────────────────────────────
async function onModuleChange() {
  form.teacher_id     = ''
  moduleTeachers.value = []
  conflictError.value  = ''
  if (!form.module_id) return

  loadingTeachers.value = true
  try {
    const res = await axios.get(`/api/admin/modules/${form.module_id}`)
    moduleTeachers.value = res.data.module?.teachers || []
  } catch (e) {
    console.error('Failed to load teachers:', e)
  } finally {
    loadingTeachers.value = false
  }
}

// ── Submit ────────────────────────────────────────────────────────────────────
async function submit() {
  errors.value        = {}
  conflictError.value = ''
  conflictDetail.value= ''

  if (!form.module_id)  { errors.value.module_id  = 'Requis'; return }
  if (!form.teacher_id) { errors.value.teacher_id = 'Requis'; return }

  loading.value = true
  try {
    await schedulesApi.createSchedule({
      session_id:    props.session.id,
      specialty_id:  props.specialty.specialty_id,
      semester:      props.specialty.semester,
      study_mode:    props.specialty.study_mode,
      group:         props.group ?? null,
      module_id:     parseInt(form.module_id),
      teacher_id:    parseInt(form.teacher_id),
      classroom:     form.classroom || null,
      day:           props.day,
      start_time:    props.startTime,
      end_time:      props.endTime,
      academic_year: props.session.academic_year || '2025-2026',
    })
    emit('saved')
  } catch (e) {
    const data = e.response?.data
    if (data?.conflict) {
      conflictError.value  = data.message
      const c = data.conflict
      conflictDetail.value = c.specialty
        ? `${c.specialty} — ${c.module} (${c.time})`
        : `${c.module} — Groupe ${c.group ?? 'tous'} (${c.time})`
    } else {
      conflictError.value = data?.message || 'Une erreur est survenue.'
    }
  } finally {
    loading.value = false
  }
}
</script>
