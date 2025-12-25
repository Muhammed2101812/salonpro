<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">KPI Değerleri</h1>
        <p class="mt-2 text-sm text-gray-600">Performans göstergelerinin değerlerini takip edin</p>
      </div>
      <div class="flex gap-2">
        <select v-model="periodFilter" class="rounded-lg border-gray-300">
          <option value="">Tüm Dönemler</option>
          <option value="daily">Günlük</option>
          <option value="weekly">Haftalık</option>
          <option value="monthly">Aylık</option>
        </select>
        <button @click="exportData" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
          <ArrowDownTrayIcon class="h-5 w-5 mr-2" />Dışa Aktar
        </button>
      </div>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-emerald-100"><ChartBarIcon class="h-6 w-6 text-emerald-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Kayıt</p><p class="text-2xl font-bold">{{ values.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><ArrowTrendingUpIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Hedefe Ulaşan</p><p class="text-2xl font-bold text-green-600">{{ onTargetCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100"><ArrowTrendingDownIcon class="h-6 w-6 text-red-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Hedef Altı</p><p class="text-2xl font-bold text-red-600">{{ belowTargetCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><CalendarIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Son Güncelleme</p><p class="text-lg font-bold text-blue-600">{{ lastUpdateDate }}</p></div>
        </div>
      </div>
    </div>

    <!-- Değer Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">KPI</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Dönem</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Değer</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Hedef</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tarih</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="v in filteredValues" :key="v.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-emerald-100 flex items-center justify-center"><ChartBarIcon class="h-5 w-5 text-emerald-600" /></div>
                <div>
                  <div class="font-medium text-gray-900">{{ v.kpi?.name || 'KPI' }}</div>
                  <code class="text-xs text-gray-500">{{ v.kpi?.key || '' }}</code>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center"><span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">{{ getPeriodLabel(v.period) }}</span></td>
            <td class="px-6 py-4 text-center text-lg font-bold text-gray-900">{{ formatValue(v.value, v.kpi?.unit) }}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ formatValue(v.target || v.kpi?.target, v.kpi?.unit) }}</td>
            <td class="px-6 py-4 text-center">
              <span :class="['inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full font-medium', getStatusBadge(v)]">
                <component :is="getStatusIcon(v)" class="h-3 w-3" />
                {{ getStatusLabel(v) }}
              </span>
            </td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ formatDate(v.recorded_at || v.created_at) }}</td>
          </tr>
        </tbody>
      </table>
      <div v-if="filteredValues.length === 0" class="p-12 text-center">
        <ChartBarIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Değer bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import { ChartBarIcon, ArrowTrendingUpIcon, ArrowTrendingDownIcon, CalendarIcon, ArrowDownTrayIcon, CheckIcon, XMarkIcon as XIcon } from '@heroicons/vue/24/outline'
import { useKpiValueStore } from '@/stores/kpivalue'

const store = useKpiValueStore()
const periodFilter = ref('')
const values = ref<any[]>([])

const onTargetCount = computed(() => values.value.filter(v => isOnTarget(v)).length)
const belowTargetCount = computed(() => values.value.filter(v => !isOnTarget(v) && v.target).length)
const lastUpdateDate = computed(() => { const dates = values.value.map(v => new Date(v.recorded_at || v.created_at)); return dates.length ? formatDate(Math.max(...dates.map(d => d.getTime())).toString()) : '-' })
const filteredValues = computed(() => values.value.filter(v => !periodFilter.value || v.period === periodFilter.value))
const formatDate = (d: string | number) => d ? new Intl.DateTimeFormat('tr-TR').format(new Date(d)) : '-'
const formatValue = (v: any, unit: string) => { if (v === undefined || v === null) return '-'; if (unit === '₺') return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(v); if (unit === '%') return v + '%'; return v.toLocaleString() + (unit ? ' ' + unit : '') }
const getPeriodLabel = (p: string) => ({ daily: 'Günlük', weekly: 'Haftalık', monthly: 'Aylık', yearly: 'Yıllık' }[p] || p || 'Genel')
const isOnTarget = (v: any) => { const target = v.target || v.kpi?.target; if (!target) return true; return v.value >= target }
const getStatusLabel = (v: any) => isOnTarget(v) ? 'Hedefte' : 'Altında'
const getStatusBadge = (v: any) => isOnTarget(v) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
const getStatusIcon = (v: any) => isOnTarget(v) ? markRaw(CheckIcon) : markRaw(XIcon)

const exportData = () => { alert('Veriler dışa aktarılıyor...') }
const loadData = async () => { const r = await store.fetchAll({}); values.value = r?.data || [] }
onMounted(() => { loadData() })
</script>