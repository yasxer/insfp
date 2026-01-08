<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-200">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Mon Profil</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Gérez vos informations personnelles</p>
      </div>
      <div class="flex items-center space-x-3">
        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">
          Administrateur
        </span>
      </div>
    </div>

    <LoadingSpinner v-if="loading" />

    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
      
      <!-- Left Column: Profile Card -->
      <div class="md:col-span-1 space-y-6">
        <Card class="text-center p-6 border-t-4 border-t-purple-600">
          <div class="relative inline-block mb-4">
            <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-3xl font-bold mx-auto border-4 border-white shadow-md">
              {{ profile.first_name?.charAt(0) }}{{ profile.last_name?.charAt(0) }}
            </div>
            <div class="absolute bottom-0 right-0 bg-green-500 w-5 h-5 rounded-full border-2 border-white" title="Actif"></div>
          </div>
          
          <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ profile.first_name }} {{ profile.last_name }}</h2>
          <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">{{ profile.email }}</p>
          
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-sm text-left space-y-2 transition-colors duration-200">
            <div class="flex justify-between">
              <span class="text-gray-500 dark:text-gray-300">Poste:</span>
              <span class="font-medium text-gray-800 dark:text-white">{{ profile.position || 'Non défini' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-500 dark:text-gray-300">Membre depuis:</span>
              <span class="font-medium text-gray-800 dark:text-white">{{ profile.created_at || 'N/A' }}</span>
            </div>
          </div>
        </Card>
      </div>

      <!-- Right Column: Details & Edit -->
      <div class="md:col-span-2 space-y-6">
        
        <!-- Tabs -->
        <div class="flex border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-t-lg px-4 pt-4 transition-colors duration-200">
          <button 
            @click="activeTab = 'info'"
            :class="['px-4 py-2 font-medium text-sm focus:outline-none transition-colors duration-200 border-b-2', 
              activeTab === 'info' ? 'border-purple-600 text-purple-600 dark:text-purple-400 dark:border-purple-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200']"
          >
            Informations Personnelles
          </button>
          <button 
            @click="activeTab = 'settings'"
            :class="['px-4 py-2 font-medium text-sm focus:outline-none transition-colors duration-200 border-b-2', 
              activeTab === 'settings' ? 'border-purple-600 text-purple-600 dark:text-purple-400 dark:border-purple-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200']"
          >
            Paramètres & Sécurité
          </button>
        </div>

        <!-- Info Tab -->
        <div v-if="activeTab === 'info'" class="space-y-6">
          <Card>
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                <i class="fas fa-user-circle mr-2 text-purple-600 dark:text-purple-400"></i>
                Détails du Profil
              </h3>
              <button 
                v-if="!editMode" 
                @click="enableEdit" 
                class="text-sm bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 px-3 py-1.5 rounded-md hover:bg-purple-100 dark:hover:bg-purple-900/40 transition-colors"
              >
                <i class="fas fa-edit mr-1"></i> Modifier
              </button>
            </div>

            <!-- View Mode -->
            <div v-if="!editMode" class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Prénom</label>
                <p class="text-gray-800 dark:text-white font-medium">{{ profile.first_name }}</p>
              </div>
              <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Nom</label>
                <p class="text-gray-800 dark:text-white font-medium">{{ profile.last_name }}</p>
              </div>
              <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Email</label>
                <p class="text-gray-800 dark:text-white font-medium">{{ profile.email }}</p>
              </div>
              <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Téléphone</label>
                <p class="text-gray-800 dark:text-white font-medium">{{ profile.phone || '-' }}</p>
              </div>
              <div class="space-y-1 md:col-span-2">
                <label class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Poste / Fonction</label>
                <p class="text-gray-800 dark:text-white font-medium">{{ profile.position || '-' }}</p>
              </div>
            </div>

            <!-- Edit Mode -->
            <form v-else @submit.prevent="saveProfile" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prénom</label>
                  <input type="text" v-model="editableProfile.first_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                </div>
                <div class="space-y-1">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom</label>
                  <input type="text" v-model="editableProfile.last_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Téléphone</label>
                  <input type="tel" v-model="editableProfile.phone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                </div>
                <div class="space-y-1">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Poste</label>
                  <input type="text" v-model="editableProfile.position" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-4">
                <button type="button" @click="cancelEdit" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm">Cancel</button>
                <button type="submit" :disabled="saving" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm flex items-center">
                  <span v-if="saving" class="animate-spin mr-2"><i class="fas fa-spinner"></i></span>
                  Enregistrer
                </button>
              </div>
            </form>
          </Card>
        </div>

        <!-- Settings Tab -->
        <div v-if="activeTab === 'settings'" class="space-y-6">
          <Card>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center">
              <i class="fas fa-lock mr-2 text-purple-600 dark:text-purple-400"></i>
              Changer le mot de passe
            </h3>
            
            <form @submit.prevent="updatePassword" class="space-y-4 max-w-lg">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mot de passe actuel</label>
                <input type="password" v-model="passwordForm.current_password" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nouveau mot de passe</label>
                <input type="password" v-model="passwordForm.new_password" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmer le nouveau mot de passe</label>
                <input type="password" v-model="passwordForm.confirm_password" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
              </div>
              
              <div class="pt-2">
                <button type="submit" :disabled="saving" class="px-4 py-2 bg-purple-800 text-white rounded-md hover:bg-gray-900 text-sm">
                  Modifier le mot de passe
                </button>
              </div>
            </form>
          </Card>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Card from '@/components/common/Card.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import adminApi from '@/api/endpoints/admin'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/api/axios'

const authStore = useAuthStore()
const loading = ref(true)
const saving = ref(false)
const editMode = ref(false)
const activeTab = ref('info') 

const profile = ref({
  id: null,
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  position: '',
  created_at: ''
})

const editableProfile = ref({})

const passwordForm = ref({
  current_password: '',
  new_password: '',
  confirm_password: ''
})

const loadProfile = async () => {
  try {
    loading.value = true
    const data = await adminApi.getProfile()
    profile.value = data
  } catch (err) {
    console.error('Failed to load profile', err)
  } finally {
    loading.value = false
  }
}

const enableEdit = () => {
  editableProfile.value = {
    first_name: profile.value.first_name,
    last_name: profile.value.last_name,
    phone: profile.value.phone,
    position: profile.value.position
  }
  editMode.value = true
}

const cancelEdit = () => {
  editMode.value = false
}

const saveProfile = async () => {
  try {
    saving.value = true
    const response = await adminApi.updateProfile(editableProfile.value)
    
    // Optimistic update
    profile.value = { ...profile.value, ...editableProfile.value }

    // If backend returns the updated user object, use it
    if (response.user) {
        // Manually map fields if needed, or spread
        profile.value.first_name = response.user.first_name
        profile.value.last_name = response.user.last_name
        profile.value.phone = response.user.phone
        profile.value.position = response.user.position
    }
    
    editMode.value = false
    // You might want to use a toast notification here
  } catch (err) {
    console.error('Failed to update profile', err)
    alert('Erreur lors de la mise à jour du profil')
  } finally {
    saving.value = false
  }
}

const updatePassword = async () => {
  if (passwordForm.value.new_password !== passwordForm.value.confirm_password) {
    alert('Les mots de passe ne correspondent pas')
    return
  }
  
  try {
    saving.value = true
    await apiClient.post('/api/change-password', {
        current_password: passwordForm.value.current_password,
        new_password: passwordForm.value.new_password,
        new_password_confirmation: passwordForm.value.confirm_password
    })
    
    passwordForm.value = { current_password: '', new_password: '', confirm_password: '' }
    alert('Mot de passe modifié avec succès')
  } catch (err) {
    console.error('Failed to update password', err)
    alert('Erreur lors du changement de mot de passe: ' + (err.response?.data?.message || 'Erreur inconnue'))
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadProfile()
})
</script>