<script setup>
import { ref, onMounted } from 'vue'
import studentApi from '@/api/endpoints/student'
import Card from '@/components/common/Card.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { 
  BookOpenIcon, 
  DocumentArrowDownIcon,
  FolderIcon,
  ChevronRightIcon
} from '@heroicons/vue/24/outline'

const loading = ref(true)
const modules = ref([])
const selectedModule = ref(null)
const loadingDetails = ref(false)
const error = ref(null)

const loadModules = async () => {
  try {
    loading.value = true
    console.log('Loading modules...')
    const data = await studentApi.getLessonModules()
    console.log('Modules response:', data)
    modules.value = data.modules || []
    console.log('Modules loaded:', modules.value.length)
  } catch (err) {
    console.error('Failed to load modules:', err)
    error.value = 'Failed to load courses'
  } finally {
    loading.value = false
  }
}

const selectModule = async (module) => {
  try {
    loadingDetails.value = true
    console.log('Loading lessons for module:', module.id)
    const data = await studentApi.getModuleLessons(module.id)
    console.log('Module lessons response:', data)
    selectedModule.value = {
      id: data.module.id,
      name: data.module.name,
      code: data.module.code,
      lessons: data.lessons || []
    }
    console.log('Lessons loaded:', selectedModule.value.lessons.length)
  } catch (err) {
    console.error('Failed to load module details:', err)
    error.value = 'Failed to load lessons'
  } finally {
    loadingDetails.value = false
  }
}

const downloadLesson = async (lesson) => {
  try {
    const response = await studentApi.downloadLesson(lesson.id)
    
    // Create blob link to download
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', lesson.file_name)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
    
    // Mark as viewed in list
    if (selectedModule.value && selectedModule.value.lessons) {
      const index = selectedModule.value.lessons.findIndex(l => l.id === lesson.id)
      if (index !== -1) {
        selectedModule.value.lessons[index].is_viewed = true
      }
    }
  } catch (err) {
    console.error('Failed to download lesson:', err)
    alert('Failed to download file')
  }
}

onMounted(() => {
  loadModules()
})
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">My Courses</h1>

    <div v-if="loading" class="flex justify-center items-center h-64">
      <LoadingSpinner size="large" />
    </div>

    <div v-else-if="error" class="text-center py-12">
      <p class="text-red-600 dark:text-red-400">{{ error }}</p>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Modules List -->
      <div class="lg:col-span-1">
        <Card title="Modules">
          <div class="space-y-2">
            <button
              v-for="module in modules"
              :key="module.id"
              @click="selectModule(module)"
              :class="[
                'w-full text-left p-4 rounded-lg transition-colors flex items-center justify-between',
                selectedModule?.id === module.id
                  ? 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500'
                  : 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700'
              ]"
            >
              <div class="flex items-center gap-3">
                <FolderIcon class="w-5 h-5 text-gray-400" />
                <div>
                  <p class="font-medium text-gray-900 dark:text-white">{{ module.name }}</p>
                  <p class="text-xs text-gray-500">{{ module.code }} • {{ module.lessons_count }} lessons</p>
                </div>
              </div>
              <ChevronRightIcon class="w-4 h-4 text-gray-400" />
            </button>
          </div>
        </Card>
      </div>

      <!-- Lessons List -->
      <div class="lg:col-span-2">
        <Card v-if="selectedModule">
          <template #title>
            <div class="flex items-center gap-2">
              <BookOpenIcon class="w-5 h-5 text-blue-500" />
              <span>{{ selectedModule.name }} - Lessons</span>
            </div>
          </template>

          <div v-if="loadingDetails" class="flex justify-center py-12">
            <LoadingSpinner />
          </div>

          <div v-else-if="selectedModule.lessons && selectedModule.lessons.length > 0" class="space-y-3">
            <div 
              v-for="lesson in selectedModule.lessons" 
              :key="lesson.id"
              class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700"
            >
              <div class="flex items-start gap-3">
                <div class="mt-1 p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400">
                  <DocumentArrowDownIcon class="w-5 h-5" />
                </div>
                <div>
                  <h3 class="font-medium text-gray-900 dark:text-white">{{ lesson.title }}</h3>
                  <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ lesson.description }}</p>
                  <p class="text-xs text-gray-400 mt-2">Added {{ lesson.created_at }}</p>
                </div>
              </div>
              
              <button 
                @click="downloadLesson(lesson)"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 dark:text-blue-400 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 rounded-lg transition-colors"
              >
                Download
              </button>
            </div>
          </div>

          <div v-else class="text-center py-12 text-gray-500 dark:text-gray-400">
            <p>No lessons available for this module yet.</p>
          </div>
        </Card>

        <div v-else class="flex items-center justify-center h-64 text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
          <div class="text-center">
            <BookOpenIcon class="w-16 h-16 mx-auto mb-4 opacity-30" />
            <p>Select a module to view lessons</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
