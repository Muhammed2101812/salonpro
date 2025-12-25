<template>
  <div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <div class="flex items-center gap-3 mb-6">
        <div class="p-2 rounded-lg bg-blue-100">
          <CalendarDaysIcon class="h-6 w-6 text-blue-600" />
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Randevu Kuralları</h3>
          <p class="text-sm text-gray-500">Randevu süre ve rezervasyon ayarları</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
        <Input
          :modelValue="settings.min_appointment_duration"
          @update:modelValue="updateSetting('min_appointment_duration', Number($event))"
          type="number"
          label="Minimum Randevu Süresi (Dakika) *"
          min="15"
          step="15"
          required
        />
        <Input
          :modelValue="settings.appointment_slot_duration"
          @update:modelValue="updateSetting('appointment_slot_duration', Number($event))"
          type="number"
          label="Randevu Aralığı (Dakika) *"
          min="15"
          step="15"
          required
        />
        <Input
          :modelValue="settings.max_advance_booking_days"
          @update:modelValue="updateSetting('max_advance_booking_days', Number($event))"
          type="number"
          label="Maks. Önceden Rezervasyon (Gün) *"
          min="1"
          required
        />
        <Input
          :modelValue="settings.cancellation_deadline_hours"
          @update:modelValue="updateSetting('cancellation_deadline_hours', Number($event))"
          type="number"
          label="İptal Süresi (Saat) *"
          min="1"
          required
        />
      </div>

      <div class="space-y-4">
        <label class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
          <input
            type="checkbox"
            :checked="settings.allow_online_booking"
            @change="updateSetting('allow_online_booking', ($event.target as HTMLInputElement).checked)"
            class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
          />
          <div>
            <span class="font-medium text-gray-900">Online Randevu</span>
            <p class="text-sm text-gray-500">Müşteriler online randevu alabilsin</p>
          </div>
        </label>

        <label class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
          <input
            type="checkbox"
            :checked="settings.send_appointment_reminders"
            @change="updateSetting('send_appointment_reminders', ($event.target as HTMLInputElement).checked)"
            class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
          />
          <div>
            <span class="font-medium text-gray-900">Hatırlatıcı Gönder</span>
            <p class="text-sm text-gray-500">Randevudan önce hatırlatma gönder</p>
          </div>
        </label>
      </div>

      <div v-if="settings.send_appointment_reminders" class="mt-4">
        <Input
          :modelValue="settings.reminder_hours_before"
          @update:modelValue="updateSetting('reminder_hours_before', Number($event))"
          type="number"
          label="Hatırlatma Süresi (Saat) *"
          min="1"
          required
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { CalendarDaysIcon } from '@heroicons/vue/24/outline'
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
