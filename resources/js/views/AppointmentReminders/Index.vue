<template>
  <div class="space-y-6">
    <!-- Başlık ve Aksiyon Butonları -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Randevu Hatırlatıcıları</h1>
        <p class="mt-2 text-sm text-gray-600">Müşterilere gönderilecek hatırlatma mesajlarını yönetin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportReminders"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
          Dışa Aktar
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Yeni Hatırlatıcı
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <BellIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100">
            <ClockIcon class="h-6 w-6 text-yellow-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Bekleyen</p>
            <p class="text-2xl font-bold text-yellow-600">{{ stats.pending }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <CheckCircleIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Gönderilen</p>
            <p class="text-2xl font-bold text-green-600">{{ stats.sent }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100">
            <XCircleIcon class="h-6 w-6 text-red-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Başarısız</p>
            <p class="text-2xl font-bold text-red-600">{{ stats.failed }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtreler -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center">
          <div class="relative">
            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
            <input
              v-model="search"
              type="text"
              placeholder="Ara..."
              class="pl-10 w-64 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
            />
          </div>

          <div class="flex rounded-lg border border-gray-200 overflow-hidden">
            <button
              v-for="status in statusFilters"
              :key="status.value"
              @click="filters.status = filters.status === status.value ? '' : status.value"
              :class="[
                'px-3 py-2 text-xs font-medium transition-colors',
                filters.status === status.value ? status.activeClass : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              {{ status.label }}
            </button>
          </div>

          <select v-model="filters.type" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            <option value="">Tüm Tipler</option>
            <option value="sms">SMS</option>
            <option value="email">E-posta</option>
            <option value="push">Push Bildirim</option>
          </select>
        </div>

        <button @click="loadData" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
          <ArrowPathIcon class="h-5 w-5" />
        </button>
      </div>
    </div>

    <!-- Yükleniyor -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Hatırlatıcı Listesi -->
    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Randevu</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gönderim</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tip</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="reminder in filteredReminders" :key="reminder.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <CalendarIcon class="h-4 w-4 text-blue-500" />
                <span class="text-sm font-medium text-gray-900">{{ formatDate(reminder.appointment?.appointment_date) }}</span>
              </div>
              <p class="text-xs text-gray-500">{{ reminder.appointment?.appointment_time }}</p>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <p class="text-sm font-medium text-gray-900">{{ reminder.appointment?.customer?.name || 'Bilinmiyor' }}</p>
              <p class="text-xs text-gray-500">{{ reminder.appointment?.service?.name }}</p>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <p class="text-sm text-gray-900">{{ formatDateTime(reminder.scheduled_at) }}</p>
              <p class="text-xs text-gray-500">{{ reminder.minutes_before }} dk önce</p>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span :class="['px-2 py-1 text-xs rounded-full font-medium', getTypeBadge(reminder.reminder_type)]">
                {{ getTypeLabel(reminder.reminder_type) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span :class="['px-2 py-1 text-xs rounded-full font-medium', getStatusBadge(reminder.status)]">
                {{ getStatusLabel(reminder.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
              <div class="flex items-center justify-end gap-2">
                <button
                  v-if="reminder.status === 'pending'"
                  @click="sendNow(reminder)"
                  class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                  title="Şimdi Gönder"
                >
                  <PaperAirplaneIcon class="h-4 w-4" />
                </button>
                <button
                  @click="editReminder(reminder)"
                  class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                  title="Düzenle"
                >
                  <PencilIcon class="h-4 w-4" />
                </button>
                <button
                  @click="handleDelete(reminder.id)"
                  class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  title="Sil"
                >
                  <TrashIcon class="h-4 w-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="filteredReminders.length === 0" class="p-12 text-center">
        <BellIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Hatırlatıcı bulunamadı</p>
        <button @click="openCreateModal" class="mt-4 text-blue-600 hover:text-blue-700 font-medium">
          Hatırlatıcı oluşturun
        </button>
      </div>
    </div>

    <!-- Hatırlatıcı Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">{{ isEdit ? 'Hatırlatıcı Düzenle' : 'Yeni Hatırlatıcı' }}</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Randevu *</label>
            <select v-model="form.appointment_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
              <option value="">Randevu Seçin</option>
              <option v-for="apt in appointments" :key="apt.id" :value="apt.id">
                {{ apt.customer?.name }} - {{ apt.appointment_date }} {{ apt.appointment_time }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Hatırlatma Tipi *</label>
            <div class="grid grid-cols-3 gap-2">
              <button
                v-for="type in reminderTypes"
                :key="type.value"
                type="button"
                @click="form.reminder_type = type.value"
                :class="[
                  'p-3 rounded-lg border text-center transition-colors',
                  form.reminder_type === type.value ? type.activeClass : 'border-gray-200 hover:border-blue-300'
                ]"
              >
                <component :is="type.icon" class="h-5 w-5 mx-auto mb-1" />
                <span class="text-xs font-medium">{{ type.label }}</span>
              </button>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kaç Dakika Önce *</label>
            <select v-model.number="form.minutes_before" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
              <option :value="30">30 dakika</option>
              <option :value="60">1 saat</option>
              <option :value="120">2 saat</option>
              <option :value="1440">1 gün</option>
              <option :value="2880">2 gün</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mesaj</label>
            <textarea v-model="form.message" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Özel mesaj (boş bırakılırsa varsayılan kullanılır)"></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">İptal</button>
            <button type="submit" :disabled="loading" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors">
              {{ loading ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import {
  PlusIcon,
  BellIcon,
  ClockIcon,
  CheckCircleIcon,
  XCircleIcon,
  MagnifyingGlassIcon,
  CalendarIcon,
  PaperAirplaneIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  DevicePhoneMobileIcon,
  EnvelopeIcon,
  BellAlertIcon
} from '@heroicons/vue/24/outline'
import { useAppointmentReminderStore } from '@/stores/appointmentreminder'
import { useAppointmentStore } from '@/stores/appointment'

const store = useAppointmentReminderStore()
const appointmentStore = useAppointmentStore()

const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const search = ref('')

const filters = ref({ status: '', type: '' })

const stats = ref({ total: 0, pending: 0, sent: 0, failed: 0 })

const statusFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-blue-600 text-white' },
  { value: 'pending', label: 'Bekleyen', activeClass: 'bg-yellow-600 text-white' },
  { value: 'sent', label: 'Gönderildi', activeClass: 'bg-green-600 text-white' },
  { value: 'failed', label: 'Başarısız', activeClass: 'bg-red-600 text-white' }
]

const reminderTypes = [
  { value: 'sms', label: 'SMS', icon: markRaw(DevicePhoneMobileIcon), activeClass: 'bg-blue-50 text-blue-700 border-blue-500' },
  { value: 'email', label: 'E-posta', icon: markRaw(EnvelopeIcon), activeClass: 'bg-green-50 text-green-700 border-green-500' },
  { value: 'push', label: 'Push', icon: markRaw(BellAlertIcon), activeClass: 'bg-orange-50 text-orange-700 border-orange-500' }
]

const form = ref({
  appointment_id: '', reminder_type: 'sms', minutes_before: 60, message: ''
})

const reminders = ref<any[]>([])
const appointments = computed(() => appointmentStore.appointments || [])

const filteredReminders = computed(() => {
  let result = reminders.value
  if (search.value) result = result.filter(r => r.appointment?.customer?.name?.toLowerCase().includes(search.value.toLowerCase()))
  if (filters.value.status) result = result.filter(r => r.status === filters.value.status)
  if (filters.value.type) result = result.filter(r => r.reminder_type === filters.value.type)
  return result.sort((a, b) => new Date(b.scheduled_at).getTime() - new Date(a.scheduled_at).getTime())
})

const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: 'long' }).format(new Date(d)) : '-'
const formatDateTime = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(d)) : '-'
const getTypeLabel = (type: string) => ({ sms: 'SMS', email: 'E-posta', push: 'Push' }[type] || type)
const getTypeBadge = (type: string) => ({ sms: 'bg-blue-100 text-blue-800', email: 'bg-green-100 text-green-800', push: 'bg-orange-100 text-orange-800' }[type] || 'bg-gray-100')
const getStatusLabel = (status: string) => ({ pending: 'Bekliyor', sent: 'Gönderildi', failed: 'Başarısız', cancelled: 'İptal' }[status] || status)
const getStatusBadge = (status: string) => ({ pending: 'bg-yellow-100 text-yellow-800', sent: 'bg-green-100 text-green-800', failed: 'bg-red-100 text-red-800', cancelled: 'bg-gray-100 text-gray-600' }[status] || 'bg-gray-100')

const updateStats = () => {
  stats.value.total = reminders.value.length
  stats.value.pending = reminders.value.filter(r => r.status === 'pending').length
  stats.value.sent = reminders.value.filter(r => r.status === 'sent').length
  stats.value.failed = reminders.value.filter(r => r.status === 'failed').length
}

const openCreateModal = () => { form.value = { appointment_id: '', reminder_type: 'sms', minutes_before: 60, message: '' }; isEdit.value = false; editingId.value = null; showModal.value = true }
const editReminder = (reminder: any) => { form.value = { ...reminder }; isEdit.value = true; editingId.value = reminder.id; showModal.value = true }
const closeModal = () => { showModal.value = false }

const handleSubmit = async () => {
  loading.value = true
  try {
    if (isEdit.value && editingId.value) { await store.update(editingId.value, form.value) }
    else { await store.create({ ...form.value, status: 'pending' }) }
    closeModal(); await loadData()
  } catch (e) { console.error(e); alert('Kaydedilemedi') }
  finally { loading.value = false }
}

const sendNow = async (reminder: any) => { if (!confirm('Bu hatırlatmayı şimdi göndermek istiyor musunuz?')) return; await store.update(reminder.id, { status: 'sent' }); await loadData() }
const handleDelete = async (id: string) => { if (!confirm('Bu hatırlatmayı silmek istediğinizden emin misiniz?')) return; try { await store.delete(id); await loadData() } catch (e) { console.error(e) } }

const loadData = async () => {
  loading.value = true
  try {
    const response = await store.fetchAll({})
    reminders.value = response?.data || []
    await appointmentStore.fetchAppointments()
    updateStats()
  } catch (e) { console.error(e) }
  finally { loading.value = false }
}

const exportReminders = () => {
  const csvContent = [
    ['Randevu Tarihi', 'Müşteri', 'Tip', 'Gönderim', 'Durum'].join(','),
    ...filteredReminders.value.map(r => [r.appointment?.appointment_date, r.appointment?.customer?.name || '', getTypeLabel(r.reminder_type), r.scheduled_at, getStatusLabel(r.status)].join(','))
  ].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `hatirlaticilar_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>