<template>
  <div class="p-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Ayarlar</h1>
      <p class="mt-2 text-gray-600">Sistem ayarlarınızı yönetin</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-600">Yükleniyor...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded mb-6">
      {{ error }}
    </div>

    <!-- Settings Content -->
    <div v-else class="bg-white rounded-lg shadow">
      <!-- Tabs -->
      <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'px-6 py-4 text-sm font-medium border-b-2 transition',
              activeTab === tab.id
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            {{ tab.label }}
          </button>
        </nav>
      </div>

      <!-- Tab Content -->
      <div class="p-6">
        <form @submit.prevent="handleSubmit">
          <!-- Genel Ayarlar -->
          <div v-show="activeTab === 'general'" class="space-y-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Genel Bilgiler</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Salon/Şirket Adı *</label>
                <input
                  v-model="settings.business_name"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Örn: Güzellik Salonu"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Telefon *</label>
                <input
                  v-model="settings.business_phone"
                  type="tel"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="0555 123 45 67"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">E-posta *</label>
                <input
                  v-model="settings.business_email"
                  type="email"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="info@salon.com"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Web Sitesi</label>
                <input
                  v-model="settings.business_website"
                  type="url"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="https://www.salon.com"
                >
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Adres *</label>
                <textarea
                  v-model="settings.business_address"
                  rows="3"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Tam adres..."
                ></textarea>
              </div>
            </div>
          </div>

          <!-- İş Ayarları -->
          <div v-show="activeTab === 'business'" class="space-y-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Çalışma Saatleri</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Açılış Saati *</label>
                <input
                  v-model="settings.opening_time"
                  type="time"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kapanış Saati *</label>
                <input
                  v-model="settings.closing_time"
                  type="time"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Çalışma Günleri *</label>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <label v-for="day in weekDays" :key="day.value" class="flex items-center space-x-2 cursor-pointer">
                  <input
                    type="checkbox"
                    :value="day.value"
                    v-model="settings.working_days"
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                  >
                  <span class="text-sm text-gray-700">{{ day.label }}</span>
                </label>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Saat Dilimi *</label>
              <select
                v-model="settings.timezone"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="Europe/Istanbul">İstanbul (UTC+3)</option>
                <option value="Europe/Athens">Atina (UTC+2)</option>
                <option value="Europe/Berlin">Berlin (UTC+1)</option>
              </select>
            </div>
          </div>

          <!-- Randevu Ayarları -->
          <div v-show="activeTab === 'appointment'" class="space-y-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Randevu Kuralları</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Randevu Süresi (Dakika) *</label>
                <input
                  v-model.number="settings.min_appointment_duration"
                  type="number"
                  min="15"
                  step="15"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <p class="text-xs text-gray-500 mt-1">Örn: 15, 30, 45, 60</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Randevu Aralığı (Dakika) *</label>
                <input
                  v-model.number="settings.appointment_slot_duration"
                  type="number"
                  min="15"
                  step="15"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <p class="text-xs text-gray-500 mt-1">Randevular arasındaki zaman aralığı</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Maksimum Önceden Rezervasyon (Gün) *</label>
                <input
                  v-model.number="settings.max_advance_booking_days"
                  type="number"
                  min="1"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <p class="text-xs text-gray-500 mt-1">Müşteriler kaç gün öncesinden randevu alabilir</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">İptal Süresi (Saat) *</label>
                <input
                  v-model.number="settings.cancellation_deadline_hours"
                  type="number"
                  min="1"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <p class="text-xs text-gray-500 mt-1">Randevudan kaç saat öncesine kadar iptal edilebilir</p>
              </div>
            </div>

            <div>
              <label class="flex items-center space-x-2 cursor-pointer">
                <input
                  type="checkbox"
                  v-model="settings.allow_online_booking"
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                >
                <span class="text-sm font-medium text-gray-700">Online randevu almaya izin ver</span>
              </label>
            </div>

            <div>
              <label class="flex items-center space-x-2 cursor-pointer">
                <input
                  type="checkbox"
                  v-model="settings.send_appointment_reminders"
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                >
                <span class="text-sm font-medium text-gray-700">Randevu hatırlatıcıları gönder</span>
              </label>
            </div>

            <div v-if="settings.send_appointment_reminders">
              <label class="block text-sm font-medium text-gray-700 mb-2">Hatırlatma Süresi (Saat) *</label>
              <input
                v-model.number="settings.reminder_hours_before"
                type="number"
                min="1"
                :required="settings.send_appointment_reminders"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
              <p class="text-xs text-gray-500 mt-1">Randevudan kaç saat önce hatırlatma gönderilsin</p>
            </div>
          </div>

          <!-- Finansal Ayarlar -->
          <div v-show="activeTab === 'financial'" class="space-y-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Para ve Vergi Ayarları</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Para Birimi *</label>
                <select
                  v-model="settings.currency"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="TRY">Türk Lirası (₺)</option>
                  <option value="USD">Amerikan Doları ($)</option>
                  <option value="EUR">Euro (€)</option>
                  <option value="GBP">İngiliz Sterlini (£)</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Varsayılan KDV Oranı (%) *</label>
                <input
                  v-model.number="settings.default_tax_rate"
                  type="number"
                  min="0"
                  max="100"
                  step="0.01"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <p class="text-xs text-gray-500 mt-1">Türkiye için genellikle 20</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fiyat Formatı *</label>
                <select
                  v-model="settings.price_format"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="before">Sembol Önce (₺100,00)</option>
                  <option value="after">Sembol Sonra (100,00₺)</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ondalık Basamak *</label>
                <select
                  v-model.number="settings.decimal_places"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option :value="0">0 (100)</option>
                  <option :value="2">2 (100,00)</option>
                </select>
              </div>
            </div>

            <div>
              <label class="flex items-center space-x-2 cursor-pointer">
                <input
                  type="checkbox"
                  v-model="settings.enable_invoicing"
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                >
                <span class="text-sm font-medium text-gray-700">Fatura/Fiş sistemini etkinleştir</span>
              </label>
            </div>

            <div v-if="settings.enable_invoicing">
              <label class="block text-sm font-medium text-gray-700 mb-2">Fatura Numarası Öneki</label>
              <input
                v-model="settings.invoice_prefix"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Örn: INV-"
              >
              <p class="text-xs text-gray-500 mt-1">Fatura numaraları şu şekilde olur: INV-0001, INV-0002, ...</p>
            </div>
          </div>

          <!-- Bildirim Ayarları -->
          <div v-show="activeTab === 'notification'" class="space-y-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bildirim Tercihleri</h3>

            <div class="space-y-4">
              <div class="bg-gray-50 p-4 rounded-lg">
                <label class="flex items-center space-x-2 cursor-pointer mb-3">
                  <input
                    type="checkbox"
                    v-model="settings.enable_sms_notifications"
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                  >
                  <span class="text-sm font-semibold text-gray-900">SMS Bildirimleri</span>
                </label>
                <p class="text-sm text-gray-600 ml-6">Müşterilere SMS ile bildirim gönderin</p>
              </div>

              <div class="bg-gray-50 p-4 rounded-lg">
                <label class="flex items-center space-x-2 cursor-pointer mb-3">
                  <input
                    type="checkbox"
                    v-model="settings.enable_email_notifications"
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                  >
                  <span class="text-sm font-semibold text-gray-900">E-posta Bildirimleri</span>
                </label>
                <p class="text-sm text-gray-600 ml-6">Müşterilere e-posta ile bildirim gönderin</p>
              </div>

              <div class="bg-gray-50 p-4 rounded-lg">
                <label class="flex items-center space-x-2 cursor-pointer mb-3">
                  <input
                    type="checkbox"
                    v-model="settings.notify_new_appointment"
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                  >
                  <span class="text-sm font-semibold text-gray-900">Yeni Randevu Bildirimi</span>
                </label>
                <p class="text-sm text-gray-600 ml-6">Yeni randevu oluşturulduğunda bildirim al</p>
              </div>

              <div class="bg-gray-50 p-4 rounded-lg">
                <label class="flex items-center space-x-2 cursor-pointer mb-3">
                  <input
                    type="checkbox"
                    v-model="settings.notify_cancelled_appointment"
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                  >
                  <span class="text-sm font-semibold text-gray-900">İptal Edilen Randevu Bildirimi</span>
                </label>
                <p class="text-sm text-gray-600 ml-6">Randevu iptal edildiğinde bildirim al</p>
              </div>

              <div class="bg-gray-50 p-4 rounded-lg">
                <label class="flex items-center space-x-2 cursor-pointer mb-3">
                  <input
                    type="checkbox"
                    v-model="settings.notify_low_stock"
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                  >
                  <span class="text-sm font-semibold text-gray-900">Düşük Stok Bildirimi</span>
                </label>
                <p class="text-sm text-gray-600 ml-6">Ürün stoku azaldığında bildirim al</p>
              </div>
            </div>
          </div>

          <!-- Save Button -->
          <div class="flex justify-end pt-6 border-t border-gray-200 mt-8">
            <button
              type="submit"
              :disabled="saving"
              class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition font-medium"
            >
              {{ saving ? 'Kaydediliyor...' : 'Ayarları Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useSettingStore } from '@/stores/setting';

const settingStore = useSettingStore();

const loading = ref(false);
const saving = ref(false);
const error = ref<string | null>(null);
const activeTab = ref('general');

const tabs = [
  { id: 'general', label: 'Genel' },
  { id: 'business', label: 'İş Ayarları' },
  { id: 'appointment', label: 'Randevular' },
  { id: 'financial', label: 'Finansal' },
  { id: 'notification', label: 'Bildirimler' },
];

const weekDays = [
  { value: 'monday', label: 'Pazartesi' },
  { value: 'tuesday', label: 'Salı' },
  { value: 'wednesday', label: 'Çarşamba' },
  { value: 'thursday', label: 'Perşembe' },
  { value: 'friday', label: 'Cuma' },
  { value: 'saturday', label: 'Cumartesi' },
  { value: 'sunday', label: 'Pazar' },
];

const settings = ref({
  // Genel
  business_name: '',
  business_phone: '',
  business_email: '',
  business_website: '',
  business_address: '',

  // İş
  opening_time: '09:00',
  closing_time: '18:00',
  working_days: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
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
  notify_low_stock: true,
});

const loadSettings = async () => {
  loading.value = true;
  error.value = null;

  try {
    await settingStore.fetchSettings();

    // Backend'den gelen ayarları settings objesine map et
    if (settingStore.settings && settingStore.settings.length > 0) {
      settingStore.settings.forEach((setting: any) => {
        const key = setting.key as keyof typeof settings.value;

        if (key in settings.value) {
          // Type'a göre değeri dönüştür
          if (setting.type === 'boolean') {
            (settings.value as any)[key] = setting.value === 'true' || setting.value === '1' || setting.value === 1 || setting.value === true;
          } else if (setting.type === 'number') {
            (settings.value as any)[key] = Number(setting.value);
          } else if (setting.type === 'json') {
            try {
              (settings.value as any)[key] = JSON.parse(setting.value);
            } catch {
              (settings.value as any)[key] = setting.value;
            }
          } else {
            (settings.value as any)[key] = setting.value;
          }
        }
      });
    }
  } catch (err: any) {
    error.value = 'Ayarlar yüklenirken hata oluştu';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const handleSubmit = async () => {
  saving.value = true;
  error.value = null;

  try {
    // Her ayarı ayrı ayrı kaydet
    for (const [key, value] of Object.entries(settings.value)) {
      let type = 'string';
      let formattedValue = value;

      if (typeof value === 'boolean') {
        type = 'boolean';
        formattedValue = value ? 'true' : 'false';
      } else if (typeof value === 'number') {
        type = 'number';
        formattedValue = String(value);
      } else if (Array.isArray(value)) {
        type = 'json';
        formattedValue = JSON.stringify(value);
      }

      // Ayarın var olup olmadığını kontrol et
      const existingSetting = settingStore.settings.find((s: any) => s.key === key);

      const settingData = {
        key,
        value: formattedValue,
        type,
        description: ''
      };

      if (existingSetting) {
        await settingStore.updateSetting(existingSetting.id, settingData);
      } else {
        await settingStore.createSetting(settingData);
      }
    }

    alert('Ayarlar başarıyla kaydedildi!');
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Ayarlar kaydedilirken hata oluştu';
    console.error(err);
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  loadSettings();
});
</script>
