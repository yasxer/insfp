<script setup>
import { ref, onMounted } from 'vue'
import teacherApi from '@/api/endpoints/teacherPortal'
import Card from '@/components/common/Card.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { 
  EnvelopeIcon, 
  EnvelopeOpenIcon,
  UserIcon,
  CalendarIcon
} from '@heroicons/vue/24/outline'

const loading = ref(true)
const messages = ref([])
const selectedMessage = ref(null)
const error = ref(null)

const loadMessages = async () => {
  try {
    loading.value = true
    const response = await teacherApi.getMessages()
    messages.value = response.data || []
  } catch (err) {
    console.error('Failed to load messages:', err)
    error.value = 'Erreur lors du chargement des messages'
  } finally {
    loading.value = false
  }
}

const openMessage = async (message) => {
  try {
    const data = await teacherApi.getMessage(message.id)
    selectedMessage.value = data
    
    // Mark as read in list
    const index = messages.value.findIndex(m => m.id === message.id)
    if (index !== -1) {
      messages.value[index].is_read = true
    }
    
    // Emit event to update sidebar badge count
    window.dispatchEvent(new CustomEvent('message-read'))
  } catch (err) {
    console.error('Failed to load message:', err)
    error.value = 'Erreur lors du chargement du message'
  }
}

const closeMessage = () => {
  selectedMessage.value = null
}

const getRecipientTypeLabel = (type) => {
  const labels = {
    'all': 'Général',
    'specialty': 'Spécialité',
    'individual': 'Personnel'
  }
  return labels[type] || type
}

const getRecipientTypeColor = (type) => {
  const colors = {
    'all': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    'specialty': 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
    'individual': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
  }
  return colors[type] || colors.all
}

onMounted(() => {
  loadMessages()
})
</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Messages</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Communications de l'administration
        </p>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center items-center h-64">
      <LoadingSpinner size="lg" />
    </div>

    <div v-else-if="error" class="text-center py-12">
      <p class="text-red-600 dark:text-red-400">{{ error }}</p>
      <button @click="loadMessages" class="mt-4 text-blue-600 hover:underline">Réessayer</button>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Messages List -->
      <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col h-[600px]">
          <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80">
            <h2 class="font-semibold text-gray-900 dark:text-white">Boîte de réception</h2>
          </div>
          <div class="overflow-y-auto flex-1 p-4 space-y-3">
            <div v-if="messages.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
              <EnvelopeIcon class="w-12 h-12 mx-auto mb-3 opacity-50 text-gray-400" />
              <p>Aucun message</p>
            </div>
            
            <button
              v-for="message in messages"
              :key="message.id"
              @click="openMessage(message)"
              :class="[
                'w-full text-left p-4 rounded-lg transition-colors border',
                message.is_read 
                  ? 'bg-white border-gray-100 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50' 
                  : 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800/30 border-l-4 border-l-blue-500',
                selectedMessage?.id === message.id && 'ring-2 ring-blue-500 border-transparent relative z-10'
              ]"
            >
              <div class="flex items-start justify-between mb-2">
                <div class="flex items-center gap-2 max-w-[70%]">
                  <component 
                    :is="message.is_read ? EnvelopeOpenIcon : EnvelopeIcon" 
                    class="w-4 h-4 flex-shrink-0"
                    :class="message.is_read ? 'text-gray-400' : 'text-blue-500'"
                  />
                  <span class="font-semibold text-sm text-gray-900 dark:text-white truncate">
                    {{ message.sender.name }}
                  </span>
                </div>
                <span :class="['px-2 py-0.5 rounded text-[10px] font-medium whitespace-nowrap', getRecipientTypeColor(message.recipient_type)]">
                  {{ getRecipientTypeLabel(message.recipient_type) }}
                </span>
              </div>
              <p class="text-sm font-medium text-gray-900 dark:text-white mb-1 line-clamp-1" :class="!message.is_read && 'font-bold'">
                {{ message.subject }}
              </p>
              <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                {{ new Date(message.created_at).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' }) }}
              </p>
            </button>
          </div>
        </div>
      </div>

      <!-- Message Detail -->
      <div class="lg:col-span-2">
        <div v-if="selectedMessage" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 h-[600px] flex flex-col">
          <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-start">
            <div class="space-y-4 w-full">
              <h2 class="text-xl font-bold text-gray-900 dark:text-white pr-8">
                {{ selectedMessage.subject }}
              </h2>
              
              <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex items-center gap-2">
                  <UserIcon class="w-4 h-4" />
                  <span class="font-medium">De: {{ selectedMessage.sender.name }}</span>
                  <span class="px-2 py-0.5 rounded text-xs capitalize bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                    Administration
                  </span>
                </div>
                <div class="flex items-center gap-2">
                  <CalendarIcon class="w-4 h-4" />
                  <span>{{ new Date(selectedMessage.created_at).toLocaleString('fr-FR') }}</span>
                </div>
              </div>
            </div>
            
            <button 
              @click="closeMessage"
              class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 absolute top-4 right-4"
            >
              ✕
            </button>
          </div>

          <div class="p-6 overflow-y-auto flex-1">
            <div class="prose dark:prose-invert max-w-none">
              <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">
                {{ selectedMessage.body }}
              </p>
            </div>
          </div>
        </div>

        <div v-else class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 border-dashed dark:border-gray-700 h-[600px] flex items-center justify-center text-gray-500 dark:text-gray-400">
          <div class="text-center">
            <EnvelopeOpenIcon class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" />
            <p class="text-lg font-medium text-gray-900 dark:text-gray-300">Sélectionnez un message</p>
            <p class="text-sm mt-1">Cliquez sur un message dans la liste pour le lire</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>