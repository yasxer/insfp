<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="close"></div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                {{ isEditing ? 'Edit Specialty' : 'Add New Specialty' }}
              </h3>
              <div class="mt-4 space-y-4">
                <!-- Name -->
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                  <input type="text" v-model="form.name" id="name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Computer Science">
                </div>

                <!-- Code -->
                <div>
                  <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                  <input type="text" v-model="form.code" id="code" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="CS">
                </div>

                <!-- Description -->
                <div>
                  <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                  <textarea v-model="form.description" id="description" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                </div>

                <!-- Duration -->
                <div>
                  <label for="duration" class="block text-sm font-medium text-gray-700">Duration (Years)</label>
                  <input type="number" step="0.5" v-model="form.duration_years" id="duration" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <!-- PDF Upload -->
                <div>
                  <label class="block text-sm font-medium text-gray-700">Program PDF</label>
                  <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                      <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                      <div class="flex text-sm text-gray-600">
                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                          <span>Upload a file</span>
                          <input id="file-upload" name="file-upload" type="file" class="sr-only" accept=".pdf" @change="handleFileUpload">
                        </label>
                        <p class="pl-1">or drag and drop</p>
                      </div>
                      <p class="text-xs text-gray-500">PDF up to 10MB</p>
                      <p v-if="selectedFile" class="text-sm text-green-600 mt-2">Selected: {{ selectedFile.name }}</p>
                    </div>
                  </div>
                </div>

                <!-- Cover Image Upload -->
                <div>
                  <label class="block text-sm font-medium text-gray-700">Cover Image</label>
                  <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                      <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                      <div class="flex text-sm text-gray-600">
                        <label for="image-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                          <span>Upload an image</span>
                          <input id="image-upload" name="image-upload" type="file" class="sr-only" accept="image/*" @change="handleImageUpload">
                        </label>
                        <p class="pl-1">or drag and drop</p>
                      </div>
                      <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                      <p v-if="selectedImage" class="text-sm text-green-600 mt-2">Selected: {{ selectedImage.name }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm" @click="submit" :disabled="loading">
            {{ loading ? 'Saving...' : 'Save' }}
          </button>
          <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="close">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'

const props = defineProps({
  isOpen: Boolean,
  specialty: Object
})

const emit = defineEmits(['close', 'save'])

const isEditing = ref(false)
const loading = ref(false)
const selectedFile = ref(null)
const selectedImage = ref(null)

const form = reactive({
  name: '',
  code: '',
  description: '',
  duration_years: 2.5
})

watch(() => props.specialty, (newVal) => {
  if (newVal) {
    isEditing.value = true
    form.name = newVal.name
    form.code = newVal.code
    form.description = newVal.description
    form.duration_years = newVal.duration_years
  } else {
    isEditing.value = false
    form.name = ''
    form.code = ''
    form.description = ''
    form.duration_years = 2.5
  }
}, { immediate: true })

const handleFileUpload = (event) => {
  selectedFile.value = event.target.files[0]
}

const handleImageUpload = (event) => {
  selectedImage.value = event.target.files[0]
}

const close = () => {
  emit('close')
  selectedFile.value = null
  selectedImage.value = null
}

const submit = async () => {
  loading.value = true
  try {
    const formData = new FormData()
    formData.append('name', form.name)
    formData.append('code', form.code)
    formData.append('description', form.description || '')
    formData.append('duration_years', form.duration_years)
    
    if (selectedFile.value) {
      formData.append('program_pdf', selectedFile.value)
    }

    if (selectedImage.value) {
      formData.append('cover_image', selectedImage.value)
    }

    emit('save', formData)
  } finally {
    loading.value = false
  }
}
</script>
