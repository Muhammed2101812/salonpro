<template>
  <div class="max-w-4xl mx-auto py-6 px-4">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Şube Ayarları</h1>
      <p class="mt-1 text-sm text-gray-600">
        {{ currentBranch?.name }} şubesinin ayarlarını yönetin
      </p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Settings Tabs -->
    <div v-else>
      <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            :class="[
              activeTab === tab.id
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
            ]"
            @click="activeTab = tab.id"
          >
            {{ tab.name }}
          </button>
        </nav>
      </div>

      <!-- Tab Content -->
      <div class="mt-6">
        <!-- Business Info -->
        <div v-show="activeTab === 'business'" class="space-y-6">
          <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">İşletme Bilgileri</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormField
                v-model="settings.business.manager_name"
                name="manager_name"
                label="Müdür Adı"
                placeholder="Ali Yılmaz"
              />
              <FormField
                v-model="settings.business.tax_office"
                name="tax_office"
                label="Vergi Dairesi"
                placeholder="Kadıköy"
              />
              <FormField
                v-model="settings.business.tax_number"
                name="tax_number"
                label="Vergi Numarası"
                placeholder="1234567890"
              />
              <FormField
                v-model="settings.business.trade_registry_number"
                name="trade_registry_number"
                label="Ticaret Sicil No"
              />
            </div>
          </div>
        </div>

        <!-- Working Hours -->
        <div v-show="activeTab === 'hours'" class="space-y-6">
          <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Çalışma Saatleri</h3>
            <div class="space-y-4">
              <div
                v-for="day in weekDays"
                :key="day.id"
                class="flex items-center gap-4"
              >
                <div class="w-32">
                  <label class="flex items-center">
                    <input
                      v-model="settings.hours[day.id].enabled"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <span class="ml-2 text-sm font-medium text-gray-700">
                      {{ day.name }}
                    </span>
                  </label>
                </div>
                <div v-if="settings.hours[day.id].enabled" class="flex-1 grid grid-cols-2 gap-4">
                  <FormField
                    v-model="settings.hours[day.id].open"
                    :name="`${day.id}_open`"
                    type="time"
                    label="Açılış"
                  />
                  <FormField
                    v-model="settings.hours[day.id].close"
                    :name="`${day.id}_close`"
                    type="time"
                    label="Kapanış"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Appointments -->
        <div v-show="activeTab === 'appointments'" class="space-y-6">
          <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Randevu Ayarları</h3>
            <div class="space-y-4">
              <FormField
                v-model.number="settings.appointments.slot_duration"
                name="slot_duration"
                type="number"
                label="Randevu Aralığı (dakika)"
                hint="Her randevu arasındaki süre"
              />
              <FormField
                v-model.number="settings.appointments.min_advance_booking"
                name="min_advance_booking"
                type="number"
                label="Minimum Önceden Rezervasyon (saat)"
                hint="Randevu en az kaç saat önceden alınmalı"
              />
              <FormField
                v-model.number="settings.appointments.max_advance_booking"
                name="max_advance_booking"
                type="number"
                label="Maximum Önceden Rezervasyon (gün)"
                hint="En fazla kaç gün sonrası için randevu alınabilir"
              />
              <FormField
                v-model.number="settings.appointments.cancellation_hours"
                name="cancellation_hours"
                type="number"
                label="İptal Süresi (saat)"
                hint="Randevudan kaç saat önce iptal edilebilir"
              />
              <div>
                <label class="flex items-center">
                  <input
                    v-model="settings.appointments.allow_overbooking"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <span class="ml-2 text-sm font-medium text-gray-700">
                    Aşırı rezervasyona izin ver
                  </span>
                </label>
              </div>
              <div>
                <label class="flex items-center">
                  <input
                    v-model="settings.appointments.send_reminders"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <span class="ml-2 text-sm font-medium text-gray-700">
                    Randevu hatırlatıcıları gönder
                  </span>
                </label>
              </div>
              <FormField
                v-if="settings.appointments.send_reminders"
                v-model.number="settings.appointments.reminder_hours"
                name="reminder_hours"
                type="number"
                label="Hatırlatıcı Süresi (saat)"
                hint="Randevudan kaç saat önce hatırlatıcı gönderilsin"
              />
            </div>
          </div>
        </div>

        <!-- Notifications -->
        <div v-show="activeTab === 'notifications'" class="space-y-6">
          <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Bildirim Ayarları</h3>
            <div class="space-y-4">
              <div>
                <label class="flex items-center">
                  <input
                    v-model="settings.notifications.sms_enabled"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <span class="ml-2 text-sm font-medium text-gray-700">
                    SMS bildirimleri gönder
                  </span>
                </label>
              </div>
              <div>
                <label class="flex items-center">
                  <input
                    v-model="settings.notifications.email_enabled"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <span class="ml-2 text-sm font-medium text-gray-700">
                    E-posta bildirimleri gönder
                  </span>
                </label>
              </div>
              <div>
                <label class="flex items-center">
                  <input
                    v-model="settings.notifications.notify_on_booking"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <span class="ml-2 text-sm font-medium text-gray-700">
                    Yeni randevu bildirimi gönder
                  </span>
                </label>
              </div>
              <div>
                <label class="flex items-center">
                  <input
                    v-model="settings.notifications.notify_on_cancellation"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <span class="ml-2 text-sm font-medium text-gray-700">
                    Randevu iptali bildirimi gönder
                  </span>
                </label>
              </div>
              <div>
                <label class="flex items-center">
                  <input
                    v-model="settings.notifications.notify_low_stock"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <span class="ml-2 text-sm font-medium text-gray-700">
                    Düşük stok bildirimi gönder
                  </span>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Financial -->
        <div v-show="activeTab === 'financial'" class="space-y-6">
          <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Finansal Ayarlar</h3>
            <div class="space-y-4">
              <FormSelect
                v-model="settings.financial.currency"
                name="currency"
                label="Para Birimi"
                :options="currencies"
                option-value="code"
                option-label="name"
              />
              <FormField
                v-model.number="settings.financial.tax_rate"
                name="tax_rate"
                type="number"
                step="0.01"
                label="KDV Oranı (%)"
                hint="Varsayılan KDV oranı"
              />
              <div>
                <label class="flex items-center">
                  <input
                    v-model="settings.financial.require_payment_on_booking"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <span class="ml-2 text-sm font-medium text-gray-700">
                    Randevu sırasında ödeme iste
                  </span>
                </label>
              </div>
              <FormField
                v-if="settings.financial.require_payment_on_booking"
                v-model.number="settings.financial.deposit_percentage"
                name="deposit_percentage"
                type="number"
                step="0.01"
                label="Depozito Oranı (%)"
                hint="Randevu sırasında alınacak depozito yüzdesi"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Save Button -->
      <div class="mt-6 flex justify-end">
        <Button
          type="button"
          variant="primary"
          :loading="saving"
          @click="saveSettings"
        >
          Ayarları Kaydet
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useBranchStore } from '@/stores/branch'
import FormField from '@/components/ui/FormField.vue'
import FormSelect from '@/components/ui/FormSelect.vue'
import Button from '@/components/ui/Button.vue'
import api from '@/services/api'

const branchStore = useBranchStore()

const activeTab = ref('business')
const loading = ref(false)
const saving = ref(false)

const tabs = [
  { id: 'business', name: 'İşletme' },
  { id: 'hours', name: 'Çalışma Saatleri' },
  { id: 'appointments', name: 'Randevular' },
  { id: 'notifications', name: 'Bildirimler' },
  { id: 'financial', name: 'Finansal' },
]

const weekDays = [
  { id: 'monday', name: 'Pazartesi' },
  { id: 'tuesday', name: 'Salı' },
  { id: 'wednesday', name: 'Çarşamba' },
  { id: 'thursday', name: 'Perşembe' },
  { id: 'friday', name: 'Cuma' },
  { id: 'saturday', name: 'Cumartesi' },
  { id: 'sunday', name: 'Pazar' },
]

const currencies = [
  { code: 'TRY', name: 'Türk Lirası (₺)' },
  { code: 'USD', name: 'Amerikan Doları ($)' },
  { code: 'EUR', name: 'Euro (€)' },
]

const settings = ref({
  business: {
    manager_name: '',
    tax_office: '',
    tax_number: '',
    trade_registry_number: '',
  },
  hours: {
    monday: { enabled: true, open: '09:00', close: '18:00' },
    tuesday: { enabled: true, open: '09:00', close: '18:00' },
    wednesday: { enabled: true, open: '09:00', close: '18:00' },
    thursday: { enabled: true, open: '09:00', close: '18:00' },
    friday: { enabled: true, open: '09:00', close: '18:00' },
    saturday: { enabled: true, open: '10:00', close: '16:00' },
    sunday: { enabled: false, open: '10:00', close: '16:00' },
  },
  appointments: {
    slot_duration: 30,
    min_advance_booking: 2,
    max_advance_booking: 30,
    cancellation_hours: 24,
    allow_overbooking: false,
    send_reminders: true,
    reminder_hours: 24,
  },
  notifications: {
    sms_enabled: true,
    email_enabled: true,
    notify_on_booking: true,
    notify_on_cancellation: true,
    notify_low_stock: true,
  },
  financial: {
    currency: 'TRY',
    tax_rate: 20,
    require_payment_on_booking: false,
    deposit_percentage: 20,
  },
})

const currentBranch = computed(() => branchStore.currentBranch)

const loadSettings = async () => {
  if (!currentBranch.value) return

  loading.value = true
  try {
    const response = await api.get<any>(`/branches/${currentBranch.value.id}/settings`)

    // Merge response data with default settings to ensure structure
    if (response) {
      // We do a careful merge to avoid overwriting defaults with empty values if that's not intended,
      // but usually the backend returns what is saved.
      // Since response might not have all keys if they were never saved, we use a merge strategy.
      // However, for simplicity and since we want to respect backend data:

      // Basic merge
      Object.keys(response).forEach(group => {
        if (settings.value[group as keyof typeof settings.value]) {
          Object.assign(settings.value[group as keyof typeof settings.value], response[group])
        }
      })
    }
  } catch (error) {
    console.error('Failed to load settings:', error)
  } finally {
    loading.value = false
  }
}

const saveSettings = async () => {
  if (!currentBranch.value) return

  saving.value = true
  try {
    await api.put(`/branches/${currentBranch.value.id}/settings`, settings.value)
    alert('Ayarlar başarıyla kaydedildi!')
  } catch (error) {
    console.error('Failed to save settings:', error)
    alert('Ayarlar kaydedilirken bir hata oluştu')
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadSettings()
})
</script>
