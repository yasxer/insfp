<template>
  <div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Students Management</h1>
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
          Add Student
        </button>
      </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6">
      <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8">
          <button
            @click="activeTab = 'active'"
            :class="[
              activeTab === 'active'
                ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300',
              'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
            ]"
          >
            Active Students
          </button>
          <button
            @click="activeTab = 'graduated'"
            :class="[
              activeTab === 'graduated'
                ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300',
              'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
            ]"
          >
            Graduated Students
          </button>
          <button
            @click="activeTab = 'pending'"
            :class="[
              activeTab === 'pending'
                ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300',
              'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
            ]"
          >
            Pending Registrations
          </button>
        </nav>
      </div>
    </div>

    <!-- Filters -->
    <StudentFilters v-if="activeTab !== 'pending'" :tab="activeTab" @update:filters="updateFilters" />

    <!-- Selection Bar -->
    <div v-if="selectedStudentIds.length > 0" class="mb-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-4 flex items-center justify-between">
      <div class="flex items-center space-x-3">
        <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
          <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
        </svg>
        <span class="text-sm font-medium text-indigo-900 dark:text-indigo-100">
          {{ selectedStudentIds.length }} student{{ selectedStudentIds.length > 1 ? 's' : '' }} selected
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

    <!-- Active Students Table -->
    <ActiveStudentsTable 
      v-if="activeTab === 'active'"
      :students="students"
      :loading="loading"
      :pagination="pagination"
      :selectedIds="selectedStudentIds"
      @edit="editStudent"
      @delete="confirmDelete"
      @view="viewStudent"
      @select="handleSelect"
      @select-all="handleSelectAll"
      @message-individual="handleMessageIndividual"
    />

    <!-- Graduated Students Table -->
    <GraduatedStudentsTable 
      v-else-if="activeTab === 'graduated'"
      :students="students"
      :loading="loading"
      :pagination="pagination"
      :selectedIds="selectedStudentIds"
      @delete="confirmDelete"
      @view="viewStudent"
      @select="handleSelect"
      @select-all="handleSelectAll"
      @message-individual="handleMessageIndividual"
    />

    <!-- Pending Students Table -->
    <PendingStudentsTable
      v-else-if="activeTab === 'pending'"
      :students="pendingStudents"
      :loading="loading"
      @approve="confirmApprove"
      @reject="confirmReject"
    />

    <!-- Pagination -->
    <!-- Removed pagination - showing all students -->

    <!-- Modals -->
    <StudentForm
      v-if="showFormModal"
      :student="selectedStudent"
      @close="closeFormModal"
      @save="handleSaveStudent"
    />

    <DeleteConfirmation
      v-if="showDeleteModal"
      @close="closeDeleteModal"
      @confirm="handleDeleteStudent"
    />

    <MessageComposer
      v-if="showMessageModal"
      :selectedStudents="selectedStudentsData"
      @close="closeMessageModal"
      @send="handleSendMessage"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useStudentsStore } from '@/stores/students'
import axios from '@/api/axios'
import StudentFilters from './StudentFilters.vue'
import StudentForm from './StudentForm.vue'
import DeleteConfirmation from './DeleteConfirmation.vue'
import ActiveStudentsTable from './ActiveStudentsTable.vue'
import GraduatedStudentsTable from './GraduatedStudentsTable.vue'
import PendingStudentsTable from './PendingStudentsTable.vue'
import MessageComposer from './MessageComposer.vue'

const studentsStore = useStudentsStore()

const students = computed(() => studentsStore.students)
const pendingStudents = computed(() => studentsStore.pendingStudents)
const loading = computed(() => studentsStore.loading)
const pagination = computed(() => studentsStore.pagination)

const activeTab = ref('active')
const showFormModal = ref(false)
const showDeleteModal = ref(false)
const showMessageModal = ref(false)
const selectedStudent = ref(null)
const messageTarget = ref(null)
const selectedStudentIds = ref([])

const selectedStudentsData = computed(() => {
  if (messageTarget.value) {
    return [messageTarget.value]
  }
  return students.value.filter(s => selectedStudentIds.value.includes(s.id))
})

// Watch tab change
watch(activeTab, (newTab) => {
  clearSelection() // Clear selection when switching tabs

  if (newTab === 'pending') {
    studentsStore.fetchPendingStudents()
    return
  }

  const isGraduated = newTab === 'graduated'
  studentsStore.setFilters({ is_graduated: isGraduated })
  studentsStore.fetchStudents(1)
})

onMounted(() => {
  studentsStore.setFilters({ is_graduated: false })
  studentsStore.fetchStudents()
})

// Selection methods
const handleSelect = (studentId, checked) => {
  if (checked) {
    if (!selectedStudentIds.value.includes(studentId)) {
      selectedStudentIds.value.push(studentId)
    }
  } else {
    selectedStudentIds.value = selectedStudentIds.value.filter(id => id !== studentId)
  }
}

const handleSelectAll = (checked) => {
  if (checked) {
    selectedStudentIds.value = students.value.map(s => s.id)
  } else {
    selectedStudentIds.value = []
  }
}

const clearSelection = () => {
  selectedStudentIds.value = []
}

const openMessageComposer = () => {
  messageTarget.value = null
  showMessageModal.value = true
}

const handleMessageIndividual = (student) => {
  // Set single student target and open modal
  messageTarget.value = student
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
      // Individual message from icon
      payload.recipient_type = 'individual'
      payload.recipient_ids = [messageTarget.value.id]
    } else if (selectedStudentIds.value.length === 0) {
      // Should not happen as button is hidden, but fallback to broadcast
      payload.recipient_type = 'all'
      payload.is_graduated = activeTab.value === 'graduated'
    } else {
      // Send to selected students (as 'all' type per requirement)
      payload.recipient_type = 'all'
      payload.recipient_ids = messageData.recipients
    }

    const response = await axios.post('/api/admin/messages/send', payload)
    
    // Show success message
    alert(response.data.message || `Message sent successfully to ${response.data.recipient_count} student(s)`)
    
    closeMessageModal()
    if (!messageTarget.value) {
      clearSelection()
    }
  } catch (error) {
    console.error('Failed to send message', error)
    alert('Failed to send message: ' + (error.response?.data?.message || error.message))
  }
}

const updateFilters = (filters) => {
  studentsStore.setFilters(filters)
  studentsStore.fetchStudents(1)
}

const refreshList = () => {
  studentsStore.fetchStudents(pagination.value.current_page)
}

const changePage = (page) => {
  studentsStore.fetchStudents(page)
}

const openAddModal = () => {
  selectedStudent.value = null
  showFormModal.value = true
}

const editStudent = (student) => {
  selectedStudent.value = student
  showFormModal.value = true
}

const viewStudent = (student) => {
  console.log('View student details', student)
  // TODO: Implement student details view (Phase 2)
}

const closeFormModal = () => {
  showFormModal.value = false
  selectedStudent.value = null
}

const handleSaveStudent = async (studentData) => {
  try {
    if (selectedStudent.value) {
      await studentsStore.updateStudent(selectedStudent.value.id, studentData)
    } else {
      await studentsStore.createStudent(studentData)
    }
    closeFormModal()
  } catch (error) {
    console.error('Failed to save student', error)
    // Handle error (show toast, etc.)
  }
}

const confirmDelete = (student) => {
  selectedStudent.value = student
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  selectedStudent.value = null
}

const handleDeleteStudent = async () => {
  if (selectedStudent.value) {
    try {
      await studentsStore.deleteStudent(selectedStudent.value.id)
      closeDeleteModal()
    } catch (error) {
      console.error('Failed to delete student', error)
    }
  }
}

const confirmApprove = async (student) => {
  if (confirm(`Approve registration for ${student.full_name}?`)) {
    try {
      await studentsStore.approveStudent(student.id)
    } catch (error) {
      console.error('Failed to approve student', error)
      alert(error.response?.data?.message || 'Failed to approve student')
    }
  }
}

const confirmReject = async (student) => {
  const reason = prompt(`Reason for rejecting ${student.full_name}?`)
  if (reason !== null) { // If not cancelled
    try {
      await studentsStore.rejectStudent(student.id, reason)
    } catch (error) {
       console.error('Failed to reject student', error)
       alert(error.response?.data?.message || 'Failed to reject student')
    }
  }
}

const toggleSelection = (student) => {
  // Implementation for selection
}
</script>
