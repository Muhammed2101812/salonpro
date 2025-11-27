<template>
  <Form
    @submit="onSubmit"
    :validation-schema="validationSchema"
    :initial-values="initialValues"
    v-slot="{ errors, isSubmitting }"
    class="space-y-6"
  >
    <slot :errors="errors" :isSubmitting="isSubmitting" />

    <div v-if="showActions" class="flex justify-end gap-3 pt-4 border-t">
      <button
        type="button"
        @click="$emit('cancel')"
        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
        :disabled="isSubmitting"
      >
        İptal
      </button>
      <button
        type="submit"
        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
        :disabled="isSubmitting"
      >
        <span v-if="isSubmitting" class="flex items-center gap-2">
          <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Kaydediliyor...
        </span>
        <span v-else>{{ submitText }}</span>
      </button>
    </div>
  </Form>
</template>

<script setup lang="ts">
import { Form } from 'vee-validate'
import type { AnyObjectSchema } from 'yup'

interface Props {
  validationSchema: AnyObjectSchema
  initialValues?: Record<string, any>
  submitText?: string
  showActions?: boolean
}

withDefaults(defineProps<Props>(), {
  submitText: 'Kaydet',
  showActions: true,
})

const emit = defineEmits<{
  submit: [values: Record<string, any>]
  cancel: []
}>()

const onSubmit = (values: Record<string, any>) => {
  emit('submit', values)
}
</script>
