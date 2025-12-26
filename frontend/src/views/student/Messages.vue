<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import studentApi from '@/api/endpoints/student'
import Card from '@/components/common/Card.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { 
  EnvelopeIcon, 
  EnvelopeOpenIcon,
  UserIcon,
  CalendarIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const loading = ref(true)
const messages = ref([])
const selectedMessage = ref(null)
const error = ref(null)

const loadMessages = async () => {
  try {
    loading.value = true
    console.log('Loading messages...')
    const response = await studentApi.getMessages()
    console.log('Messages response:', response)
    // Handle paginated response
    messages.value = response.data || []
    console.log('Messages loaded:', messages.value.length)
  } catch (err) {
    console.error('Failed to load messages:', err)
    error.value = 'Failed to load messages'
  } finally {
    loading.value = false
  }
}

const openMessage = async (message) => {
  try {
    const data = await studentApi.getMessage(message.id)
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
    error.value = 'Failed to load message details'
  }
}

const closeMessage = () => {
  selectedMessage.value = null
}

const getRecipientTypeLabel = (type) => {
  const labels = {
    'all': 'All Students',
    'specialty': 'Specialty',
    'individual': 'Personal'
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
  <div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Messages</h1>

    <div v-if="loading" class="flex justify-center items-center h-64">
      <LoadingSpinner size="large" />
    </div>

    <div v-else-if="error" class="text-center py-12">
      <p class="text-red-600 dark:text-red-400">{{ error }}</p>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Messages List -->
      <div class="lg:col-span-1">
        <Card title="Inbox">
          <div class="space-y-2">
            <button
              v-for="message in messages"
              :key="message.id"
              @click="openMessage(message)"
              :class="[
                'w-full text-left p-4 rounded-lg transition-colors',
                message.is_read 
                  ? 'bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700' 
                  : 'bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 border-l-4 border-blue-500',
                selectedMessage?.id === message.id && 'ring-2 ring-blue-500'
              ]"
            >
              <div class="flex items-start justify-between mb-2">
                <div class="flex items-center gap-2">
                  <component 
                    :is="message.is_read ? EnvelopeOpenIcon : EnvelopeIcon" 
                    class="w-5 h-5 text-gray-400"
                  />
                  <span class="font-semibold text-sm text-gray-900 dark:text-white">
                    {{ message.sender.name }}
                  </span>
                </div>
                <span :class="['px-2 py-1 rounded text-xs font-medium', getRecipientTypeColor(message.recipient_type)]">
                  {{ getRecipientTypeLabel(message.recipient_type) }}
                </span>
              </div>
              <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                {{ message.subject }}
              </p>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ message.created_at }}
              </p>
            </button>

            <div v-if="messages.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
              <EnvelopeIcon class="w-16 h-16 mx-auto mb-4 opacity-50" />
              <p>No messages yet</p>
            </div>
          </div>
        </Card>
      </div>

      <!-- Message Detail -->
      <div class="lg:col-span-2">
        <Card v-if="selectedMessage">
          <template #title>
            <div class="flex items-center justify-between">
              <span>Message Details</span>
              <button 
                @click="closeMessage"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
              >
                ✕
              </button>
            </div>
          </template>

          <div class="space-y-4">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
              <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                {{ selectedMessage.subject }}
              </h2>
              
              <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex items-center gap-2">
                  <UserIcon class="w-4 h-4" />
                  <span>From: {{ selectedMessage.sender.name }}</span>
                  <span :class="['px-2 py-0.5 rounded text-xs capitalize', 
                    selectedMessage.sender.role === 'administration' 
                      ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400'
                      : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
                  ]">
                    {{ selectedMessage.sender.role }}
                  </span>
                </div>
                <div class="flex items-center gap-2">
                  <CalendarIcon class="w-4 h-4" />
                  <span>{{ selectedMessage.created_at }}</span>
                </div>
              </div>
            </div>

            <!-- Content -->
            <div class="prose dark:prose-invert max-w-none">
              <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                {{ selectedMessage.body }}
              </p>
            </div>
          </div>
        </Card>

        <div v-else class="flex items-center justify-center h-96 text-gray-500 dark:text-gray-400">
          <div class="text-center">
            <EnvelopeIcon class="w-20 h-20 mx-auto mb-4 opacity-30" />
            <p class="text-lg">Select a message to read</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
