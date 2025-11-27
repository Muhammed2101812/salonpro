<template>
  <div class="space-y-1">
    <label v-if="label" :for="name" class="block text-sm font-medium text-gray-700">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <Field
      :id="name"
      :name="name"
      :disabled="disabled || loading"
      v-slot="{ field, errors }"
    >
      <div class="relative">
        <select
          v-bind="field"
          :disabled="disabled || loading"
          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
          :class="{
            'border-red-300 focus:border-red-500 focus:ring-red-500': errors.length > 0,
            'bg-gray-100 cursor-not-allowed': disabled || loading,
          }"
        >
          <option value="" disabled>
            {{ loading ? 'Yükleniyor...' : (placeholder || 'Seçiniz...') }}
          </option>
          <option
            v-for="option in options"
            :key="option.value"
            :value="option.value"
          >
            {{ option.label }}
          </option>
        </select>

        <!-- Loading Spinner -->
        <div v-if="loading" class="absolute inset-y-0 right-8 flex items-center pointer-events-none">
          <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>

        <!-- Refresh Button -->
        <button
          v-if="showRefresh && !loading"
          type="button"
          @click.prevent="$emit('refresh')"
          class="absolute inset-y-0 right-8 flex items-center text-gray-400 hover:text-gray-600"
          title="Yenile"
        >
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </button>
      </div>
      <p v-if="errors.length" class="mt-1 text-sm text-red-600">{{ errors[0] }}</p>
    </Field>
    <p v-if="hint" class="text-xs text-gray-500">{{ hint }}</p>

    <!-- Error loading message -->
    <p v-if="error" class="mt-1 text-sm text-red-600 flex items-center gap-1">
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      {{ error }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { Field } from 'vee-validate'

interface Option {
  value: string | number
  label: string
}

interface Props {
  name: string
  label?: string
  options: Option[]
  placeholder?: string
  required?: boolean
  disabled?: boolean
  loading?: boolean
  error?: string | null
  hint?: string
  showRefresh?: boolean
}

withDefaults(defineProps<Props>(), {
  required: false,
  disabled: false,
  loading: false,
  error: null,
  showRefresh: false,
})

defineEmits<{
  refresh: []
}>()
</script>
