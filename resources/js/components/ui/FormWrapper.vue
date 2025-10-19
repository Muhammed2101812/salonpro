<template>
  <Form
    v-slot="{ errors, isSubmitting, values, meta }"
    :validation-schema="validationSchema"
    :initial-values="initialValues"
    @submit="handleSubmit"
  >
    <slot
      :errors="errors"
      :is-submitting="isSubmitting"
      :values="values"
      :meta="meta"
    />
  </Form>
</template>

<script setup lang="ts">
import { Form } from 'vee-validate'
import type { AnyObjectSchema } from 'yup'

interface Props {
  validationSchema?: AnyObjectSchema
  initialValues?: Record<string, any>
}

const props = withDefaults(defineProps<Props>(), {
  initialValues: () => ({}),
})

const emit = defineEmits<{
  submit: [values: Record<string, any>]
}>()

const handleSubmit = (values: Record<string, any>) => {
  emit('submit', values)
}
</script>
