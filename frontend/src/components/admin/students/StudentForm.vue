<template>
  <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" aria-hidden="true" @click="$emit('close')"></div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                {{ isEditing ? t('admin.student_form.modifier_stagiaire') : t('admin.student_form.nouveau_stagiaire') }}
              </h3>
              <div class="mt-4">
                <form @submit.prevent="handleSubmit" class="space-y-4">
                  <!-- Personal Information -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('common.first_name') }}</label>
                      <input v-model="form.first_name" type="text" :class="['mt-1 block w-full rounded-md dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', fieldErrors.first_name ? 'border-red-500' : 'border-gray-300 dark:border-gray-600']">
                      <p v-if="fieldErrors.first_name" class="mt-1 text-xs text-red-600">{{ fieldErrors.first_name }}</p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('common.last_name') }}</label>
                      <input v-model="form.last_name" type="text" :class="['mt-1 block w-full rounded-md dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', fieldErrors.last_name ? 'border-red-500' : 'border-gray-300 dark:border-gray-600']">
                      <p v-if="fieldErrors.last_name" class="mt-1 text-xs text-red-600">{{ fieldErrors.last_name }}</p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('common.email') }}</label>
                      <input v-model="form.email" type="email" :class="['mt-1 block w-full rounded-md dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', fieldErrors.email ? 'border-red-500' : 'border-gray-300 dark:border-gray-600']">
                      <p v-if="fieldErrors.email" class="mt-1 text-xs text-red-600">{{ fieldErrors.email }}</p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('common.phone') }}</label>
                      <input v-model="form.phone" type="tel" placeholder="0612345678" :class="['mt-1 block w-full rounded-md dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', fieldErrors.phone ? 'border-red-500' : 'border-gray-300 dark:border-gray-600']">
                      <p v-if="fieldErrors.phone" class="mt-1 text-xs text-red-600">{{ fieldErrors.phone }}</p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('admin.student_form.date_naissance') }}</label>
                      <input v-model="form.date_of_birth" type="date" :min="MIN_BIRTH_DATE" :max="maxBirthDate()" :class="['mt-1 block w-full rounded-md dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', fieldErrors.date_of_birth ? 'border-red-500' : 'border-gray-300 dark:border-gray-600']">
                      <p v-if="fieldErrors.date_of_birth" class="mt-1 text-xs text-red-600">{{ fieldErrors.date_of_birth }}</p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('common.address') }}</label>
                      <input v-model="form.address" type="text" :class="['mt-1 block w-full rounded-md dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', fieldErrors.address ? 'border-red-500' : 'border-gray-300 dark:border-gray-600']">
                      <p v-if="fieldErrors.address" class="mt-1 text-xs text-red-600">{{ fieldErrors.address }}</p>
                    </div>
                  </div>

                  <hr class="my-4 border-gray-200 dark:border-gray-700">

                  <!-- Academic Information -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('admin.student_form.numero_dinscription') }}</label>
                      <input v-model="form.registration_number" type="text" :disabled="isEditing" :class="['mt-1 block w-full rounded-md dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm disabled:bg-gray-100 dark:disabled:bg-gray-600', fieldErrors.registration_number ? 'border-red-500' : 'border-gray-300 dark:border-gray-600']">
                      <p v-if="fieldErrors.registration_number" class="mt-1 text-xs text-red-600">{{ fieldErrors.registration_number }}</p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('common.specialty') }}</label>
                      <select v-model="form.specialty_id" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option v-for="specialty in specialties" :key="specialty.id" :value="specialty.id">
                          {{ specialty.name }}
                        </option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('admin.student_form.study_mode') }}</label>
                      <select v-model="form.study_mode" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="initial">{{ t('admin.student_form.initial') }}</option>
                        <option value="alternance">{{ t('admin.student_form.alternance') }}</option>
                        <option value="continue">{{ t('admin.student_form.continue') }}</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('admin.student_form.semestre_actuel') }}</label>
                      <select v-model="form.current_semester" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option v-for="n in 6" :key="n" :value="n">{{ t('admin.student_form.semestre', { p0: n }) }}</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('common.group') }}</label>
                      <select v-model="form.group" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option :value="null">{{ t('admin.student_form.none') }}</option>
                        <option value="A">{{ t('admin.student_form.groupe') }}</option>
                        <option value="B">{{ t('admin.student_form.groupe_b') }}</option>
                        <option value="C">{{ t('admin.student_form.groupe_c') }}</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('admin.student_form.annees_dinscription') }}</label>
                      <input v-model="form.years_enrolled" type="number" min="1" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                  </div>

                  <div v-if="error" class="text-red-600 dark:text-red-400 text-sm mt-2">
                    {{ error }}
                  </div>

                  <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="submit" :disabled="loading" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 text-base font-medium text-white sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                      {{ loading ? t('common.saving') : (isEditing ? t('common.save') : t('admin.student_form.creer_stagiaire')) }}
                    </button>
                    <button type="button" @click="$emit('close')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                      {{ t('common.cancel') }}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
import { ref, onMounted, computed } from 'vue'
import { maxBirthDate, MIN_BIRTH_DATE } from '@/utils/dates'
import axios from '@/api/axios'
import { studentSchema } from '@/validations/schemas'

const props = defineProps({
  student: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'save'])

const loading = ref(false)
const error = ref(null)
const fieldErrors = ref({})
const specialties = ref([])

const isEditing = computed(() => !!props.student)

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  date_of_birth: '',
  address: '',
  registration_number: '',
  specialty_id: '',
  study_mode: 'initial',
  current_semester: 1,
  group: null,
  years_enrolled: 1
})

onMounted(async () => {
  try {
    const response = await axios.get('/api/specialties')
    console.log('Specialties response:', response.data)
    specialties.value = response.data.specialties || response.data
  } catch (e) {
    console.error('Failed to fetch specialties', e)
  }

  if (props.student) {
    form.value = {
      ...props.student,
      specialty_id: props.student.specialty?.id || props.student.specialty_id
    }
  }
})

const handleSubmit = async () => {
  error.value = null
  fieldErrors.value = {}

  try {
    await studentSchema.validate(form.value, { abortEarly: false })
  } catch (validationError) {
    const errors = {}
    for (const issue of validationError.inner) {
      if (!errors[issue.path]) errors[issue.path] = issue.message
    }
    fieldErrors.value = errors
    error.value = t('admin.student_form.please_correct_the_errors_below')
    return
  }

  loading.value = true
  try {
    emit('save', form.value)
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}
</script>
