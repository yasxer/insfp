<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sessions (Promotions)</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">
          Gérer les sessions de formation et leurs spécialités
        </p>
      </div>
      <div class="flex gap-3">
        <button
          @click="showArchived = !showArchived"
          class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center gap-2"
        >
          <ArchiveBoxIcon class="w-5 h-5" />
          <span v-if="!showArchived">Voir les archives</span>
          <span v-else>Sessions actuelles</span>
        </button>
        <button
          @click="openCreateModal"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2"
        >
          <PlusIcon class="w-5 h-5" />
          Nouvelle Session
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Sessions List -->
    <div v-else-if="displayedSessions.length > 0" class="grid gap-6">
      <div
        v-for="session in displayedSessions"
        :key="session.id"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
      >
        <!-- Session Header -->
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-start">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                <AcademicCapIcon class="w-7 h-7 text-blue-600 dark:text-blue-400" />
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ session.name }}
                </h3>
                <div class="flex items-center gap-3 mt-1">
                  <span :class="statusBadgeClass(session.status)" class="px-2.5 py-0.5 rounded-full text-xs font-medium">
                    {{ session.status }}
                  </span>
                  <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                    <CalendarIcon class="w-4 h-4" />
                    {{ formatDate(session.start_date) }} → {{ formatDate(session.end_date) }}
                  </span>
                  <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                    <UsersIcon class="w-4 h-4" />
                    {{ session.students_count || 0 }} étudiants
                  </span>
                </div>
              </div>
            </div>
            <div class="flex gap-2">
              <button
                @click="openSessionDetails(session)"
                class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/50 rounded-lg transition-colors"
                title="Gérer les spécialités"
              >
                <Cog6ToothIcon class="w-5 h-5" />
              </button>
              <button
                @click="openEditModal(session)"
                class="p-2 text-gray-500 hover:text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/50 rounded-lg transition-colors"
                title="Modifier"
              >
                <PencilSquareIcon class="w-5 h-5" />
              </button>
              <button
                v-if="session.students_count === 0"
                @click="confirmDelete(session)"
                class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/50 rounded-lg transition-colors"
                title="Supprimer"
              >
                <TrashIcon class="w-5 h-5" />
              </button>
            </div>
          </div>
        </div>

        <!-- Specialties by Type -->
        <div class="p-5">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div
              v-for="typeGroup in session.specialties_by_type"
              :key="typeGroup.type"
              class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4"
            >
              <h4 class="font-medium text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <component :is="getTypeIcon(typeGroup.type)" class="w-5 h-5" :class="getTypeIconClass(typeGroup.type)" />
                {{ typeGroup.label }}
              </h4>
              <div v-if="typeGroup.specialties.length > 0" class="space-y-2">
                <div
                  v-for="specialty in typeGroup.specialties"
                  :key="specialty.id"
                  class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-lg px-3 py-2 text-sm"
                >
                  <span class="text-gray-700 dark:text-gray-300">{{ specialty.specialty_name }}</span>
                  <span class="text-xs text-gray-500">{{ specialty.specialty_code }}</span>
                </div>
              </div>
              <div v-else class="text-sm text-gray-400 dark:text-gray-500 italic">
                Aucune spécialité
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
      <FolderOpenIcon class="w-16 h-16 mx-auto text-gray-400 mb-4" />
      <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
        {{ showArchived ? 'Aucune session archivée' : 'Aucune session active' }}
      </h3>
      <p class="text-gray-500 dark:text-gray-400 mb-6">
        {{ showArchived ? 'Les sessions terminées apparaîtront ici.' : 'Commencez par créer votre première session.' }}
      </p>
      <button
        v-if="!showArchived"
        @click="openCreateModal"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
      >
        Créer une session
      </button>
    </div>

    <!-- Create/Edit Session Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" @click="closeModal"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            {{ editingSession ? 'Modifier la session' : 'Nouvelle session' }}
          </h3>
          
          <form @submit.prevent="saveSession" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mois</label>
                <select
                  v-model="formData.month"
                  required
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                >
                  <option v-for="(name, index) in months" :key="index" :value="index + 1">
                    {{ name }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Année</label>
                <select
                  v-model="formData.year"
                  required
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                >
                  <option v-for="year in years" :key="year" :value="year">
                    {{ year }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Preview -->
            <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4">
              <p class="text-sm text-blue-800 dark:text-blue-200">
                <strong>Session:</strong> {{ previewSessionName }}
              </p>
              <p class="text-sm text-blue-800 dark:text-blue-200 mt-1">
                <strong>Date de fin:</strong> {{ previewEndDate }}
              </p>
            </div>

            <div class="flex justify-end gap-3 pt-4">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                Annuler
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
              >
                {{ saving ? 'Enregistrement...' : (editingSession ? 'Modifier' : 'Créer') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Session Details Modal (Manage Specialties) -->
    <div v-if="showDetailsModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" @click="closeDetailsModal"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
          <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
            <div class="flex justify-between items-center">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ selectedSession?.name }} - Gestion des spécialités
              </h3>
              <button @click="closeDetailsModal" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <XMarkIcon class="w-6 h-6" />
              </button>
            </div>
          </div>

          <div class="p-6">
            <!-- Add Specialty Form -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-6">
              <h4 class="font-medium text-gray-900 dark:text-white mb-3">Ajouter une spécialité</h4>
              <form @submit.prevent="addSpecialty" class="flex flex-wrap gap-3">
                <div class="flex-1 min-w-[200px]">
                  <select
                    v-model="addSpecialtyForm.specialty_id"
                    required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                  >
                    <option value="">Sélectionner une spécialité</option>
                    <option v-for="specialty in specialties" :key="specialty.id" :value="specialty.id">
                      {{ specialty.name }} ({{ specialty.code }})
                    </option>
                  </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                  <select
                    v-model="addSpecialtyForm.study_type"
                    required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                  >
                    <option value="">Type d'étude</option>
                    <option value="presential">Présentiel</option>
                    <option value="apprentissage">Apprentissage</option>
                    <option value="cours_soir">Cours du soir</option>
                  </select>
                </div>
                <button
                  type="submit"
                  :disabled="addingSpecialty"
                  class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 flex items-center gap-2"
                >
                  <PlusIcon class="w-4 h-4" />
                  {{ addingSpecialty ? 'Ajout...' : 'Ajouter' }}
                </button>
              </form>
            </div>

            <!-- Specialties by Type -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div
                v-for="typeGroup in selectedSession?.specialties_by_type"
                :key="typeGroup.type"
                class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4"
              >
                <h4 class="font-medium text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                  <component :is="getTypeIcon(typeGroup.type)" class="w-5 h-5" :class="getTypeIconClass(typeGroup.type)" />
                  {{ typeGroup.label }}
                  <span class="ml-auto text-xs bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded-full">
                    {{ typeGroup.specialties.length }}
                  </span>
                </h4>
                <div v-if="typeGroup.specialties.length > 0" class="space-y-2">
                  <div
                    v-for="specialty in typeGroup.specialties"
                    :key="specialty.id"
                    class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-lg px-3 py-2"
                  >
                    <div>
                      <span class="text-sm text-gray-700 dark:text-gray-300">{{ specialty.specialty_name }}</span>
                      <span class="text-xs text-gray-500 ml-2">({{ specialty.specialty_code }})</span>
                    </div>
                    <button
                      v-if="specialty.students_count === 0"
                      @click="removeSpecialty(specialty.id)"
                      class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/50 rounded"
                      title="Retirer"
                    >
                      <XMarkIcon class="w-4 h-4" />
                    </button>
                    <span v-else class="text-xs text-gray-400">{{ specialty.students_count }} étud.</span>
                  </div>
                </div>
                <div v-else class="text-sm text-gray-400 dark:text-gray-500 italic py-4 text-center">
                  Aucune spécialité
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" @click="showDeleteModal = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/50 flex items-center justify-center">
              <ExclamationTriangleIcon class="w-6 h-6 text-red-600 dark:text-red-400" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirmer la suppression</h3>
          </div>
          <p class="text-gray-600 dark:text-gray-400 mb-6">
            Êtes-vous sûr de vouloir supprimer la session <strong>{{ sessionToDelete?.name }}</strong> ?
            Cette action est irréversible.
          </p>
          <div class="flex justify-end gap-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              Annuler
            </button>
            <button
              @click="deleteSession"
              :disabled="deleting"
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
            >
              {{ deleting ? 'Suppression...' : 'Supprimer' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="translate-x-full opacity-0"
      enter-to-class="translate-x-0 opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="translate-x-0 opacity-100"
      leave-to-class="translate-x-full opacity-0"
    >
      <div
        v-if="toast.show"
        :class="[
          'fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2',
          toast.type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'
        ]"
      >
        <CheckCircleIcon v-if="toast.type === 'success'" class="w-5 h-5" />
        <ExclamationCircleIcon v-else class="w-5 h-5" />
        {{ toast.message }}
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useSessionsStore } from '@/stores/sessions';
import {
  PlusIcon,
  PencilSquareIcon,
  TrashIcon,
  XMarkIcon,
  ArchiveBoxIcon,
  AcademicCapIcon,
  CalendarIcon,
  UsersIcon,
  Cog6ToothIcon,
  FolderOpenIcon,
  BuildingOfficeIcon,
  WrenchScrewdriverIcon,
  MoonIcon,
  CheckCircleIcon,
  ExclamationCircleIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline';

const sessionsStore = useSessionsStore();

// State
const showArchived = ref(false);
const showModal = ref(false);
const showDetailsModal = ref(false);
const showDeleteModal = ref(false);
const editingSession = ref(null);
const selectedSession = ref(null);
const sessionToDelete = ref(null);
const saving = ref(false);
const deleting = ref(false);
const addingSpecialty = ref(false);

const formData = ref({
  month: new Date().getMonth() + 1,
  year: new Date().getFullYear()
});

const addSpecialtyForm = ref({
  specialty_id: '',
  study_type: ''
});

const toast = ref({
  show: false,
  message: '',
  type: 'success'
});

// Computed
const loading = computed(() => sessionsStore.loading);
const specialties = computed(() => sessionsStore.specialties);

const displayedSessions = computed(() => {
  return showArchived.value ? sessionsStore.archivedSessions : sessionsStore.sessions;
});

const months = [
  'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
  'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
];

const years = computed(() => {
  const currentYear = new Date().getFullYear();
  return Array.from({ length: 10 }, (_, i) => currentYear - 3 + i);
});

const previewSessionName = computed(() => {
  if (!formData.value.month || !formData.value.year) return '-';
  return `Session ${months[formData.value.month - 1]} ${formData.value.year}`;
});

const previewEndDate = computed(() => {
  if (!formData.value.month || !formData.value.year) return '-';
  const startDate = new Date(formData.value.year, formData.value.month - 1, 1);
  startDate.setMonth(startDate.getMonth() + 30);
  return startDate.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
});

// Methods
const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
};

const statusBadgeClass = (status) => {
  const classes = {
    'en cours': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'à venir': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'terminée': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
  };
  return classes[status] || classes['en cours'];
};

const getTypeIcon = (type) => {
  const icons = {
    presential: BuildingOfficeIcon,
    apprentissage: WrenchScrewdriverIcon,
    cours_soir: MoonIcon
  };
  return icons[type] || AcademicCapIcon;
};

const getTypeIconClass = (type) => {
  const classes = {
    presential: 'text-blue-600 dark:text-blue-400',
    apprentissage: 'text-orange-600 dark:text-orange-400',
    cours_soir: 'text-purple-600 dark:text-purple-400'
  };
  return classes[type] || 'text-gray-600';
};

const openCreateModal = () => {
  editingSession.value = null;
  formData.value = {
    month: new Date().getMonth() + 1,
    year: new Date().getFullYear()
  };
  showModal.value = true;
};

const openEditModal = (session) => {
  editingSession.value = session;
  formData.value = {
    month: session.month,
    year: session.year
  };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingSession.value = null;
};

const openSessionDetails = async (session) => {
  try {
    await sessionsStore.fetchSession(session.id);
    selectedSession.value = sessionsStore.currentSession;
    showDetailsModal.value = true;
  } catch (error) {
    showToast('Erreur lors du chargement de la session', 'error');
  }
};

const closeDetailsModal = () => {
  showDetailsModal.value = false;
  selectedSession.value = null;
  addSpecialtyForm.value = { specialty_id: '', study_type: '' };
};

const confirmDelete = (session) => {
  sessionToDelete.value = session;
  showDeleteModal.value = true;
};

const saveSession = async () => {
  saving.value = true;
  try {
    if (editingSession.value) {
      await sessionsStore.updateSession(editingSession.value.id, formData.value);
      showToast('Session mise à jour avec succès', 'success');
    } else {
      await sessionsStore.createSession(formData.value);
      showToast('Session créée avec succès', 'success');
    }
    closeModal();
  } catch (error) {
    showToast(error.response?.data?.message || 'Une erreur est survenue', 'error');
  } finally {
    saving.value = false;
  }
};

const deleteSession = async () => {
  deleting.value = true;
  try {
    await sessionsStore.deleteSession(sessionToDelete.value.id);
    showDeleteModal.value = false;
    sessionToDelete.value = null;
    showToast('Session supprimée avec succès', 'success');
  } catch (error) {
    showToast(error.response?.data?.message || 'Erreur lors de la suppression', 'error');
  } finally {
    deleting.value = false;
  }
};

const addSpecialty = async () => {
  if (!addSpecialtyForm.value.specialty_id || !addSpecialtyForm.value.study_type) return;
  
  addingSpecialty.value = true;
  try {
    await sessionsStore.addSpecialtyToSession(selectedSession.value.id, addSpecialtyForm.value);
    selectedSession.value = sessionsStore.currentSession;
    addSpecialtyForm.value = { specialty_id: '', study_type: '' };
    showToast('Spécialité ajoutée avec succès', 'success');
  } catch (error) {
    showToast(error.response?.data?.message || 'Erreur lors de l\'ajout', 'error');
  } finally {
    addingSpecialty.value = false;
  }
};

const removeSpecialty = async (sessionSpecialtyId) => {
  try {
    await sessionsStore.removeSpecialtyFromSession(selectedSession.value.id, sessionSpecialtyId);
    selectedSession.value = sessionsStore.currentSession;
    showToast('Spécialité retirée avec succès', 'success');
  } catch (error) {
    showToast(error.response?.data?.message || 'Erreur lors de la suppression', 'error');
  }
};

const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type };
  setTimeout(() => {
    toast.value.show = false;
  }, 3000);
};

// Lifecycle
onMounted(async () => {
  try {
    await Promise.all([
      sessionsStore.fetchSessions(),
      sessionsStore.fetchSpecialties(),
      sessionsStore.fetchStudyTypes()
    ]);
  } catch (error) {
    console.error('Error loading data:', error);
    showToast('Erreur lors du chargement des données', 'error');
  }
});
</script>
