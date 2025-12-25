<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Randevu İptalleri</h1>
        <p class="mt-2 text-sm text-gray-600">İptal edilen randevuları ve nedenlerini görüntüleyin</p>
      </div>
      <button
        @click="exportCancellations"
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
          <div class="p-3 rounded-full bg-red-100">
            <XCircleIcon class="h-6 w-6 text-red-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam İptal</p>
            <p class="text-2xl font-bold text-red-600">{{ stats.total }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-orange-100">
            <UserIcon class="h-6 w-6 text-orange-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Müşteri Kaynaklı</p>
            <p class="text-2xl font-bold text-orange-600">{{ stats.byCustomer }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <BuildingStorefrontIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Salon Kaynaklı</p>
            <p class="text-2xl font-bold text-blue-600">{{ stats.bySalon }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <CurrencyDollarIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Kayıp</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.totalLoss) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- İptal Nedenleri Özeti -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
      <h3 class="text-sm font-medium text-gray-700 mb-4">İptal Nedenleri Dağılımı</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div v-for="reason in reasonStats" :key="reason.key" class="p-3 bg-gray-50 rounded-lg text-center">
          <p class="text-2xl font-bold text-gray-900">{{ reason.count }}</p>
          <p class="text-xs text-gray-500">{{ reason.label }}</p>
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
              class="pl-10 w-64 rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
            />
          </div>

          <select v-model="filters.reason" class="rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm">
            <option value="">Tüm Nedenler</option>
            <option v-for="r in reasons" :key="r.value" :value="r.value">{{ r.label }}</option>
          </select>

          <input
            v-model="filters.date"
            type="date"
            class="rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
          />
        </div>

        <button @click="loadData" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
          <ArrowPathIcon class="h-5 w-5" />
        </button>
      </div>
    </div>

    <!-- Yükleniyor -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Yükleniyor...</p>
    </div>

    <!-- İptal Listesi -->
    <div v-else class="space-y-3">
      <div v-for="cancellation in filteredCancellations" :key="cancellation.id" class="bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-red-500 p-4">
        <div class="flex items-start justify-between">
          <div class="flex items-start gap-4">
            <div class="p-2 rounded-lg bg-red-100">
              <XCircleIcon class="h-6 w-6 text-red-600" />
            </div>
            <div>
              <p class="font-medium text-gray-900">{{ cancellation.appointment?.customer?.name || 'Bilinmiyor' }}</p>
              <p class="text-sm text-gray-500">{{ cancellation.appointment?.service?.name }}</p>
              <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                <span class="flex items-center gap-1">
                  <CalendarDaysIcon class="h-3.5 w-3.5" />
                  {{ formatDate(cancellation.appointment?.appointment_date) }}
                </span>
                <span class="flex items-center gap-1">
                  <ClockIcon class="h-3.5 w-3.5" />
                  {{ cancellation.appointment?.appointment_time }}
                </span>
              </div>
            </div>
          </div>
          <div class="text-right">
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', getReasonBadge(cancellation.reason)]">
              {{ getReasonLabel(cancellation.reason) }}
            </span>
            <p class="text-xs text-gray-400 mt-2">{{ formatDateTime(cancellation.cancelled_at) }}</p>
          </div>
        </div>

        <div v-if="cancellation.notes" class="mt-3 p-3 bg-gray-50 rounded-lg">
          <p class="text-sm text-gray-600">{{ cancellation.notes }}</p>
        </div>

        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
          <span class="text-sm text-gray-500">
            İptal Eden: <span class="font-medium">{{ getCancelledByLabel(cancellation.cancelled_by_type) }}</span>
          </span>
          <span v-if="cancellation.refund_amount" class="text-sm font-medium text-green-600">
            İade: {{ formatCurrency(cancellation.refund_amount) }}
          </span>
        </div>
      </div>

      <div v-if="filteredCancellations.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <XCircleIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">İptal kaydı bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import {
  XCircleIcon,
  UserIcon,
  BuildingStorefrontIcon,
  CurrencyDollarIcon,
  MagnifyingGlassIcon,
  CalendarDaysIcon,
  ClockIcon,
  ArrowPathIcon,
  ArrowDownTrayIcon
} from '@heroicons/vue/24/outline'
import { useAppointmentCancellationStore } from '@/stores/appointmentcancellation'

const store = useAppointmentCancellationStore()

const loading = ref(false)
const search = ref('')

const filters = ref({ reason: '', date: '' })

const stats = ref({ total: 0, byCustomer: 0, bySalon: 0, totalLoss: 0 })

const reasons = [
  { value: 'schedule_conflict', label: 'Program Çakışması' },
  { value: 'personal', label: 'Kişisel Nedenler' },
  { value: 'illness', label: 'Hastalık' },
  { value: 'no_show', label: 'Gelmedi' },
  { value: 'weather', label: 'Hava Durumu' },
  { value: 'other', label: 'Diğer' }
]

const cancellations = ref<any[]>([])

const filteredCancellations = computed(() => {
  let result = cancellations.value
  if (search.value) result = result.filter(c => c.appointment?.customer?.name?.toLowerCase().includes(search.value.toLowerCase()))
  if (filters.value.reason) result = result.filter(c => c.reason === filters.value.reason)
  if (filters.value.date) result = result.filter(c => c.cancelled_at?.startsWith(filters.value.date))
  return result.sort((a, b) => new Date(b.cancelled_at).getTime() - new Date(a.cancelled_at).getTime())
})

const reasonStats = computed(() => {
  return reasons.map(r => ({
    key: r.value,
    label: r.label,
    count: cancellations.value.filter(c => c.reason === r.value).length
  })).filter(r => r.count > 0)
})

const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: 'long' }).format(new Date(d)) : '-'
const formatDateTime = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(d)) : '-'
const formatCurrency = (amount: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
const getReasonLabel = (reason: string) => reasons.find(r => r.value === reason)?.label || reason
const getReasonBadge = (reason: string) => ({ schedule_conflict: 'bg-orange-100 text-orange-800', personal: 'bg-blue-100 text-blue-800', illness: 'bg-purple-100 text-purple-800', no_show: 'bg-red-100 text-red-800', weather: 'bg-cyan-100 text-cyan-800', other: 'bg-gray-100 text-gray-800' }[reason] || 'bg-gray-100')
const getCancelledByLabel = (type: string) => ({ customer: 'Müşteri', salon: 'Salon', system: 'Sistem' }[type] || type)

const updateStats = () => {
  stats.value.total = cancellations.value.length
  stats.value.byCustomer = cancellations.value.filter(c => c.cancelled_by_type === 'customer').length
  stats.value.bySalon = cancellations.value.filter(c => c.cancelled_by_type === 'salon').length
  stats.value.totalLoss = cancellations.value.reduce((sum, c) => sum + (parseFloat(c.appointment?.total || c.estimated_loss) || 0), 0)
}

const loadData = async () => {
  loading.value = true
  try {
    const response = await store.fetchAll({})
    cancellations.value = response?.data || []
    updateStats()
  } catch (e) { console.error(e) }
  finally { loading.value = false }
}

const exportCancellations = () => {
  const csvContent = [
    ['Müşteri', 'Hizmet', 'Tarih', 'Neden', 'İptal Eden'].join(','),
    ...filteredCancellations.value.map(c => [c.appointment?.customer?.name || '', c.appointment?.service?.name || '', c.appointment?.appointment_date, getReasonLabel(c.reason), getCancelledByLabel(c.cancelled_by_type)].join(','))
  ].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `randevu_iptalleri_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

onMounted(() => { loadData() })
</script>