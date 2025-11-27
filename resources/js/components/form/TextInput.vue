<template>
  <div class="space-y-1">
    <label v-if="label" :for="name" class="block text-sm font-medium text-gray-700">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <Field
      :id="name"
      :name="name"
      :type="type"
      :placeholder="placeholder"
      :disabled="disabled"
      v-slot="{ field, errors }"
    >
      <input
        v-bind="field"
        :type="type"
        :placeholder="placeholder"
        :disabled="disabled"
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
        :class="{
          'border-red-300 focus:border-red-500 focus:ring-red-500': errors.length > 0,
          'bg-gray-100 cursor-not-allowed': disabled,
        }"
      />
      <p v-if="errors.length" class="mt-1 text-sm text-red-600">{{ errors[0] }}</p>
    </Field>
    <p v-if="hint" class="text-xs text-gray-500">{{ hint }}</p>
  </div>
</template>

<script setup lang="ts">
import { Field } from 'vee-validate'

interface Props {
  name: string
  label?: string
  type?: string
  placeholder?: string
  required?: boolean
  disabled?: boolean
  hint?: string
}

withDefaults(defineProps<Props>(), {
  type: 'text',
  required: false,
  disabled: false,
})
</script>
