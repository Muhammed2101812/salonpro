<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Performans Metrikleri</h1>
        <p class="mt-2 text-sm text-gray-600">Sistem ve uygulama performansını izleyin</p>
      </div>
      <button @click="refreshMetrics" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
        <ArrowPathIcon class="h-5 w-5 mr-2" />Yenile
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><CpuChipIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">CPU</p><p class="text-2xl font-bold text-blue-600">{{ cpuUsage }}%</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><ServerIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Bellek</p><p class="text-2xl font-bold text-purple-600">{{ memoryUsage }}%</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><ClockIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Yanıt Süresi</p><p class="text-2xl font-bold text-green-600">{{ avgResponseTime }} ms</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-orange-100"><BoltIcon class="h-6 w-6 text-orange-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">İstek/dk</p><p class="text-2xl font-bold text-orange-600">{{ requestsPerMinute }}</p></div>
        </div>
      </div>
    </div>

    <!-- Metrik Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-4 border-b border-gray-100 flex gap-4">
        <select v-model="categoryFilter" class="rounded-lg border-gray-300">
          <option value="">Tüm Kategoriler</option>
          <option value="system">Sistem</option>
          <option value="database">Veritabanı</option>
          <option value="cache">Önbellek</option>
          <option value="api">API</option>
        </select>
      </div>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metrik</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Değer</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Min</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Max</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Son Güncelleme</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="m in filteredMetrics" :key="m.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div :class="['h-10 w-10 rounded-lg flex items-center justify-center', getCategoryBg(m.category)]">
                  <component :is="getCategoryIcon(m.category)" class="h-5 w-5 text-white" />
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ m.name }}</div>
                  <span class="text-xs text-gray-500">{{ getCategoryLabel(m.category) }}</span>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center text-lg font-bold text-gray-900">{{ m.value }}{{ m.unit || '' }}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ m.min || '-' }}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ m.max || '-' }}</td>
            <td class="px-6 py-4 text-center">
              <span :class="['px-2 py-1 text-xs rounded-full font-medium', getStatusBadge(m.status)]">{{ getStatusLabel(m.status) }}</span>
            </td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ formatDateTime(m.updated_at) }}</td>
          </tr>
        </tbody>
      </table>
      <div v-if="filteredMetrics.length === 0" class="p-12 text-center">
        <ChartBarIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Metrik bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import { CpuChipIcon, ServerIcon, ClockIcon, BoltIcon, ArrowPathIcon, CircleStackIcon, ChartBarIcon } from '@heroicons/vue/24/outline'
import { usePerformanceMetricStore } from '@/stores/performancemetric'

const store = usePerformanceMetricStore()
const categoryFilter = ref('')
const metrics = ref<any[]>([])

const cpuUsage = computed(() => { const cpu = metrics.value.find(m => m.name?.toLowerCase().includes('cpu')); return cpu?.value || 45 })
const memoryUsage = computed(() => { const mem = metrics.value.find(m => m.name?.toLowerCase().includes('memory') || m.name?.toLowerCase().includes('bellek')); return mem?.value || 62 })
const avgResponseTime = computed(() => { const resp = metrics.value.find(m => m.name?.toLowerCase().includes('response') || m.name?.toLowerCase().includes('yanıt')); return resp?.value || 120 })
const requestsPerMinute = computed(() => { const req = metrics.value.find(m => m.name?.toLowerCase().includes('request') || m.name?.toLowerCase().includes('istek')); return req?.value || 85 })
const filteredMetrics = computed(() => metrics.value.filter(m => !categoryFilter.value || m.category === categoryFilter.value))
const formatDateTime = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).format(new Date(d)) : '-'
const getCategoryLabel = (c: string) => ({ system: 'Sistem', database: 'Veritabanı', cache: 'Önbellek', api: 'API' }[c] || c || 'Genel')
const getCategoryBg = (c: string) => ({ system: 'bg-blue-500', database: 'bg-purple-500', cache: 'bg-green-500', api: 'bg-orange-500' }[c] || 'bg-gray-500')
const getCategoryIcon = (c: string) => { const icons: Record<string, any> = { system: markRaw(CpuChipIcon), database: markRaw(CircleStackIcon), cache: markRaw(ServerIcon), api: markRaw(BoltIcon) }; return icons[c] || markRaw(ChartBarIcon) }
const getStatusLabel = (s: string) => ({ healthy: 'Sağlıklı', warning: 'Uyarı', critical: 'Kritik' }[s] || s || 'Sağlıklı')
const getStatusBadge = (s: string) => ({ healthy: 'bg-green-100 text-green-800', warning: 'bg-yellow-100 text-yellow-800', critical: 'bg-red-100 text-red-800' }[s] || 'bg-green-100 text-green-800')

const refreshMetrics = () => { loadData() }
const loadData = async () => { const r = await store.fetchAll({}); metrics.value = r?.data || [] }
onMounted(() => { loadData() })
</script>