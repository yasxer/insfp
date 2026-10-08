<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @click.self="$emit('close')">
    <div class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-lg shadow-xl">
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
          {{ isEdit ? 'Modifier l\'enseignant' : 'Ajouter un enseignant' }}
        </h3>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" aria-label="Fermer">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
      </div>

      <form @submit.prevent="submit" class="px-6 py-5 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prénom *</label>
            <input v-model.trim="form.first_name" type="text" required maxlength="100" :class="inputClass('first_name')" />
            <p v-if="errors.first_name" class="mt-1 text-xs text-red-600">{{ errors.first_name }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom *</label>
            <input v-model.trim="form.last_name" type="text" required maxlength="100" :class="inputClass('last_name')" />
            <p v-if="errors.last_name" class="mt-1 text-xs text-red-600">{{ errors.last_name }}</p>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email *</label>
          <input v-model.trim="form.email" type="email" required :class="inputClass('email')" />
          <p v-if="errors.email" class="mt-1 text-xs text-red-600">{{ errors.email }}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Téléphone</label>
            <input v-model.trim="form.phone" type="tel" pattern="0[5-7][0-9]{8}" placeholder="0612345678" title="Format : 0612345678" :class="inputClass('phone')" />
            <p v-if="errors.phone" class="mt-1 text-xs text-red-600">{{ errors.phone }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Spécialisation *</label>
            <input v-model.trim="form.specialization" type="text" required maxlength="255" placeholder="Ex : Développement web" :class="inputClass('specialization')" />
            <p v-if="errors.specialization" class="mt-1 text-xs text-red-600">{{ errors.specialization }}</p>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Mot de passe {{ isEdit ? '(laisser vide pour ne pas le changer)' : '*' }}
          </label>
          <input v-model="form.password" type="password" :required="!isEdit" minlength="8" autocomplete="new-password" :class="inputClass('password')" />
          <p v-if="errors.password" class="mt-1 text-xs text-red-600">{{ errors.password }}</p>
        </div>

        <div class="flex justify-end gap-3 pt-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600">
            Annuler
          </button>
          <button type="submit" :disabled="saving" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50">
            {{ saving ? 'Enregistrement…' : (isEdit ? 'Enregistrer' : 'Ajouter') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  teacher: { type: Object, default: null },
  // async (payload) => void; throws an axios error on failure
  save: { type: Function, required: true },
})
const emit = defineEmits(['close', 'saved'])

const isEdit = computed(() => !!props.teacher)
const saving = ref(false)
const errors = ref({})
const form = ref({
  first_name: props.teacher?.first_name || '',
  last_name: props.teacher?.last_name || '',
  email: props.teacher?.email || '',
  phone: props.teacher?.phone || '',
  specialization: props.teacher?.specialization || '',
  password: '',
})

const inputClass = (field) => [
  'mt-1 block w-full rounded-md shadow-sm sm:text-sm dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500',
  errors.value[field] ? 'border-red-500' : 'border-gray-300 dark:border-gray-600',
]

const submit = async () => {
  saving.value = true
  errors.value = {}
  const payload = { ...form.value }
  if (!payload.phone) payload.phone = null
  if (isEdit.value && !payload.password) delete payload.password

  try {
    await props.save(payload)
    emit('saved')
  } catch (error) {
    // Laravel validation errors: { field: [message] }
    const fieldErrors = error.response?.data?.errors || {}
    errors.value = Object.fromEntries(Object.entries(fieldErrors).map(([k, v]) => [k, v[0]]))
  } finally {
    saving.value = false
  }
}
</script>
