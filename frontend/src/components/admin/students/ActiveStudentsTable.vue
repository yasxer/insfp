<template>
  <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
    <div class="flex flex-col">
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200 dark:border-gray-700 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                  <th scope="col" class="px-4 py-4 w-12">
                    <input
                      type="checkbox"
                      :checked="isAllSelected"
                      @change="$emit('select-all', $event.target.checked)"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer"
                    />
                  </th>
                  <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Name
                  </th>
                  <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    ID
                  </th>
                  <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Specialty
                  </th>
                  <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Year / Group
                  </th>
                  <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col" class="relative px-6 py-4">
                    <span class="sr-only">Actions</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-if="loading" class="animate-pulse">
                  <td colspan="7" class="px-6 py-5 text-center text-gray-500 dark:text-gray-400">Loading...</td>
                </tr>
                <tr v-else-if="students.length === 0">
                  <td colspan="7" class="px-6 py-5 text-center text-gray-500 dark:text-gray-400">No students found</td>
                </tr>
                <tr v-for="student in students" :key="student.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="px-4 py-5 w-12" @click.stop>
                    <input
                      type="checkbox"
                      :checked="isSelected(student.id)"
                      @change="$emit('select', student.id, $event.target.checked)"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer"
                    />
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap">
                    <router-link :to="`/admin/students/${student.id}`" class="flex items-center hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg p-1 -m-1">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-lg">
                          {{ student.first_name.charAt(0) }}
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                          {{ student.full_name }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                          {{ student.email }}
                        </div>
                      </div>
                    </router-link>
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 cursor-pointer" @click="$emit('view', student)">
                    {{ student.registration_number }}
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 cursor-pointer" @click="$emit('view', student)">
                    {{ student.specialty?.name || 'N/A' }}
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 cursor-pointer" @click="$emit('view', student)">
                    S{{ student.current_semester }} / {{ student.group || '-' }}
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap cursor-pointer" @click="$emit('view', student)">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                      :class="student.is_graduated ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'">
                      {{ student.is_graduated ? 'Graduated' : 'Enrolled' }}
                    </span>
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                    <button 
                      @click.stop="$emit('message-individual', student)" 
                      class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-4"
                      title="Send message to this student"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                      </svg>
                    </button>
                    <button @click.stop="$emit('edit', student)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-4">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                      </svg>
                    </button>
                    <button @click.stop="$emit('delete', student)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  students: Array,
  loading: Boolean,
  pagination: Object,
  selectedIds: {
    type: Array,
    default: () => []
  }
})

defineEmits(['edit', 'delete', 'view', 'select', 'select-all', 'message-individual'])

const isSelected = (id) => {
  return props.selectedIds.includes(id)
}

const isAllSelected = computed(() => {
  return props.students.length > 0 && props.students.every(s => props.selectedIds.includes(s.id))
})
</script>
