<template>
  <div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Teachers Management</h1>
      <div class="flex space-x-3">
        <button
          @click="refreshList"
          class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Refresh
        </button>
        <button
          @click="openAddModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Add Teacher
        </button>
      </div>
    </div>

    <!-- Filters -->
    <TeacherFilters @update:filters="updateFilters" />

    <!-- Selection Bar -->
    <div v-if="selectedTeacherIds.length > 0" class="mb-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-4 flex items-center justify-between">
      <div class="flex items-center space-x-3">
        <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
          <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
        </svg>
        <span class="text-sm font-medium text-indigo-900 dark:text-indigo-100">
          {{ selectedTeacherIds.length }} teacher{{ selectedTeacherIds.length > 1 ? 's' : '' }} selected
        </span>
      </div>
      <div class="flex items-center space-x-2">
        <button
          @click="clearSelection"
          class="px-3 py-1.5 text-sm font-medium text-indigo-700 dark:text-indigo-300 hover:text-indigo-900 dark:hover:text-indigo-100"
        >
          Clear
        </button>
        <button
          @click="openMessageComposer"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
          </svg>
          Send Message
        </button>
      </div>
    </div>

    <!-- Teachers Table -->
    <TeachersTable 
      :teachers="teachers"
      :loading="loading"
      :selectedIds="selectedTeacherIds"
      @edit="editTeacher"
      @delete="confirmDelete"
      @view="viewTeacher"
      @select="handleSelect"
      @select-all="handleSelectAll"
      @message-individual="handleMessageIndividual"
    />

    <!-- Message Composer Modal -->
    <MessageComposer
      v-if="showMessageModal"
      :selectedTeachers="selectedTeachersData"
      @close="closeMessageModal"
      @send="handleSendMessage"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useTeachersStore } from '@/stores/teachers'
import { useRouter } from 'vue-router'
import axios from '@/api/axios'
import TeacherFilters from './TeacherFilters.vue'
import TeachersTable from './TeachersTable.vue'
import MessageComposer from './MessageComposer.vue'

const router = useRouter()
const teachersStore = useTeachersStore()

const teachers = computed(() => teachersStore.teachers)
const loading = computed(() => teachersStore.loading)

const selectedTeacherIds = ref([])
const showMessageModal = ref(false)
const messageTarget = ref(null)

const selectedTeachersData = computed(() => {
  if (messageTarget.value) {
    return [messageTarget.value]
  }
  return teachers.value.filter(t => selectedTeacherIds.value.includes(t.id))
})

onMounted(() => {
  teachersStore.fetchTeachers()
})

const updateFilters = (filters) => {
  teachersStore.setFilters(filters)
  teachersStore.fetchTeachers()
}

const refreshList = () => {
  teachersStore.fetchTeachers()
}

// Selection methods
const handleSelect = (teacherId, checked) => {
  if (checked) {
    if (!selectedTeacherIds.value.includes(teacherId)) {
      selectedTeacherIds.value.push(teacherId)
    }
  } else {
    selectedTeacherIds.value = selectedTeacherIds.value.filter(id => id !== teacherId)
  }
}

const handleSelectAll = (checked) => {
  if (checked) {
    selectedTeacherIds.value = teachers.value.map(t => t.id)
  } else {
    selectedTeacherIds.value = []
  }
}

const clearSelection = () => {
  selectedTeacherIds.value = []
}

// Message methods
const openMessageComposer = () => {
  messageTarget.value = null
  showMessageModal.value = true
}

const handleMessageIndividual = (teacher) => {
  messageTarget.value = teacher
  showMessageModal.value = true
}

const closeMessageModal = () => {
  showMessageModal.value = false
  messageTarget.value = null
}

const handleSendMessage = async (messageData) => {
  try {
    const payload = {
      subject: messageData.subject,
      message: messageData.message
    }

    // Determine message type
    if (messageTarget.value) {
      // Individual message
      payload.recipient_type = 'individual'
      payload.recipient_ids = [messageTarget.value.id]
    } else {
      // Send to selected teachers
      payload.recipient_type = 'all'
      payload.recipient_ids = messageData.recipients
    }

    const response = await axios.post('/api/admin/messages/send', payload)
    
    alert(response.data.message || `Message sent successfully to ${response.data.recipient_count} teacher(s)`)
    
    closeMessageModal()
    if (!messageTarget.value) {
      clearSelection()
    }
  } catch (error) {
    console.error('Failed to send message', error)
    alert('Failed to send message: ' + (error.response?.data?.message || error.message))
  }
}

const openAddModal = () => {
  console.log('Add teacher')
}

const editTeacher = (teacher) => {
  // TODO: Implement edit teacher modal
  console.log('Edit teacher', teacher)
}

const viewTeacher = (teacher) => {
  router.push(`/admin/teachers/${teacher.id}`)
}

const confirmDelete = (teacher) => {
  // TODO: Implement delete confirmation
  console.log('Delete teacher', teacher)
}
</script>
