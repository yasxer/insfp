<template>
  <div>
    <label v-if="label" :for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <slot
      :id="name"
      :value="modelValue"
      :class="inputClass"
      :onInput="onInput"
      :onBlur="onBlur"
    >
      <input
        :id="name"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :class="inputClass"
        @input="onInput"
        @blur="onBlur"
      />
    </slot>
    <p v-if="error" class="mt-1 text-xs text-red-600 dark:text-red-400">{{ error }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  name: { type: String, required: true },
  label: { type: String, default: '' },
  type: { type: String, default: 'text' },
  modelValue: { type: [String, Number], default: '' },
  error: { type: String, default: '' },
  required: { type: Boolean, default: false },
  placeholder: { type: String, default: '' }
})

const emit = defineEmits(['update:modelValue', 'blur'])

const onInput = (event) => emit('update:modelValue', event.target.value)
const onBlur = (event) => emit('blur', event)

const inputClass = computed(() => [
  'mt-1 block w-full rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500',
  'dark:bg-gray-700 dark:text-white',
  props.error ? 'border-red-500 dark:border-red-500' : 'border-gray-300 dark:border-gray-600'
])
</script>
