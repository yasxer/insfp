<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-opacity-70 overflow-y-auto h-full w-full z-50 flex items-center justify-center" @click.self="$emit('close')">
    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4">
      <!-- Header -->
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
          {{ recipientCount === 0 ? 'Broadcast Message' : `Send Message to ${recipientCount} Teacher${recipientCount > 1 ? 's' : ''}` }}
        </h3>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Body -->
      <div class="px-6 py-4">
        <form @submit.prevent="handleSubmit">
          <!-- Recipients Info -->
          <div class="mb-4 p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
            <p class="text-sm text-indigo-700 dark:text-indigo-300">
              <span class="font-medium">Recipients:</span> {{ recipientNames }}
            </p>
          </div>

          <!-- Subject -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Subject <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.subject"
              type="text"
              required
              placeholder="Enter message subject..."
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
            />
          </div>

          <!-- Message Content -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Message <span class="text-red-500">*</span>
            </label>
            <textarea
              v-model="form.message"
              required
              rows="6"
              placeholder="Type your message here..."
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white resize-none"
            ></textarea>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              {{ form.message.length }} characters
            </p>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
            <p class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
          </div>

          <!-- Actions -->
          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button
              type="button"
              @click="$emit('close')"
              class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="sending"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
            >
              <svg v-if="sending" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ sending ? 'Sending...' : 'Send Message' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  selectedTeachers: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['close', 'send'])

const form = ref({
  subject: '',
  message: ''
})

const sending = ref(false)
const error = ref(null)

const recipientCount = computed(() => props.selectedTeachers.length)

const recipientNames = computed(() => {
  if (props.selectedTeachers.length === 0) {
    return 'All teachers'
  }
  if (props.selectedTeachers.length <= 3) {
    return props.selectedTeachers.map(t => t.full_name).join(', ')
  }
  return `${props.selectedTeachers.slice(0, 2).map(t => t.full_name).join(', ')} and ${props.selectedTeachers.length - 2} others`
})

const handleSubmit = async () => {
  error.value = null
  
  if (!form.value.subject.trim() || !form.value.message.trim()) {
    error.value = 'Please fill in all required fields'
    return
  }

  sending.value = true
  
  try {
    await emit('send', {
      subject: form.value.subject,
      message: form.value.message,
      recipients: props.selectedTeachers.map(t => t.id)
    })
    
    // Reset form
    form.value = {
      subject: '',
      message: ''
    }
  } catch (err) {
    error.value = err.message || 'Failed to send message'
  } finally {
    sending.value = false
  }
}
</script>
