<template>
  <div class="p-6 space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Passages de semestre</h1>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        Étudiants ayant échoué un semestre (moyenne &lt; 10) — décidez au cas par cas.
      </p>
    </div>

    <!-- Status tabs -->
    <div class="flex gap-1 border-b border-gray-200 dark:border-gray-700">
      <button
        v-for="tab in tabs"
        :key="tab.value"
        @click="statusFilter = tab.value; fetchReviews()"
        class="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors"
        :class="statusFilter === tab.value
          ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
          : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700'"
      >
        {{ tab.label }}
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Empty -->
    <div v-else-if="reviews.length === 0" class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
      <p class="text-gray-500 dark:text-gray-400">
        {{ statusFilter === 'pending' ? 'Aucun étudiant en attente de décision.' : 'Aucun élément.' }}
      </p>
    </div>

    <!-- Table -->
    <div v-else class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
            <th class="px-4 py-3 font-medium">Étudiant</th>
            <th class="px-4 py-3 font-medium">Spécialité</th>
            <th class="px-4 py-3 font-medium">Semestre</th>
            <th class="px-4 py-3 font-medium">Moyenne</th>
            <th class="px-4 py-3 font-medium">Année</th>
            <th class="px-4 py-3 font-medium">Statut</th>
            <th class="px-4 py-3 font-medium text-right">Décision</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="r in reviews"
            :key="r.id"
            class="border-b border-gray-100 dark:border-gray-700/50 last:border-0"
          >
            <td class="px-4 py-3">
              <div class="font-medium text-gray-900 dark:text-white">{{ r.student_name }}</div>
              <div class="text-xs text-gray-400">{{ r.registration || '—' }}</div>
            </td>
            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ r.specialty || '—' }}</td>
            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">S{{ r.semester }}</td>
            <td class="px-4 py-3">
              <span class="font-semibold text-red-600 dark:text-red-400">{{ r.average ?? '—' }}</span>
            </td>
            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ r.academic_year }}</td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="statusBadge(r.status)">
                {{ statusLabel(r.status) }}
              </span>
            </td>
            <td class="px-4 py-3">
              <div v-if="r.status === 'pending'" class="flex flex-wrap gap-1.5 justify-end">
                <button @click="resolve(r, 'advance')" :disabled="busyId === r.id"
                  class="px-2.5 py-1 text-xs font-medium rounded-md text-green-700 bg-green-50 dark:bg-green-900/30 dark:text-green-300 hover:bg-green-100 disabled:opacity-50">
                  Faire passer
                </button>
                <button @click="resolve(r, 'redouble')" :disabled="busyId === r.id"
                  class="px-2.5 py-1 text-xs font-medium rounded-md text-amber-700 bg-amber-50 dark:bg-amber-900/30 dark:text-amber-300 hover:bg-amber-100 disabled:opacity-50">
                  Redoubler
                </button>
                <button @click="askExclude(r)" :disabled="busyId === r.id"
                  class="px-2.5 py-1 text-xs font-medium rounded-md text-red-700 bg-red-50 dark:bg-red-900/30 dark:text-red-300 hover:bg-red-100 disabled:opacity-50">
                  Exclure
                </button>
              </div>
              <span v-else class="text-xs text-gray-400">Traité</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Exclude confirmation -->
    <div v-if="excludeTarget" class="fixed inset-0 z-50 flex items-center justify-center px-4">
      <div class="absolute inset-0 bg-black/50" @click="excludeTarget = null"></div>
      <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Exclure l'étudiant</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
          {{ excludeTarget.student_name }} sera exclu du cycle. Cette action peut être motivée ci-dessous.
        </p>
        <input
          v-model="excludeReason"
          type="text"
          placeholder="Motif (facultatif)"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white mb-4"
        />
        <div class="flex justify-end gap-3">
          <button @click="excludeTarget = null"
            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
            Annuler
          </button>
          <button @click="confirmExclude" :disabled="busyId === excludeTarget.id"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50">
            Exclure
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import adminApi from '@/api/endpoints/admin'
import { useToastStore } from '@/stores/toast'

const toast = useToastStore()

const tabs = [
  { value: 'pending', label: 'À traiter' },
  { value: 'all', label: 'Tout' },
]

const loading = ref(false)
const reviews = ref([])
const statusFilter = ref('pending')
const busyId = ref(null)
const excludeTarget = ref(null)
const excludeReason = ref('')

const statusLabel = (s) => ({
  pending: 'En attente',
  redoubled: 'Redouble',
  advanced: 'Passé',
  excluded: 'Exclu',
}[s] || s)

const statusBadge = (s) => ({
  pending: 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
  redoubled: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
  advanced: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
  excluded: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
}[s] || 'bg-gray-100 text-gray-600')

async function fetchReviews() {
  loading.value = true
  try {
    const res = await adminApi.getAdvancementReviews(statusFilter.value)
    reviews.value = res.data || []
  } catch (e) {
    toast.error('Erreur lors du chargement des passages.')
  } finally {
    loading.value = false
  }
}

async function resolve(review, decision, reason = null) {
  busyId.value = review.id
  try {
    const res = await adminApi.resolveAdvancementReview(review.id, decision, reason)
    toast.success(res.message || 'Décision enregistrée.')
    await fetchReviews()
  } catch (e) {
    toast.error(e.response?.data?.message || 'Erreur lors de l\'enregistrement.')
  } finally {
    busyId.value = null
  }
}

function askExclude(review) {
  excludeTarget.value = review
  excludeReason.value = ''
}

async function confirmExclude() {
  const target = excludeTarget.value
  await resolve(target, 'exclude', excludeReason.value || null)
  excludeTarget.value = null
}

onMounted(fetchReviews)
</script>
