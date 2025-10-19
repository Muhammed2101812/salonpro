<template>
  <FormWrapper
    :validation-schema="customerSchema"
    :initial-values="initialValues"
    @submit="handleSubmit"
  >
    <template #default="{ isSubmitting, errors }">
      <div class="space-y-4">
        <!-- Name Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <FormField
            name="first_name"
            label="Ad"
            placeholder="Müşteri adı"
            required
          />

          <FormField
            name="last_name"
            label="Soyad"
            placeholder="Müşteri soyadı"
            required
          />
        </div>

        <!-- Contact Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <FormField
            name="email"
            type="email"
            label="E-posta"
            placeholder="ornek@email.com"
          >
            <template #prefix>
              <EnvelopeIcon class="h-5 w-5 text-gray-400" />
            </template>
          </FormField>

          <FormField
            name="phone"
            type="tel"
            label="Telefon"
            placeholder="0555 123 4567"
            required
          >
            <template #prefix>
              <PhoneIcon class="h-5 w-5 text-gray-400" />
            </template>
          </FormField>
        </div>

        <!-- Address -->
        <FormField
          name="address"
          as="textarea"
          label="Adres"
          placeholder="Müşteri adresi"
          hint="Müşterinin tam adresi"
        />

        <!-- City -->
        <FormField
          name="city"
          label="Şehir"
          placeholder="İstanbul"
        />

        <!-- Notes -->
        <FormField
          name="notes"
          as="textarea"
          label="Notlar"
          placeholder="Müşteri hakkında notlar"
          hint="Müşteri tercihleri, alerjiler vb."
        />

        <!-- Error Summary -->
        <div v-if="Object.keys(errors).length > 0" class="bg-red-50 border border-red-200 rounded-md p-4">
          <div class="flex">
            <ExclamationCircleIcon class="h-5 w-5 text-red-400" />
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">
                Form hatası
              </h3>
              <div class="mt-2 text-sm text-red-700">
                <p>Lütfen formdaki hataları düzeltin ve tekrar deneyin.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3">
          <Button
            type="button"
            variant="secondary"
            @click="$emit('cancel')"
          >
            İptal
          </Button>

          <Button
            type="submit"
            variant="primary"
            :loading="isSubmitting"
            :disabled="isSubmitting"
          >
            {{ submitLabel }}
          </Button>
        </div>
      </div>
    </template>
  </FormWrapper>
</template>

<script setup lang="ts">
import { EnvelopeIcon, PhoneIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline'
import FormWrapper from '@/components/ui/FormWrapper.vue'
import FormField from '@/components/ui/FormField.vue'
import Button from '@/components/ui/Button.vue'
import { customerSchema } from '@/composables/useValidation'

interface Props {
  initialValues?: Record<string, any>
  submitLabel?: string
}

withDefaults(defineProps<Props>(), {
  initialValues: () => ({}),
  submitLabel: 'Kaydet',
})

const emit = defineEmits<{
  submit: [values: Record<string, any>]
  cancel: []
}>()

const handleSubmit = (values: Record<string, any>) => {
  emit('submit', values)
}
</script>
