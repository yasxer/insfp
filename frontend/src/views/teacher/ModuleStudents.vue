<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import teacherApi from '@/api/endpoints/teacherPortal'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import {
  ArrowLeftIcon,
  EnvelopeIcon,
  PhoneIcon,
  IdentificationIcon,
  UserCircleIcon
} from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const moduleId = route.params.id

const loading = ref(true)
const moduleData = ref(null)
const students = ref([])

onMounted(async () => {
  try {
    const data = await teacherApi.getModuleStudents(moduleId)
    moduleData.value = data.module
    students.value = data.students || []
  } catch (error) {
    console.error('Failed to fetch module students:', error)
  } finally {
    loading.value = false
  }
})

const goBack = () => {
  router.push({ name: 'TeacherModules' })
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
      <button 
        @click="goBack"
        class="p-2 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors"
      >
        <ArrowLeftIcon class="w-5 h-5" />
      </button>
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
          {{ moduleData?.name || 'Chargement...' }}
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Module Code: {{ moduleData?.code || '...' }} | {{ students.length }} Étudiants inscrits
        </p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <LoadingSpinner size="lg" />
    </div>

    <!-- Content -->
    <template v-else>
      <div v-if="students.length === 0" class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <UserCircleIcon class="w-12 h-12 mx-auto text-gray-400 mb-4" />
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Aucun étudiant trouvé</h3>
        <p class="mt-1 text-gray-500">Il n'y a pas d'étudiants inscrits dans la spécialité de ce module.</p>
      </div>

      <!-- Students Table -->
      <div v-else class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Étudiant
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Matricule
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Contact
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="student in students" :key="student.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 flex items-center justify-center font-bold">
                      {{ student.full_name?.charAt(0) || 'E' }}
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ student.full_name }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <IdentificationIcon class="w-4 h-4 mr-1 text-gray-400" />
                    {{ student.registration_number }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex flex-col text-sm text-gray-500 dark:text-gray-400 space-y-1">
                    <div v-if="student.email" class="flex items-center">
                      <EnvelopeIcon class="flex-shrink-0 w-4 h-4 mr-1.5 text-gray-400" />
                      <span>{{ student.email }}</span>
                    </div>
                    <div v-if="student.phone" class="flex items-center">
                      <PhoneIcon class="flex-shrink-0 w-4 h-4 mr-1.5 text-gray-400" />
                      <span>{{ student.phone }}</span>
                    </div>
                    <span v-if="!student.email && !student.phone" class="text-gray-400 italic">Aucun contact</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>
  </div>
</template>