<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Randevu Çakışmaları</h1>
        <p class="mt-2 text-sm text-gray-600">Çakışan randevuları tespit edin ve çözün</p>
      </div>
      <button
        @click="loadData"
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 transition-colors"
      >
        <ArrowPathIcon class="h-5 w-5 mr-2" />
        Yeniden Tara
      </button>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-orange-100">
            <ExclamationTriangleIcon class="h-6 w-6 text-orange-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Çakışma</p>
            <p class="text-2xl font-bold text-orange-600">{{ stats.total }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100">
            <ClockIcon class="h-6 w-6 text-red-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Zaman Çakışması</p>
            <p class="text-2xl font-bold text-red-600">{{ stats.time }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100">
            <UserIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Personel Çakışması</p>
            <p class="text-2xl font-bold text-blue-600">{{ stats.employee }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100">
            <CheckCircleIcon class="h-6 w-6 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Çözülen</p>
            <p class="text-2xl font-bold text-green-600">{{ stats.resolved }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtreler -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center">
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

          <select v-model="filters.type" class="rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm">
            <option value="">Tüm Tipler</option>
            <option value="time">Zaman</option>
            <option value="employee">Personel</option>
            <option value="resource">Kaynak</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Yükleniyor -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-600 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Çakışmalar taranıyor...</p>
    </div>

    <!-- Çakışma Listesi -->
    <div v-else class="space-y-4">
      <div v-for="conflict in filteredConflicts" :key="conflict.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div :class="['h-1', conflict.status === 'resolved' ? 'bg-green-500' : 'bg-orange-500']"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div :class="['p-2 rounded-lg', getTypeBg(conflict.conflict_type)]">
                <component :is="getTypeIcon(conflict.conflict_type)" :class="['h-5 w-5', getTypeColor(conflict.conflict_type)]" />
              </div>
              <div>
                <p class="font-semibold text-gray-900">{{ getTypeLabel(conflict.conflict_type) }}</p>
                <p class="text-sm text-gray-500">{{ formatDateTime(conflict.detected_at) }}</p>
              </div>
            </div>
            <span :class="['px-3 py-1 text-xs rounded-full font-medium', conflict.status === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800']">
              {{ conflict.status === 'resolved' ? 'Çözüldü' : 'Aktif' }}
            </span>
          </div>

          <!-- Çakışan Randevular -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="p-3 bg-red-50 rounded-lg border border-red-200">
              <p class="text-xs text-red-600 font-medium mb-1">Randevu 1</p>
              <p class="font-medium text-gray-900">{{ conflict.appointment1?.customer?.name || 'Bilinmiyor' }}</p>
              <p class="text-sm text-gray-600">{{ conflict.appointment1?.service?.name }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ formatDate(conflict.appointment1?.appointment_date) }} • {{ conflict.appointment1?.appointment_time }}</p>
            </div>
            <div class="p-3 bg-orange-50 rounded-lg border border-orange-200">
              <p class="text-xs text-orange-600 font-medium mb-1">Randevu 2</p>
              <p class="font-medium text-gray-900">{{ conflict.appointment2?.customer?.name || 'Bilinmiyor' }}</p>
              <p class="text-sm text-gray-600">{{ conflict.appointment2?.service?.name }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ formatDate(conflict.appointment2?.appointment_date) }} • {{ conflict.appointment2?.appointment_time }}</p>
            </div>
          </div>

          <!-- Aksiyonlar -->
          <div v-if="conflict.status !== 'resolved'" class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button
              @click="reschedule(conflict, 1)"
              class="px-3 py-1.5 text-sm font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
            >
              Randevu 1'i Ertele
            </button>
            <button
              @click="reschedule(conflict, 2)"
              class="px-3 py-1.5 text-sm font-medium text-orange-600 hover:bg-orange-50 rounded-lg transition-colors"
            >
              Randevu 2'yi Ertele
            </button>
            <button
              @click="resolveConflict(conflict)"
              class="px-3 py-1.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors"
            >
              Çözüldü İşaretle
            </button>
          </div>
        </div>
      </div>

      <div v-if="filteredConflicts.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <CheckCircleIcon class="h-12 w-12 text-green-400 mx-auto mb-4" />
        <p class="text-gray-500">Çakışma bulunamadı!</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import {
  ExclamationTriangleIcon,
  ClockIcon,
  UserIcon,
  CheckCircleIcon,
  ArrowPathIcon,
  CubeIcon
} from '@heroicons/vue/24/outline'
import { useAppointmentConflictStore } from '@/stores/appointmentconflict'

const store = useAppointmentConflictStore()

const loading = ref(false)

const filters = ref({ status: '', type: '' })

const stats = ref({ total: 0, time: 0, employee: 0, resolved: 0 })

const statusFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-orange-600 text-white' },
  { value: 'active', label: 'Aktif', activeClass: 'bg-red-600 text-white' },
  { value: 'resolved', label: 'Çözüldü', activeClass: 'bg-green-600 text-white' }
]

const conflicts = ref<any[]>([])

const filteredConflicts = computed(() => {
  let result = conflicts.value
  if (filters.value.status) result = result.filter(c => c.status === filters.value.status)
  if (filters.value.type) result = result.filter(c => c.conflict_type === filters.value.type)
  return result.sort((a, b) => (a.status === 'resolved' ? 1 : 0) - (b.status === 'resolved' ? 1 : 0))
})

const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: 'long' }).format(new Date(d)) : '-'
const formatDateTime = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(d)) : '-'
const getTypeLabel = (type: string) => ({ time: 'Zaman Çakışması', employee: 'Personel Çakışması', resource: 'Kaynak Çakışması' }[type] || type)
const getTypeBg = (type: string) => ({ time: 'bg-red-100', employee: 'bg-blue-100', resource: 'bg-purple-100' }[type] || 'bg-gray-100')
const getTypeColor = (type: string) => ({ time: 'text-red-600', employee: 'text-blue-600', resource: 'text-purple-600' }[type] || 'text-gray-600')
const getTypeIcon = (type: string) => {
  const icons: Record<string, any> = { time: markRaw(ClockIcon), employee: markRaw(UserIcon), resource: markRaw(CubeIcon) }
  return icons[type] || markRaw(ExclamationTriangleIcon)
}

const updateStats = () => {
  stats.value.total = conflicts.value.length
  stats.value.time = conflicts.value.filter(c => c.conflict_type === 'time').length
  stats.value.employee = conflicts.value.filter(c => c.conflict_type === 'employee').length
  stats.value.resolved = conflicts.value.filter(c => c.status === 'resolved').length
}

const reschedule = (conflict: any, appointmentNum: number) => { alert(`Randevu ${appointmentNum} erteleme sayfasına yönlendiriliyor...`) }
const resolveConflict = async (conflict: any) => { if (!confirm('Bu çakışmayı çözüldü olarak işaretlemek istiyor musunuz?')) return; await store.update(conflict.id, { status: 'resolved' }); await loadData() }

const loadData = async () => {
  loading.value = true
  try {
    const response = await store.fetchAll({})
    conflicts.value = response?.data || []
    updateStats()
  } catch (e) { console.error(e) }
  finally { loading.value = false }
}

onMounted(() => { loadData() })
</script>