<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Denetim Kayıtları</h1>
        <p class="mt-2 text-sm text-gray-600">Sistemdeki güvenlik ve veri değişikliklerini izleyin</p>
      </div>
      <button @click="exportLogs" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
        <ArrowDownTrayIcon class="h-5 w-5 mr-2" />Dışa Aktar
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-indigo-100"><ShieldCheckIcon class="h-6 w-6 text-indigo-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Kayıt</p><p class="text-2xl font-bold">{{ logs.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100"><ExclamationTriangleIcon class="h-6 w-6 text-yellow-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Uyarı</p><p class="text-2xl font-bold text-yellow-600">{{ warningCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100"><XCircleIcon class="h-6 w-6 text-red-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Kritik</p><p class="text-2xl font-bold text-red-600">{{ criticalCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><CheckCircleIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Bugün</p><p class="text-2xl font-bold text-green-600">{{ todayCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Filtreler -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
      <div class="flex flex-wrap gap-3 items-center">
        <div class="relative flex-1 max-w-xs">
          <MagnifyingGlassIcon class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
          <input v-model="search" type="text" placeholder="Ara..." class="pl-10 w-full rounded-lg border-gray-300 text-sm" />
        </div>
        <div class="flex rounded-lg border border-gray-200 overflow-hidden">
          <button v-for="t in levelFilters" :key="t.value" @click="filters.level = filters.level === t.value ? '' : t.value" :class="['px-3 py-2 text-xs font-medium', filters.level === t.value ? t.activeClass : 'bg-white text-gray-700 hover:bg-gray-50']">{{ t.label }}</button>
        </div>
        <input v-model="filters.date" type="date" class="rounded-lg border-gray-300 text-sm" />
      </div>
    </div>

    <!-- Kayıt Listesi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kullanıcı</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlem</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Seviye</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detay</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="log in filteredLogs" :key="log.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm text-gray-500">{{ formatDateTime(log.created_at) }}</td>
            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ log.user?.name || log.user_agent?.slice(0, 20) || 'Sistem' }}</td>
            <td class="px-6 py-4 text-sm text-gray-600">{{ log.event || log.action }}</td>
            <td class="px-6 py-4 text-center">
              <span :class="['px-2 py-1 text-xs rounded-full font-medium', getLevelBadge(log.level)]">{{ getLevelLabel(log.level) }}</span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ log.description || log.old_values || '-' }}</td>
          </tr>
        </tbody>
      </table>
      <div v-if="filteredLogs.length === 0" class="p-12 text-center">
        <ShieldCheckIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Denetim kaydı bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { ShieldCheckIcon, ExclamationTriangleIcon, XCircleIcon, CheckCircleIcon, MagnifyingGlassIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline'
import { useAuditLogStore } from '@/stores/auditlog'

const store = useAuditLogStore()
const search = ref('')
const filters = ref({ level: '', date: '' })
const levelFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-indigo-600 text-white' },
  { value: 'info', label: 'Bilgi', activeClass: 'bg-blue-600 text-white' },
  { value: 'warning', label: 'Uyarı', activeClass: 'bg-yellow-600 text-white' },
  { value: 'critical', label: 'Kritik', activeClass: 'bg-red-600 text-white' }
]
const logs = ref<any[]>([])

const filteredLogs = computed(() => {
  let r = logs.value
  if (search.value) r = r.filter(l => l.description?.toLowerCase().includes(search.value.toLowerCase()) || l.event?.toLowerCase().includes(search.value.toLowerCase()))
  if (filters.value.level) r = r.filter(l => l.level === filters.value.level)
  if (filters.value.date) r = r.filter(l => l.created_at?.startsWith(filters.value.date))
  return r.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
})

const warningCount = computed(() => logs.value.filter(l => l.level === 'warning').length)
const criticalCount = computed(() => logs.value.filter(l => l.level === 'critical').length)
const todayCount = computed(() => { const t = new Date().toISOString().split('T')[0]; return logs.value.filter(l => l.created_at?.startsWith(t)).length })
const formatDateTime = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(d)) : '-'
const getLevelLabel = (l: string) => ({ info: 'Bilgi', warning: 'Uyarı', critical: 'Kritik', error: 'Hata' }[l] || 'Bilgi')
const getLevelBadge = (l: string) => ({ info: 'bg-blue-100 text-blue-800', warning: 'bg-yellow-100 text-yellow-800', critical: 'bg-red-100 text-red-800', error: 'bg-red-100 text-red-800' }[l] || 'bg-gray-100 text-gray-800')

const exportLogs = () => {
  const csv = [['Tarih', 'Kullanıcı', 'İşlem', 'Seviye', 'Detay'].join(','), ...filteredLogs.value.map(l => [l.created_at, l.user?.name || '', l.event || '', getLevelLabel(l.level), l.description || ''].join(','))].join('\n')
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a'); link.href = URL.createObjectURL(blob); link.download = `denetim_kayitlari_${new Date().toISOString().split('T')[0]}.csv`; link.click()
}

const loadData = async () => { const r = await store.fetchAll({}); logs.value = r?.data || [] }
onMounted(() => { loadData() })
</script>