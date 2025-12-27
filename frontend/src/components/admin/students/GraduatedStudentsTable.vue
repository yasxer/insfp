<template>
  <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
      <thead class="bg-gray-50 dark:bg-gray-900">
        <tr>
          <th class="px-4 py-4 w-12">
            <input
              type="checkbox"
              :checked="isAllSelected"
              @change="$emit('select-all', $event.target.checked)"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer"
            />
          </th>
          <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</th>
          <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Email</th>
          <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Phone</th>
          <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Specialty</th>
          <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Graduated</th>
          <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Final GPA</th>
          <th class="px-6 py-4">Actions</th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        <tr v-if="loading">
          <td colspan="8" class="px-6 py-5 text-center text-gray-500 dark:text-gray-400">Loading...</td>
        </tr>
        <tr v-else-if="students.length === 0">
          <td colspan="8" class="px-6 py-5 text-center text-gray-500 dark:text-gray-400">No graduated students found</td>
        </tr>
        <tr v-for="student in students" :key="student.id" 
            class="hover:bg-gray-50 dark:hover:bg-gray-700">
          <td class="px-4 py-5 w-12" @click.stop>
            <input
              type="checkbox"
              :checked="isSelected(student.id)"
              @change="$emit('select', student.id, $event.target.checked)"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer"
            />
          </td>
          <td class="px-6 py-5">
            <router-link :to="`/admin/students/${student.id}`" class="flex items-center hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg p-1 -m-1">
              <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold">
                {{ student.first_name.charAt(0) }}
              </div>
              <div class="ml-4 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                {{ student.full_name }}
              </div>
            </router-link>
          </td>
          <td class="px-6 py-5 text-sm text-gray-500 dark:text-gray-400">{{ student.email }}</td>
          <td class="px-6 py-5 text-sm text-gray-500 dark:text-gray-400">{{ student.phone || 'N/A' }}</td>
          <td class="px-6 py-5 text-sm text-gray-500 dark:text-gray-400">{{ student.specialty?.name }}</td>
          <td class="px-6 py-5 text-sm text-gray-500 dark:text-gray-400">
            {{ student.graduation_year }} - S{{ student.graduation_semester }}
          </td>
          <td class="px-6 py-5 text-sm font-semibold text-gray-900 dark:text-white">
            {{ student.final_gpa ? Number(student.final_gpa).toFixed(2) : 'N/A' }}/20
          </td>
          <td class="px-6 py-5 text-right text-sm font-medium">
            <button 
              @click.stop="$emit('message-individual', student)" 
              class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3"
              title="Send message to this student"
            >
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
              </svg>
            </button>
            <button @click="$emit('view', student)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" title="View">
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </button>
            <button @click="$emit('delete', student)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" />
              </svg>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
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

defineEmits(['view', 'delete', 'select', 'select-all', 'message-individual'])

const isSelected = (id) => {
  return props.selectedIds.includes(id)
}

const isAllSelected = computed(() => {
  return props.students.length > 0 && props.students.every(s => props.selectedIds.includes(s.id))
})
</script>
