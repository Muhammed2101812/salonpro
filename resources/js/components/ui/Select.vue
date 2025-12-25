<template>
  <div class="w-full">
    <label v-if="label" :for="selectId" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <div class="relative">
      <select
        :id="selectId"
        :value="modelValue"
        :disabled="disabled"
        :required="required"
        :class="selectClasses"
        @change="handleInput"
        @blur="$emit('blur', $event)"
        @focus="$emit('focus', $event)"
      >
        <option v-if="placeholder" value="" disabled selected>{{ placeholder }}</option>
        <option
          v-for="option in options"
          :key="getOptionValue(option)"
          :value="getOptionValue(option)"
        >
          {{ getOptionLabel(option) }}
        </option>
      </select>

      <!-- Chevron Icon -->
      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </div>
    </div>

    <p v-if="error" class="mt-1 text-sm text-red-600">
      {{ error }}
    </p>

    <p v-else-if="hint" class="mt-1 text-sm text-gray-500">
      {{ hint }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  modelValue?: string | number | null
  label?: string
  placeholder?: string
  error?: string
  hint?: string
  disabled?: boolean
  required?: boolean
  id?: string
  size?: 'sm' | 'md' | 'lg'
  options: any[]
  optionValue?: string
  optionLabel?: string
}

const props = withDefaults(defineProps<Props>(), {
  disabled: false,
  required: false,
  size: 'md',
  optionValue: 'value',
  optionLabel: 'label',
  options: () => [],
})

const emit = defineEmits<{
  'update:modelValue': [value: string | number]
  blur: [event: FocusEvent]
  focus: [event: FocusEvent]
}>()

const selectId = computed(() => props.id || `select-${Math.random().toString(36).substring(2, 9)}`)

const sizeClasses = {
  sm: 'h-8 py-1 pl-2 pr-8 text-xs',
  md: 'h-10 py-2 pl-3 pr-10 text-sm',
  lg: 'h-12 py-3 pl-4 pr-12 text-base',
}

const selectClasses = computed(() => {
  const base = 'block w-full appearance-none rounded-lg border shadow-sm transition-colors focus:outline-none bg-white'
  const size = sizeClasses[props.size]
  
  const state = props.error
    ? 'border-red-300 text-red-900 focus:ring-1 focus:ring-red-500 focus:border-red-500'
    : 'border-gray-300 focus:ring-1 focus:ring-primary focus:border-primary'
    
  const disabled = props.disabled ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'bg-white'

  return `${base} ${size} ${state} ${disabled}`
})

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

const handleInput = (event: Event) => {
  const target = event.target as HTMLSelectElement
  emit('update:modelValue', target.value)
}
</script>
