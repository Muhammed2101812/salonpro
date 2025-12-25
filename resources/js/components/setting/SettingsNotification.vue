<template>
  <div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <div class="flex items-center gap-3 mb-6">
        <div class="p-2 rounded-lg bg-purple-100">
          <BellIcon class="h-6 w-6 text-purple-600" />
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Bildirim Tercihleri</h3>
          <p class="text-sm text-gray-500">SMS ve e-posta bildirim ayarları</p>
        </div>
      </div>

      <div class="space-y-4">
        <label class="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
          <div class="flex items-center gap-3">
            <div class="p-2 rounded-lg bg-green-100">
              <ChatBubbleLeftIcon class="h-5 w-5 text-green-600" />
            </div>
            <div>
              <span class="font-medium text-gray-900">SMS Bildirimleri</span>
              <p class="text-sm text-gray-500">Müşterilere SMS gönder</p>
            </div>
          </div>
          <input
            type="checkbox"
            :checked="settings.enable_sms_notifications"
            @change="updateSetting('enable_sms_notifications', ($event.target as HTMLInputElement).checked)"
            class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
          />
        </label>

        <label class="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
          <div class="flex items-center gap-3">
            <div class="p-2 rounded-lg bg-blue-100">
              <EnvelopeIcon class="h-5 w-5 text-blue-600" />
            </div>
            <div>
              <span class="font-medium text-gray-900">E-posta Bildirimleri</span>
              <p class="text-sm text-gray-500">Müşterilere e-posta gönder</p>
            </div>
          </div>
          <input
            type="checkbox"
            :checked="settings.enable_email_notifications"
            @change="updateSetting('enable_email_notifications', ($event.target as HTMLInputElement).checked)"
            class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
          />
        </label>

        <label class="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
          <div class="flex items-center gap-3">
            <div class="p-2 rounded-lg bg-indigo-100">
              <CalendarDaysIcon class="h-5 w-5 text-indigo-600" />
            </div>
            <div>
              <span class="font-medium text-gray-900">Yeni Randevu Bildirimi</span>
              <p class="text-sm text-gray-500">Yeni randevularda bildirim al</p>
            </div>
          </div>
          <input
            type="checkbox"
            :checked="settings.notify_new_appointment"
            @change="updateSetting('notify_new_appointment', ($event.target as HTMLInputElement).checked)"
            class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
          />
        </label>

        <label class="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
          <div class="flex items-center gap-3">
            <div class="p-2 rounded-lg bg-red-100">
              <XCircleIcon class="h-5 w-5 text-red-600" />
            </div>
            <div>
              <span class="font-medium text-gray-900">İptal Bildirimi</span>
              <p class="text-sm text-gray-500">İptal edilen randevularda bildirim al</p>
            </div>
          </div>
          <input
            type="checkbox"
            :checked="settings.notify_cancelled_appointment"
            @change="updateSetting('notify_cancelled_appointment', ($event.target as HTMLInputElement).checked)"
            class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
          />
        </label>

        <label class="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
          <div class="flex items-center gap-3">
            <div class="p-2 rounded-lg bg-yellow-100">
              <ExclamationTriangleIcon class="h-5 w-5 text-yellow-600" />
            </div>
            <div>
              <span class="font-medium text-gray-900">Düşük Stok Bildirimi</span>
              <p class="text-sm text-gray-500">Stok azaldığında bildirim al</p>
            </div>
          </div>
          <input
            type="checkbox"
            :checked="settings.notify_low_stock"
            @change="updateSetting('notify_low_stock', ($event.target as HTMLInputElement).checked)"
            class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
          />
        </label>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {
  BellIcon,
  ChatBubbleLeftIcon,
  EnvelopeIcon,
  CalendarDaysIcon,
  XCircleIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'

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
