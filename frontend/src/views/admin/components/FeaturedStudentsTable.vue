<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
defineProps({
  students: {
    type: Array,
    default: () => []
  },
  isTeachers: {
    type: Boolean,
    default: false
  }
})

const getYearLabel = (year) => {
  if (year === '-') return '-'
  return `${year}${year === 1 ? 'ère' : 'ème'} Année`
}
</script>

<template>
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead class="bg-gray-50 dark:bg-gray-800/50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            {{ isTeachers ? t('common.teacher') : t('common.student') }}
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            {{ isTeachers ? t('common.email') : t('admin.featured_students_table.numero_inscription') }}
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            {{ t('common.specialty') }}
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            {{ t('admin.featured_students_table.annee') }}
          </th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        <tr 
          v-for="student in students" 
          :key="student.id"
          class="hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors duration-150"
        >
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center">
              <div class="flex-shrink-0 h-10 w-10">
                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                  {{ student.avatar_initials }}
                </div>
              </div>
              <div class="ml-4">
                <div class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ student.name }}
                </div>
              </div>
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm text-gray-500 dark:text-gray-400 font-mono">
              {{ student.registration_number }}
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm text-gray-900 dark:text-gray-300">
              {{ student.specialty }}
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm text-gray-500 dark:text-gray-400">
              {{ getYearLabel(student.year) }}
            </div>
          </td>
        </tr>
        <tr v-if="students.length === 0">
          <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
            {{ isTeachers ? t('admin.featured_students_table.aucun_formateur') : t('admin.featured_students_table.aucun_stagiaire') }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
