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

      <div class="relative">
        <div v-if="$slots.prefix" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <slot name="prefix" />
        </div>

        <component
          :is="component"
          v-bind="{ ...field, ...$attrs }"
          :id="inputId"
          :type="type"
          :value="value"
          :placeholder="placeholder"
          :disabled="disabled"
          :readonly="readonly"
          :required="required"
          :class="inputClasses(errors)"
        />

        <div v-if="$slots.suffix" class="absolute inset-y-0 right-0 pr-3 flex items-center">
          <slot name="suffix" />
        </div>
      </div>

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
  type?: string
  placeholder?: string
  hint?: string
  disabled?: boolean
  readonly?: boolean
  required?: boolean
  rules?: any
  id?: string
  as?: string
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  disabled: false,
  readonly: false,
  required: false,
  as: 'input',
})

const inputId = computed(() => props.id || `field-${props.name}`)

const component = computed(() => {
  if (props.as === 'textarea') return 'textarea'
  if (props.as === 'select') return 'select'
  return 'input'
})

const inputClasses = (errors: string[]) => {
  const base = 'block w-full rounded-md shadow-sm transition-colors sm:text-sm'
  const padding = 'px-3 py-2'
  const state = errors.length > 0
    ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
    : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'
  const disabledClass = props.disabled || props.readonly ? 'bg-gray-100 cursor-not-allowed' : 'bg-white'

  // Add special styles for textarea
  const componentStyle = props.as === 'textarea' ? 'min-h-[100px] resize-y' : ''

  return `${base} ${padding} ${state} ${disabledClass} ${componentStyle}`
}
</script>
