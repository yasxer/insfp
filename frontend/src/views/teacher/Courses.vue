<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Courses Management</h1>
    </div>

    <!-- Error Alert -->
    <div v-if="error" class="bg-red-50 dark:bg-red-900/50 p-4 rounded-lg flex items-start">
      <svg class="h-5 w-5 text-red-500 dark:text-red-400 mt-0.5 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
      </svg>
      <p class="text-sm text-red-800 dark:text-red-200">{{ error }}</p>
    </div>

    <div v-if="loading" class="flex justify-center py-8">
      <LoadingSpinner />
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Upload Form -->
      <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Upload Course Material</h2>
          
          <form @submit.prevent="submitForm" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Module</label>
              <select v-model="form.module_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm" required>
                <option value="" disabled>Select a module</option>
                <option v-for="module in modules" :key="module.id" :value="module.id">
                  {{ module.name }} ({{ module.code }})
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
              <input type="text" v-model="form.title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm" required>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description (Optional)</label>
              <textarea v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">File</label>
              <input type="file" @change="handleFileUpload" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-indigo-400" required>
            </div>

            <button type="submit" :disabled="uploading" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:bg-indigo-400">
              <span v-if="uploading">Uploading...</span>
              <span v-else>Upload Course</span>
            </button>
          </form>
        </div>
      </div>

      <!-- Modules and Courses List -->
      <div class="lg:col-span-2 space-y-6">
        <div v-if="modules.length === 0" class="bg-gray-50 dark:bg-gray-800 p-8 text-center rounded-lg border border-gray-200 dark:border-gray-700">
          <BookOpenIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No modules assigned</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You don't have any modules assigned to you yet.</p>
        </div>

        <div v-for="module in modules" :key="module.id" class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
          <div class="px-4 py-5 sm:px-6 flex justify-between items-center border-b border-gray-200 dark:border-gray-700">
            <div>
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">{{ module.name }}</h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">{{ module.code }}</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
              {{ module.lessons.length }} courses
            </span>
          </div>
          
          <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
            <li v-if="module.lessons.length === 0" class="px-4 py-4 sm:px-6 text-sm text-gray-500 dark:text-gray-400 text-center">
              No courses uploaded yet.
            </li>
            <li v-for="lesson in module.lessons" :key="lesson.id" class="px-4 py-4 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
              <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0 pr-4">
                  <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400 truncate">{{ lesson.title }}</p>
                  </div>
                  <div class="mt-2 flex">
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                      <DocumentIcon class="flex-shrink-0 mr-1.5 h-4 w-4" />
                      <p>{{ lesson.file_name }}</p>
                    </div>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <button @click="deleteLesson(lesson.id)" type="button" class="inline-flex items-center p-1.5 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <TrashIcon class="h-4 w-4" />
                  </button>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import teacherApi from '@/api/endpoints/teacherPortal'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { BookOpenIcon, DocumentIcon, TrashIcon } from '@heroicons/vue/24/outline'

const error = ref(null)
const loading = ref(true)
const uploading = ref(false)
const modules = ref([])

const form = ref({
  module_id: '',
  title: '',
  description: '',
  file: null
})

const fetchCourses = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await teacherApi.getCourses()
    modules.value = response.modules || []
  } catch (err) {
    error.value = 'Failed to load courses. Please try again.'
  } finally {
    loading.value = false
  }
}

const handleFileUpload = (event) => {
  const target = event.target
  if (target.files && target.files.length > 0) {
    form.value.file = target.files[0]
  }
}

const submitForm = async () => {
  if (!form.value.module_id || !form.value.title || !form.value.file) {
    error.value = 'Please fill out all required fields.'
    return
  }
  
  uploading.value = true
  error.value = null
  
  try {
    const formData = new FormData()
    formData.append('module_id', form.value.module_id)
    formData.append('title', form.value.title)
    if (form.value.description) {
      formData.append('description', form.value.description)
    }
    formData.append('file', form.value.file)
    
    await teacherApi.uploadCourse(formData)
    
    // Reset form
    form.value = {
      module_id: '',
      title: '',
      description: '',
      file: null
    }
    // Clear file input
    document.querySelector('input[type="file"]').value = ''
    
    // Refresh list
    await fetchCourses()
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to upload course.'
  } finally {
    uploading.value = false
  }
}

const deleteLesson = async (id) => {
  if (!confirm('Are you sure you want to delete this course?')) return
  
  try {
    await teacherApi.deleteCourse(id)
    await fetchCourses()
  } catch (err) {
    error.value = 'Failed to delete course.'
  }
}

onMounted(() => {
  fetchCourses()
})
</script>
