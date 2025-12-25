<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Randevu Geçmişi</h1>
        <p class="mt-2 text-sm text-gray-600">Geçmiş randevuları ve değişiklikleri görüntüleyin</p>
      </div>
      <button
        @click="exportHistories"
        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
      >
        <ArrowDownTrayIcon class="h-5 w-5 mr-2 text-gray-500" />
        Dışa Aktar
      </button>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-slate-100">
            <ClockIcon class="h-6 w-6 text-slate-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Kayıt</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <CheckCircleIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Tamamlanan</p>
            <p class="text-2xl font-bold text-green-600">{{ stats.completed }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100">
            <XCircleIcon class="h-6 w-6 text-red-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">İptal Edilen</p>
            <p class="text-2xl font-bold text-red-600">{{ stats.cancelled }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <ArrowPathIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Değişiklik</p>
            <p class="text-2xl font-bold text-blue-600">{{ stats.modified }}</p>
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
              placeholder="Müşteri veya randevu ara..."
              class="pl-10 w-64 rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 text-sm"
            />
          </div>

          <div class="flex rounded-lg border border-gray-200 overflow-hidden">
            <button
              v-for="action in actionFilters"
              :key="action.value"
              @click="filters.action = filters.action === action.value ? '' : action.value"
              :class="[
                'px-3 py-2 text-xs font-medium transition-colors',
                filters.action === action.value ? action.activeClass : 'bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              {{ action.label }}
            </button>
          </div>

          <input
            v-model="filters.date"
            type="date"
            class="rounded-lg border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 text-sm"
          />
        </div>

        <button @click="loadData" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
          <ArrowPathIcon class="h-5 w-5" />
        </button>
      </div>
    </div>

    <!-- Yükleniyor -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-slate-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- Timeline Görünümü -->
    <div v-else class="space-y-4">
      <div v-for="history in filteredHistories" :key="history.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex">
          <!-- Timeline Bar -->
          <div class="w-1.5 flex-shrink-0" :class="getActionColor(history.action)"></div>

          <div class="flex-1 p-4">
            <div class="flex items-start justify-between">
              <div class="flex items-start gap-3">
                <div :class="['p-2 rounded-lg', getActionBg(history.action)]">
                  <component :is="getActionIcon(history.action)" :class="['h-5 w-5', getActionIconColor(history.action)]" />
                </div>
                <div>
                  <p class="font-medium text-gray-900">{{ getActionLabel(history.action) }}</p>
                  <div class="flex items-center gap-2 mt-1 text-sm text-gray-500">
                    <span>{{ history.appointment?.customer?.name || 'Bilinmiyor' }}</span>
                    <span>•</span>
                    <span>{{ history.appointment?.service?.name }}</span>
                  </div>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm text-gray-900">{{ formatDateTime(history.created_at) }}</p>
                <p class="text-xs text-gray-500">{{ history.changed_by?.name || 'Sistem' }}</p>
              </div>
            </div>

            <!-- Değişiklik Detayları -->
            <div v-if="history.changes && Object.keys(history.changes).length > 0" class="mt-3 p-3 bg-gray-50 rounded-lg">
              <p class="text-xs font-medium text-gray-700 mb-2">Değişiklikler:</p>
              <div class="space-y-1">
                <div v-for="(value, key) in history.changes" :key="key" class="flex items-center text-xs">
                  <span class="text-gray-500 w-24">{{ getFieldLabel(key) }}:</span>
                  <span class="text-red-600 line-through mr-2">{{ value.old || '-' }}</span>
                  <ArrowRightIcon class="h-3 w-3 text-gray-400 mx-1" />
                  <span class="text-green-600">{{ value.new || '-' }}</span>
                </div>
              </div>
            </div>

            <!-- Randevu Bilgileri -->
            <div class="mt-3 flex items-center gap-4 text-sm text-gray-500">
              <span class="flex items-center gap-1">
                <CalendarDaysIcon class="h-4 w-4" />
                {{ formatDate(history.appointment?.appointment_date) }}
              </span>
              <span class="flex items-center gap-1">
                <ClockIcon class="h-4 w-4" />
                {{ history.appointment?.appointment_time }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div v-if="filteredHistories.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <ClockIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Geçmiş kaydı bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import {
  ClockIcon,
  CheckCircleIcon,
  XCircleIcon,
  MagnifyingGlassIcon,
  CalendarDaysIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon,
  ArrowRightIcon,
  PlusCircleIcon,
  PencilSquareIcon,
  TrashIcon,
  ArrowUturnLeftIcon
} from '@heroicons/vue/24/outline'
import { useAppointmentHistoryStore } from '@/stores/appointmenthistory'

const store = useAppointmentHistoryStore()

const loading = ref(false)
const search = ref('')

const filters = ref({ action: '', date: '' })

const stats = ref({ total: 0, completed: 0, cancelled: 0, modified: 0 })

const actionFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-slate-600 text-white' },
  { value: 'created', label: 'Oluşturma', activeClass: 'bg-green-600 text-white' },
  { value: 'updated', label: 'Güncelleme', activeClass: 'bg-blue-600 text-white' },
  { value: 'cancelled', label: 'İptal', activeClass: 'bg-red-600 text-white' }
]

const histories = ref<any[]>([])

const filteredHistories = computed(() => {
  let result = histories.value
  if (search.value) result = result.filter(h => h.appointment?.customer?.name?.toLowerCase().includes(search.value.toLowerCase()))
  if (filters.value.action) result = result.filter(h => h.action === filters.value.action)
  if (filters.value.date) result = result.filter(h => h.created_at?.startsWith(filters.value.date))
  return result.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
})

const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: 'long' }).format(new Date(d)) : '-'
const formatDateTime = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(d)) : '-'
const getActionLabel = (action: string) => ({ created: 'Randevu Oluşturuldu', updated: 'Randevu Güncellendi', cancelled: 'Randevu İptal Edildi', completed: 'Randevu Tamamlandı', rescheduled: 'Randevu Ertelendi' }[action] || action)
const getActionColor = (action: string) => ({ created: 'bg-green-500', updated: 'bg-blue-500', cancelled: 'bg-red-500', completed: 'bg-emerald-500', rescheduled: 'bg-orange-500' }[action] || 'bg-gray-400')
const getActionBg = (action: string) => ({ created: 'bg-green-100', updated: 'bg-blue-100', cancelled: 'bg-red-100', completed: 'bg-emerald-100', rescheduled: 'bg-orange-100' }[action] || 'bg-gray-100')
const getActionIconColor = (action: string) => ({ created: 'text-green-600', updated: 'text-blue-600', cancelled: 'text-red-600', completed: 'text-emerald-600', rescheduled: 'text-orange-600' }[action] || 'text-gray-600')
const getActionIcon = (action: string) => {
  const icons: Record<string, any> = { created: markRaw(PlusCircleIcon), updated: markRaw(PencilSquareIcon), cancelled: markRaw(XCircleIcon), completed: markRaw(CheckCircleIcon), rescheduled: markRaw(ArrowUturnLeftIcon) }
  return icons[action] || markRaw(ClockIcon)
}
const getFieldLabel = (field: string) => ({ appointment_date: 'Tarih', appointment_time: 'Saat', service_id: 'Hizmet', employee_id: 'Çalışan', status: 'Durum', notes: 'Notlar' }[field] || field)

const updateStats = () => {
  stats.value.total = histories.value.length
  stats.value.completed = histories.value.filter(h => h.action === 'completed').length
  stats.value.cancelled = histories.value.filter(h => h.action === 'cancelled').length
  stats.value.modified = histories.value.filter(h => h.action === 'updated' || h.action === 'rescheduled').length
}

const loadData = async () => {
  loading.value = true
  try {
    const response = await store.fetchAll({})
    histories.value = response?.data || []
    updateStats()
  } catch (e) { console.error(e) }
  finally { loading.value = false }
}

const exportHistories = () => {
  const csvContent = [
    ['Tarih', 'Müşteri', 'Hizmet', 'İşlem', 'Yapan'].join(','),
    ...filteredHistories.value.map(h => [h.created_at, h.appointment?.customer?.name || '', h.appointment?.service?.name || '', getActionLabel(h.action), h.changed_by?.name || 'Sistem'].join(','))
  ].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `randevu_gecmisi_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>