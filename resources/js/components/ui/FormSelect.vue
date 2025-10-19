<template>
  <Field
    v-slot="{ field, errors, value }"
    :name="name"
    :rules="rules"
    :label="label"
  >
    <div class="w-full">
      <label v-if="label" :for="inputId" class="block text-sm font-medium text-gray-700 mb-1">
        {{ label }}
        <span v-if="required" class="text-red-500">*</span>
      </label>

      <select
        v-bind="field"
        :id="inputId"
        :value="value"
        :disabled="disabled"
        :required="required"
        :class="selectClasses(errors)"
      >
        <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
        <option
          v-for="option in options"
          :key="getOptionValue(option)"
          :value="getOptionValue(option)"
        >
          {{ getOptionLabel(option) }}
        </option>
      </select>

      <p v-if="errors.length > 0" class="mt-1 text-sm text-red-600">
        {{ errors[0] }}
      </p>

      <p v-else-if="hint" class="mt-1 text-sm text-gray-500">
        {{ hint }}
      </p>
    </div>
  </Field>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Field } from 'vee-validate'

interface Props {
  name: string
  label?: string
  placeholder?: string
  hint?: string
  disabled?: boolean
  required?: boolean
  rules?: any
  id?: string
  options: any[]
  optionValue?: string
  optionLabel?: string
}

const props = withDefaults(defineProps<Props>(), {
  disabled: false,
  required: false,
  optionValue: 'value',
  optionLabel: 'label',
})

const inputId = computed(() => props.id || `field-${props.name}`)

const getOptionValue = (option: any) => {
  if (typeof option === 'object') {
    return option[props.optionValue]
  }
  return option
}

const getOptionLabel = (option: any) => {
  if (typeof option === 'object') {
    return option[props.optionLabel]
  }
  return option
}

const selectClasses = (errors: string[]) => {
  const base = 'block w-full rounded-md shadow-sm transition-colors sm:text-sm'
  const padding = 'px-3 py-2'
  const state = errors.length > 0
    ? 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500'
    : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'
  const disabledClass = props.disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white'

  return `${base} ${padding} ${state} ${disabledClass}`
}
</script>
