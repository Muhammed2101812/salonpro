<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Ayarlar</h1>
        <p class="mt-2 text-sm text-gray-600">Sistem ayarlarınızı ve tercihlerinizi yönetin</p>
      </div>
      <div class="flex gap-3">
        <Button variant="outline" @click="resetSettings" :icon="ArrowPathIcon" label="Varsayılana Dön" />
      </div>
    </div>

    <!-- Yükleniyor -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Ana İçerik -->
    <div v-else class="flex flex-col lg:flex-row gap-6">
      <!-- Sol Menü -->
      <div class="lg:w-64 flex-shrink-0">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 sticky top-6">
          <nav class="p-2 space-y-1">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                'w-full flex items-center gap-3 px-4 py-3 rounded-lg text-left transition-colors',
                activeTab === tab.id
                  ? 'bg-indigo-50 text-indigo-700'
                  : 'text-gray-600 hover:bg-gray-50'
              ]"
            >
              <component :is="tab.icon" class="h-5 w-5" />
              <span class="font-medium">{{ tab.label }}</span>
            </button>
          </nav>
        </div>
      </div>

      <!-- Sağ İçerik -->
      <div class="flex-1">
        <form @submit.prevent="handleSubmit">
           <component
              :is="activeComponent"
              :settings="settings"
              @update:settings="handleUpdateSetting"
           />

          <!-- Kaydet Butonu -->
          <div class="flex justify-end pt-6">
            <Button
                type="submit"
                :loading="saving"
                variant="primary"
                :label="saving ? 'Kaydediliyor...' : 'Ayarları Kaydet'"
                class="bg-indigo-600 hover:bg-indigo-700 text-white"
            >
                <template #icon v-if="!saving">
                    <CheckCircleIcon class="h-5 w-5" />
                </template>
            </Button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import {
  BuildingStorefrontIcon,
  ClockIcon,
  CalendarDaysIcon,
  CurrencyDollarIcon,
  BellIcon,
  ArrowPathIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline'
import Button from '@/components/ui/Button.vue'

import SettingsGeneral from '@/components/setting/SettingsGeneral.vue'
import SettingsBusiness from '@/components/setting/SettingsBusiness.vue'
import SettingsAppointment from '@/components/setting/SettingsAppointment.vue'
import SettingsFinancial from '@/components/setting/SettingsFinancial.vue'
import SettingsNotification from '@/components/setting/SettingsNotification.vue'

import { useSettingStore } from '@/stores/setting'

const settingStore = useSettingStore()

const loading = ref(false)
const saving = ref(false)
const activeTab = ref('general')

const tabs = [
  { id: 'general', label: 'Genel', icon: markRaw(BuildingStorefrontIcon), component: markRaw(SettingsGeneral) },
  { id: 'business', label: 'İş Ayarları', icon: markRaw(ClockIcon), component: markRaw(SettingsBusiness) },
  { id: 'appointment', label: 'Randevular', icon: markRaw(CalendarDaysIcon), component: markRaw(SettingsAppointment) },
  { id: 'financial', label: 'Finansal', icon: markRaw(CurrencyDollarIcon), component: markRaw(SettingsFinancial) },
  { id: 'notification', label: 'Bildirimler', icon: markRaw(BellIcon), component: markRaw(SettingsNotification) }
]

const activeComponent = computed(() => {
    return tabs.find(t => t.id === activeTab.value)?.component || SettingsGeneral
})

const settings = ref({
  // Genel
  business_name: '',
  business_phone: '',
  business_email: '',
  business_website: '',
  business_address: '',
  // İş
  opening_time: '09:00',
  closing_time: '21:00',
  working_days: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as string[],
  timezone: 'Europe/Istanbul',
  // Randevu
  min_appointment_duration: 30,
  appointment_slot_duration: 15,
  max_advance_booking_days: 30,
  cancellation_deadline_hours: 24,
  allow_online_booking: true,
  send_appointment_reminders: true,
  reminder_hours_before: 24,
  // Finansal
  currency: 'TRY',
  default_tax_rate: 20,
  price_format: 'after',
  decimal_places: 2,
  enable_invoicing: true,
  invoice_prefix: 'INV-',
  // Bildirim
  enable_sms_notifications: false,
  enable_email_notifications: true,
  notify_new_appointment: true,
  notify_cancelled_appointment: true,
  notify_low_stock: true
})

const handleUpdateSetting = (key: string, value: any) => {
    // @ts-ignore
    settings.value[key] = value
}

const resetSettings = () => {
  if (!confirm('Tüm ayarları varsayılan değerlere döndürmek istediğinizden emin misiniz?')) return
  
  settings.value = {
    business_name: '',
    business_phone: '',
    business_email: '',
    business_website: '',
    business_address: '',
    opening_time: '09:00',
    closing_time: '21:00',
    working_days: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
    timezone: 'Europe/Istanbul',
    min_appointment_duration: 30,
    appointment_slot_duration: 15,
    max_advance_booking_days: 30,
    cancellation_deadline_hours: 24,
    allow_online_booking: true,
    send_appointment_reminders: true,
    reminder_hours_before: 24,
    currency: 'TRY',
    default_tax_rate: 20,
    price_format: 'after',
    decimal_places: 2,
    enable_invoicing: true,
    invoice_prefix: 'INV-',
    enable_sms_notifications: false,
    enable_email_notifications: true,
    notify_new_appointment: true,
    notify_cancelled_appointment: true,
    notify_low_stock: true
  }
}

const loadSettings = async () => {
  loading.value = true
  try {
    await settingStore.fetchSettings()
    
    if (settingStore.settings?.length > 0) {
      settingStore.settings.forEach((setting: any) => {
        const key = setting.key as keyof typeof settings.value
        if (key in settings.value) {
          if (setting.type === 'boolean') {
            (settings.value as any)[key] = setting.value === 'true' || setting.value === true
          } else if (setting.type === 'number') {
            (settings.value as any)[key] = Number(setting.value)
          } else if (setting.type === 'json') {
            try {
              (settings.value as any)[key] = JSON.parse(setting.value)
            } catch {
              (settings.value as any)[key] = setting.value
            }
          } else {
            (settings.value as any)[key] = setting.value
          }
        }
      })
    }
  } catch (error) {
    console.error('Ayarlar yüklenirken hata:', error)
  } finally {
    loading.value = false
  }
}

const handleSubmit = async () => {
  saving.value = true
  try {
    for (const [key, value] of Object.entries(settings.value)) {
      let type = 'string'
      let formattedValue = value

      if (typeof value === 'boolean') {
        type = 'boolean'
        formattedValue = value ? 'true' : 'false'
      } else if (typeof value === 'number') {
        type = 'number'
        formattedValue = String(value)
      } else if (Array.isArray(value)) {
        type = 'json'
        formattedValue = JSON.stringify(value)
      }

      const existingSetting = settingStore.settings.find((s: any) => s.key === key)
      const settingData = { key, value: formattedValue, type, description: '' }

      if (existingSetting) {
        await settingStore.updateSetting(existingSetting.id, settingData)
      } else {
        await settingStore.createSetting(settingData)
      }
    }
    alert('Ayarlar başarıyla kaydedildi!')
  } catch (error) {
    console.error('Ayarlar kaydedilemedi:', error)
    alert('Ayarlar kaydedilemedi')
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadSettings()
})
</script>
