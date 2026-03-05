<template>
  <div class="space-y-4">

    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
          Emploi du Temps Global
          <span class="text-indigo-500 dark:text-indigo-400 font-normal text-base ml-2">{{ session.name }}</span>
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
          Tous les emplois du temps finalisés · {{ totalCount }} séance(s)
        </p>
      </div>
      <div class="flex items-center gap-2">
        <!-- Print button (always visible) -->
        <button
          @click="printTimetable"
          class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg transition-colors shadow-sm"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
          </svg>
          Imprimer
        </button>
        <button
          v-if="session.is_active"
          @click="$emit('notify')"
          class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors shadow-sm"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg>
          Notifier
        </button>
        <button
          v-if="session.is_active"
          @click="$emit('edit')"
          class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.172-8.172z"/>
          </svg>
          Modifier
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-2 items-center">
      <!-- Mode filter -->
      <div class="flex items-center gap-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-1">
        <button
          v-for="m in modeFilters"
          :key="m.value"
          @click="filterMode = m.value"
          class="px-3 py-1 text-xs font-medium rounded-md transition-colors"
          :class="filterMode === m.value
            ? 'bg-indigo-600 text-white shadow-sm'
            : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
        >
          {{ m.label }}
        </button>
      </div>

      <!-- Specialty filter -->
      <select
        v-model="filterSpecialty"
        class="text-sm py-1.5 px-3 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
      >
        <option value="">Toutes les spécialités</option>
        <option v-for="sp in specialtyOptions" :key="sp.id" :value="sp.id">{{ sp.name }}</option>
      </select>

      <!-- Legend -->
      <div class="ml-auto flex flex-wrap gap-2 items-center">
        <span
          v-for="item in legend"
          :key="item.key"
          class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-700 dark:text-gray-300"
        >
          <span class="inline-block w-3 h-3 rounded-sm flex-shrink-0" :style="{ background: item.color }"></span>
          {{ item.label }}
        </span>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-16">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Timetable grid -->
    <div v-else class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-x-auto">
      <table class="min-w-full table-fixed">
        <thead>
          <tr class="bg-gray-50 dark:bg-gray-700/60 border-b border-gray-200 dark:border-gray-700">
            <th class="w-20 px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Horaire
            </th>
            <th
              v-for="day in days"
              :key="day.key"
              class="px-2 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              style="min-width: 130px"
            >
              {{ day.label }}
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
          <tr
            v-for="slot in timeSlots"
            :key="slot.start"
            class="hover:bg-gray-50/30 dark:hover:bg-gray-800/30"
          >
            <!-- Time column -->
            <td class="px-3 py-2 align-top whitespace-nowrap">
              <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ slot.start }}</div>
              <div class="text-xs text-gray-400 dark:text-gray-500">{{ slot.end }}</div>
            </td>

            <!-- Day cells -->
            <td
              v-for="day in days"
              :key="`${day.key}-${slot.start}`"
              class="px-1.5 py-1.5 align-top"
            >
              <div class="space-y-1">
                <div
                  v-for="entry in getSlots(day.key, slot.start)"
                  :key="entry.id"
                  class="rounded-lg px-2 py-1.5 border-0 text-xs leading-tight shadow-sm"
                  :style="entryStyle(entry)"
                >
                  <p class="font-semibold truncate" :title="entry.module.name">{{ entry.module.name }}</p>
                  <p class="opacity-80 truncate">{{ entry.specialty.code }} · S{{ entry.semester }}</p>
                  <p class="opacity-75 truncate">{{ entry.teacher.full_name }}</p>
                  <div class="flex flex-wrap gap-0.5 mt-0.5">
                    <span class="opacity-60">{{ studyModeShort(entry.study_mode) }}</span>
                    <span v-if="entry.group" class="opacity-60">· Gr {{ entry.group }}</span>
                    <span v-if="entry.classroom" class="opacity-60">· {{ entry.classroom }}</span>
                  </div>
                </div>
                <!-- Empty indicator -->
                <div
                  v-if="getSlots(day.key, slot.start).length === 0"
                  class="h-[60px]"
                ></div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import schedulesApi from '@/api/endpoints/schedules'

// ── Props & Emits ─────────────────────────────────────────────────────────────
const props = defineProps({
  session: { type: Object, required: true },
})
const emit = defineEmits(['edit', 'notify'])

// ── State ─────────────────────────────────────────────────────────────────────
const loading        = ref(false)
const allSchedules   = ref([])
const filterMode     = ref('all')
const filterSpecialty= ref('')

// ── Constants ─────────────────────────────────────────────────────────────────
const days = [
  { key: 'saturday',  label: 'Samedi' },
  { key: 'sunday',    label: 'Dimanche' },
  { key: 'monday',    label: 'Lundi' },
  { key: 'tuesday',   label: 'Mardi' },
  { key: 'wednesday', label: 'Mercredi' },
  { key: 'thursday',  label: 'Jeudi' },
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

const modeFilters = [
  { value: 'all',        label: 'Tous' },
  { value: 'initial',    label: 'Initial' },
  { value: 'alternance', label: 'Alternance' },
  { value: 'continue',   label: 'Cours du soir' },
]

// Palette: one solid color per specialty
const palette = [
  { bg: '#6366F1', border: '#4F46E5', text: '#FFFFFF' }, // indigo
  { bg: '#10B981', border: '#059669', text: '#FFFFFF' }, // emerald
  { bg: '#F59E0B', border: '#D97706', text: '#FFFFFF' }, // amber
  { bg: '#8B5CF6', border: '#7C3AED', text: '#FFFFFF' }, // violet
  { bg: '#0EA5E9', border: '#0284C7', text: '#FFFFFF' }, // sky
  { bg: '#F43F5E', border: '#E11D48', text: '#FFFFFF' }, // rose
  { bg: '#14B8A6', border: '#0D9488', text: '#FFFFFF' }, // teal
  { bg: '#F97316', border: '#EA580C', text: '#FFFFFF' }, // orange
]

// ── Computed ──────────────────────────────────────────────────────────────────
const specialtyColorMap = computed(() => {
  const ids = [...new Set(allSchedules.value.map(s => s.specialty.id))]
  const map = {}
  ids.forEach((id, i) => { map[id] = palette[i % palette.length] })
  return map
})

const specialtyOptions = computed(() => {
  const seen = new Map()
  allSchedules.value.forEach(s => {
    if (!seen.has(s.specialty.id)) seen.set(s.specialty.id, { id: s.specialty.id, name: s.specialty.name })
  })
  return [...seen.values()].sort((a, b) => a.name.localeCompare(b.name))
})

const filteredSchedules = computed(() => {
  return allSchedules.value.filter(s => {
    if (filterMode.value !== 'all' && s.study_mode !== filterMode.value) return false
    if (filterSpecialty.value && s.specialty.id !== filterSpecialty.value) return false
    return true
  })
})

const totalCount = computed(() => filteredSchedules.value.length)

const legend = computed(() =>
  specialtyOptions.value.map(sp => ({
    key:   sp.id,
    label: sp.name,
    color: specialtyColorMap.value[sp.id]?.bg ?? '#EEF2FF',
  }))
)

// ── Fetch ─────────────────────────────────────────────────────────────────────
onMounted(fetchAll)
watch(() => props.session.id, fetchAll)

async function fetchAll() {
  loading.value = true
  try {
    const res = await schedulesApi.getSchedules({ session_id: props.session.id })
    allSchedules.value = res.data.schedules || []
  } catch (e) {
    console.error('Failed to load full timetable:', e)
  } finally {
    loading.value = false
  }
}

// ── Grid ──────────────────────────────────────────────────────────────────────
function getSlots(dayKey, startTime) {
  return filteredSchedules.value.filter(s =>
    s.day === dayKey && s.start_time.substring(0, 5) === startTime
  )
}

function entryStyle(entry) {
  const c = specialtyColorMap.value[entry.specialty.id] ?? palette[0]
  return {
    background:  c.bg,
    borderColor: c.border,
    color:       c.text,
  }
}

// ── Print / PDF ──────────────────────────────────────────────────────────────
function printTimetable() {
  const MODES = [
    { key: 'initial',    label: 'Initial (Présentiel)' },
    { key: 'alternance', label: 'Alternance' },
    { key: 'continue',   label: 'Cours du Soir' },
  ]
  const DAYS = [
    { key: 'saturday',   label: 'Samedi' },
    { key: 'sunday',     label: 'Dimanche' },
    { key: 'monday',     label: 'Lundi' },
    { key: 'tuesday',    label: 'Mardi' },
    { key: 'wednesday',  label: 'Mercredi' },
    { key: 'thursday',   label: 'Jeudi' },
  ]
  const SLOTS = [
    { start: '08:00', end: '09:30' },
    { start: '09:30', end: '11:00' },
    { start: '11:00', end: '12:30' },
    { start: '13:00', end: '14:30' },
    { start: '14:30', end: '16:00' },
    { start: '16:30', end: '18:00' },
    { start: '18:00', end: '19:30' },
  ]

  // Build specialty → color index map
  const specialtyIds = [...new Set(allSchedules.value.map(s => s.specialty.id))]
  const spIdx = {}
  specialtyIds.forEach((id, i) => { spIdx[id] = i })

  // Print colors (light bg for print readability)
  const printColors = [
    { bg: '#e8eaff', border: '#a5b4fc' },
    { bg: '#d1fae5', border: '#6ee7b7' },
    { bg: '#fef3c7', border: '#fcd34d' },
    { bg: '#ede9fe', border: '#c4b5fd' },
    { bg: '#e0f2fe', border: '#7dd3fc' },
    { bg: '#ffe4e6', border: '#fda4af' },
    { bg: '#ccfbf1', border: '#5eead4' },
    { bg: '#ffedd5', border: '#fdba74' },
  ]

  let sections = ''
  MODES.forEach((mode, mIdx) => {
    const mSchedules = allSchedules.value.filter(s => s.study_mode === mode.key)
    if (mSchedules.length === 0) return

    const rows = SLOTS.map(slot => {
      const tds = DAYS.map(day => {
        const entries = mSchedules.filter(
          s => s.day === day.key && s.start_time.substring(0, 5) === slot.start
        )
        if (!entries.length) return '<td class="cell empty"></td>'
        const inner = entries.map(e => {
          const c = printColors[spIdx[e.specialty.id] % printColors.length]
          return `<div class="entry" style="background:${c.bg};border-color:${c.border}">
            <b>${e.module.name}</b><br>
            <span>${e.specialty.code} S${e.semester}${e.group ? ' Gr ' + e.group : ''}</span><br>
            <i>${e.teacher.full_name}</i>
            ${e.classroom ? `<br><span>${e.classroom}</span>` : ''}
          </div>`
        }).join('')
        return `<td class="cell">${inner}</td>`
      }).join('')
      return `<tr>
        <td class="time">${slot.start}<br><span class="te">${slot.end}</span></td>
        ${tds}
      </tr>`
    }).join('')

    sections += `
      <div class="section" style="${mIdx > 0 ? 'page-break-before:always;' : ''}">
        <div class="mode-title">${mode.label}</div>
        <table>
          <thead><tr>
            <th style="width:58px">Horaire</th>
            ${DAYS.map(d => `<th>${d.label}</th>`).join('')}
          </tr></thead>
          <tbody>${rows}</tbody>
        </table>
      </div>`
  })

  if (!sections) {
    alert('Aucune séance à imprimer.')
    return
  }

  const html = `<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<title>Emploi du Temps – ${props.session.name}</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; font-size: 9px; color: #111; background: #fff; }
.header { text-align: center; padding-bottom: 10px; margin-bottom: 14px; border-bottom: 2px solid #222; }
.header h1 { font-size: 16px; font-weight: 800; letter-spacing: .5px; }
.header h2 { font-size: 11px; color: #555; margin-top: 3px; }
.mode-title { font-size: 12px; font-weight: bold; margin-bottom: 6px;
  padding: 4px 10px; background: #1e293b; color: #fff; border-radius: 3px; }
.section { margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; table-layout: fixed; }
th { background: #334155; color: #fff; padding: 5px 3px; text-align: center;
  font-size: 9px; border: 1px solid #475569; }
td { border: 1px solid #cbd5e1; vertical-align: top; padding: 2px; min-height: 50px; }
td.time { background: #f8fafc; text-align: center; font-weight: 700; font-size: 9px;
  color: #334155; width: 58px; }
.te { font-weight: 400; color: #64748b; }
td.empty { background: #f8fafc; }
.entry { border: 1px solid; border-radius: 2px; padding: 2px 3px; margin-bottom: 2px; font-size: 8.5px; }
.entry b { display: block; font-size: 9px; }
.entry span { color: #444; }
.entry i { color: #555; font-size: 8px; }
@media print {
  @page { size: A4 landscape; margin: 8mm; }
  .section { page-break-inside: avoid; }
}
</style></head>
<body>
  <div class="header">
    <h1>INSFP – Emploi du Temps</h1>
    <h2>${props.session.name}${props.session.academic_year ? ' &nbsp;·&nbsp; Année ' + props.session.academic_year : ''}</h2>
  </div>
  ${sections}
</body></html>`

  const win = window.open('', '_blank', 'width=1000,height=720')
  win.document.write(html)
  win.document.close()
  win.focus()
  setTimeout(() => win.print(), 600)
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function studyModeShort(mode) {
  return { initial: 'Init', alternance: 'Alt', continue: 'Soir' }[mode] || mode
}
</script>
