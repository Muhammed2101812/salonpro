<template>
  <div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <div class="flex items-center gap-3 mb-6">
        <div class="p-2 rounded-lg bg-indigo-100">
          <BuildingStorefrontIcon class="h-6 w-6 text-indigo-600" />
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-900">İşletme Bilgileri</h3>
          <p class="text-sm text-gray-500">Salon/şirket temel bilgileri</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <Input
          :modelValue="settings.business_name"
          @update:modelValue="updateSetting('business_name', $event)"
          label="Salon/Şirket Adı *"
          required
          placeholder="Örn: Güzellik Salonu"
        />
        <Input
          :modelValue="settings.business_phone"
          @update:modelValue="updateSetting('business_phone', $event)"
          type="tel"
          label="Telefon *"
          required
          placeholder="0555 123 45 67"
        />
        <Input
          :modelValue="settings.business_email"
          @update:modelValue="updateSetting('business_email', $event)"
          type="email"
          label="E-posta *"
          required
          placeholder="info@salon.com"
        />
        <Input
          :modelValue="settings.business_website"
          @update:modelValue="updateSetting('business_website', $event)"
          type="url"
          label="Web Sitesi"
          placeholder="https://www.salon.com"
        />
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Adres *</label>
          <textarea
            :value="settings.business_address"
            @input="updateSetting('business_address', ($event.target as HTMLTextAreaElement).value)"
            rows="2"
            required
            placeholder="Tam adres..."
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          ></textarea>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { BuildingStorefrontIcon } from '@heroicons/vue/24/outline'
import Input from '@/components/ui/Input.vue'

const props = defineProps<{
  settings: any
}>()

const emit = defineEmits<{
  'update:settings': [key: string, value: any]
}>()

const updateSetting = (key: string, value: any) => {
  emit('update:settings', key, value)
}
</script>
