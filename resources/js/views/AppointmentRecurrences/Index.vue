<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Tekrarlayan Randevular</h1>
        <p class="mt-2 text-sm text-gray-600">Periyodik randevu şablonlarını yönetin</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="exportRecurrences"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
          Dışa Aktar
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors"
        >
          <PlusIcon class="h-5 w-5 mr-2" />
          Yeni Şablon
        </button>
      </div>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-indigo-100">
            <ArrowPathIcon class="h-6 w-6 text-indigo-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Aktif Şablon</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.active }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <CalendarDaysIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Haftalık</p>
            <p class="text-2xl font-bold text-blue-600">{{ stats.weekly }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100">
            <CalendarIcon class="h-6 w-6 text-purple-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Aylık</p>
            <p class="text-2xl font-bold text-purple-600">{{ stats.monthly }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <UserGroupIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Müşteri</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.customers }}</p>
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
              placeholder="Müşteri ara..."
              class="pl-10 w-64 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
            />
          </div>

          <div class="flex rounded-lg border border-gray-200 overflow-hidden">
            <button
              v-for="freq in frequencyFilters"
              :key="freq.value"
              @click="filters.frequency = filters.frequency === freq.value ? '' : freq.value"
              :class="[
                'px-3 py-2 text-xs font-medium transition-colors',
                filters.frequency === freq.value ? freq.activeClass : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              {{ freq.label }}
            </button>
          </div>
        </div>

        <button @click="loadData" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
          <ArrowPathIcon class="h-5 w-5" />
        </button>
      </div>
    </div>

    <!-- Yükleniyor -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Şablon Kartları -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="recurrence in filteredRecurrences" :key="recurrence.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-1', recurrence.is_active ? 'bg-indigo-500' : 'bg-gray-300']"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-3">
            <div>
              <p class="font-semibold text-gray-900">{{ recurrence.customer?.name || 'Bilinmiyor' }}</p>
              <p class="text-sm text-gray-500">{{ recurrence.service?.name }}</p>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', recurrence.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">
              {{ recurrence.is_active ? 'Aktif' : 'Pasif' }}
            </span>
          </div>

          <!-- Tekrar Bilgisi -->
          <div class="bg-indigo-50 rounded-lg p-3 mb-4">
            <div class="flex items-center gap-2">
              <ArrowPathIcon class="h-5 w-5 text-indigo-600" />
              <span class="text-sm font-medium text-indigo-800">{{ getFrequencyLabel(recurrence.frequency) }}</span>
            </div>
            <p class="text-xs text-indigo-600 mt-1">
              {{ getDaysLabel(recurrence.days_of_week) }} • {{ recurrence.time }}
            </p>
          </div>

          <!-- Detaylar -->
          <div class="space-y-2 text-sm mb-4">
            <div class="flex justify-between">
              <span class="text-gray-500">Başlangıç</span>
              <span class="font-medium">{{ formatDate(recurrence.start_date) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-500">Bitiş</span>
              <span class="font-medium">{{ recurrence.end_date ? formatDate(recurrence.end_date) : 'Süresiz' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-500">Sonraki</span>
              <span class="font-medium text-indigo-600">{{ formatDate(recurrence.next_occurrence) }}</span>
            </div>
          </div>

          <!-- Aksiyonlar -->
          <div class="flex justify-between items-center pt-3 border-t border-gray-100">
            <button
              @click="toggleActive(recurrence)"
              :class="['text-sm font-medium', recurrence.is_active ? 'text-orange-600 hover:text-orange-700' : 'text-green-600 hover:text-green-700']"
            >
              {{ recurrence.is_active ? 'Durdur' : 'Aktifleştir' }}
            </button>
            <div class="flex gap-2">
              <button @click="editRecurrence(recurrence)" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                <PencilIcon class="h-4 w-4" />
              </button>
              <button @click="handleDelete(recurrence.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                <TrashIcon class="h-4 w-4" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="filteredRecurrences.length === 0" class="col-span-full text-center py-12">
        <ArrowPathIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Tekrarlayan randevu bulunamadı</p>
        <button @click="openCreateModal" class="mt-4 text-indigo-600 hover:text-indigo-700 font-medium">
          Şablon oluşturun
        </button>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h2 class="text-xl font-bold text-gray-900">{{ isEdit ? 'Şablonu Düzenle' : 'Yeni Tekrarlayan Randevu' }}</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri *</label>
            <select v-model="form.customer_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              <option value="">Müşteri Seçin</option>
              <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Hizmet *</label>
            <select v-model="form.service_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              <option value="">Hizmet Seçin</option>
              <option v-for="s in services" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tekrar Sıklığı *</label>
            <div class="grid grid-cols-3 gap-2">
              <button
                v-for="f in frequencies"
                :key="f.value"
                type="button"
                @click="form.frequency = f.value"
                :class="['p-3 rounded-lg border text-center transition-colors', form.frequency === f.value ? 'bg-indigo-50 text-indigo-700 border-indigo-500' : 'border-gray-200 hover:border-indigo-300']"
              >
                <span class="text-sm font-medium">{{ f.label }}</span>
              </button>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Saat *</label>
              <input v-model="form.time" type="time" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç *</label>
              <input v-model="form.start_date" type="date" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">İptal</button>
            <button type="submit" :disabled="loading" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium disabled:opacity-50 transition-colors">
              {{ loading ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import {
  PlusIcon,
  ArrowPathIcon,
  CalendarDaysIcon,
  CalendarIcon,
  UserGroupIcon,
  MagnifyingGlassIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
  ArrowDownTrayIcon
} from '@heroicons/vue/24/outline'
import { useAppointmentRecurrenceStore } from '@/stores/appointmentrecurrence'
import { useCustomerStore } from '@/stores/customer'
import { useServiceStore } from '@/stores/service'

const store = useAppointmentRecurrenceStore()
const customerStore = useCustomerStore()
const serviceStore = useServiceStore()

const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const search = ref('')

const filters = ref({ frequency: '' })

const stats = ref({ active: 0, weekly: 0, monthly: 0, customers: 0 })

const frequencyFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-indigo-600 text-white' },
  { value: 'daily', label: 'Günlük', activeClass: 'bg-green-600 text-white' },
  { value: 'weekly', label: 'Haftalık', activeClass: 'bg-blue-600 text-white' },
  { value: 'monthly', label: 'Aylık', activeClass: 'bg-purple-600 text-white' }
]

const frequencies = [
  { value: 'daily', label: 'Günlük' },
  { value: 'weekly', label: 'Haftalık' },
  { value: 'monthly', label: 'Aylık' }
]

const form = ref({
  customer_id: '', service_id: '', frequency: 'weekly', time: '10:00', start_date: new Date().toISOString().split('T')[0]
})

const recurrences = ref<any[]>([])
const customers = computed(() => customerStore.customers || [])
const services = computed(() => serviceStore.services || [])

const filteredRecurrences = computed(() => {
  let result = recurrences.value
  if (search.value) result = result.filter(r => r.customer?.name?.toLowerCase().includes(search.value.toLowerCase()))
  if (filters.value.frequency) result = result.filter(r => r.frequency === filters.value.frequency)
  return result.sort((a, b) => (b.is_active ? 1 : 0) - (a.is_active ? 1 : 0))
})

const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: 'long' }).format(new Date(d)) : '-'
const getFrequencyLabel = (f: string) => ({ daily: 'Her Gün', weekly: 'Her Hafta', monthly: 'Her Ay', biweekly: 'İki Haftada Bir' }[f] || f)
const getDaysLabel = (days: string[]) => days?.length > 0 ? days.join(', ') : 'Tüm günler'

const updateStats = () => {
  stats.value.active = recurrences.value.filter(r => r.is_active).length
  stats.value.weekly = recurrences.value.filter(r => r.frequency === 'weekly').length
  stats.value.monthly = recurrences.value.filter(r => r.frequency === 'monthly').length
  const uniqueCustomers = new Set(recurrences.value.map(r => r.customer_id))
  stats.value.customers = uniqueCustomers.size
}

const openCreateModal = () => { form.value = { customer_id: '', service_id: '', frequency: 'weekly', time: '10:00', start_date: new Date().toISOString().split('T')[0] }; isEdit.value = false; editingId.value = null; showModal.value = true }
const editRecurrence = (recurrence: any) => { form.value = { ...recurrence }; isEdit.value = true; editingId.value = recurrence.id; showModal.value = true }
const closeModal = () => { showModal.value = false }

const handleSubmit = async () => {
  loading.value = true
  try {
    if (isEdit.value && editingId.value) { await store.update(editingId.value, form.value) }
    else { await store.create({ ...form.value, is_active: true }) }
    closeModal(); await loadData()
  } catch (e) { console.error(e); alert('Kaydedilemedi') }
  finally { loading.value = false }
}

const toggleActive = async (recurrence: any) => { await store.update(recurrence.id, { is_active: !recurrence.is_active }); await loadData() }
const handleDelete = async (id: string) => { if (!confirm('Bu şablonu silmek istediğinizden emin misiniz?')) return; try { await store.delete(id); await loadData() } catch (e) { console.error(e) } }

const loadData = async () => {
  loading.value = true
  try {
    const response = await store.fetchAll({})
    recurrences.value = response?.data || []
    await Promise.all([customerStore.fetchCustomers(), serviceStore.fetchServices()])
    updateStats()
  } catch (e) { console.error(e) }
  finally { loading.value = false }
}

const exportRecurrences = () => {
  const csvContent = [
    ['Müşteri', 'Hizmet', 'Sıklık', 'Saat', 'Durum'].join(','),
    ...filteredRecurrences.value.map(r => [r.customer?.name || '', r.service?.name || '', getFrequencyLabel(r.frequency), r.time, r.is_active ? 'Aktif' : 'Pasif'].join(','))
  ].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `tekrarlayan_randevular_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>