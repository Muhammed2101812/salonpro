<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Aktivite Kayıtları</h1>
        <p class="mt-2 text-sm text-gray-600">Sistemdeki tüm kullanıcı aktivitelerini görüntüleyin</p>
      </div>
      <button @click="exportLogs" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
        <ArrowDownTrayIcon class="h-5 w-5 mr-2" />Dışa Aktar
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-slate-100"><ClipboardDocumentListIcon class="h-6 w-6 text-slate-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Kayıt</p><p class="text-2xl font-bold">{{ logs.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><UserIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Aktif Kullanıcı</p><p class="text-2xl font-bold text-blue-600">{{ uniqueUsers }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><PlusCircleIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Oluşturma</p><p class="text-2xl font-bold text-green-600">{{ createCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100"><TrashIcon class="h-6 w-6 text-red-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Silme</p><p class="text-2xl font-bold text-red-600">{{ deleteCount }}</p></div>
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
          <button v-for="t in actionFilters" :key="t.value" @click="filters.action = filters.action === t.value ? '' : t.value" :class="['px-3 py-2 text-xs font-medium', filters.action === t.value ? t.activeClass : 'bg-white text-gray-700 hover:bg-gray-50']">{{ t.label }}</button>
        </div>
        <input v-model="filters.date" type="date" class="rounded-lg border-gray-300 text-sm" />
      </div>
    </div>

    <!-- Timeline -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 divide-y divide-gray-100">
      <div v-for="log in filteredLogs" :key="log.id" class="p-4 hover:bg-gray-50 transition-colors">
        <div class="flex items-start gap-4">
          <div :class="['p-2 rounded-lg', getActionBg(log.action)]">
            <component :is="getActionIcon(log.action)" :class="['h-5 w-5', getActionColor(log.action)]" />
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
              <span class="font-medium text-gray-900">{{ log.user?.name || 'Sistem' }}</span>
              <span :class="['px-2 py-0.5 text-xs rounded-full font-medium', getActionBadge(log.action)]">{{ getActionLabel(log.action) }}</span>
            </div>
            <p class="text-sm text-gray-600">{{ log.description || getDefaultDescription(log) }}</p>
            <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
              <span>{{ log.model_type }}</span>
              <span>{{ formatDateTime(log.created_at) }}</span>
              <span v-if="log.ip_address">IP: {{ log.ip_address }}</span>
            </div>
          </div>
        </div>
      </div>
      <div v-if="filteredLogs.length === 0" class="p-12 text-center">
        <ClipboardDocumentListIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Aktivite kaydı bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import { ClipboardDocumentListIcon, UserIcon, PlusCircleIcon, TrashIcon, MagnifyingGlassIcon, ArrowDownTrayIcon, PencilIcon, EyeIcon } from '@heroicons/vue/24/outline'
import { useActivityLogStore } from '@/stores/activitylog'

const store = useActivityLogStore()
const search = ref('')
const filters = ref({ action: '', date: '' })
const actionFilters = [
  { value: '', label: 'Tümü', activeClass: 'bg-slate-600 text-white' },
  { value: 'create', label: 'Oluşturma', activeClass: 'bg-green-600 text-white' },
  { value: 'update', label: 'Güncelleme', activeClass: 'bg-blue-600 text-white' },
  { value: 'delete', label: 'Silme', activeClass: 'bg-red-600 text-white' }
]
const logs = ref<any[]>([])

const filteredLogs = computed(() => {
  let r = logs.value
  if (search.value) r = r.filter(l => l.description?.toLowerCase().includes(search.value.toLowerCase()) || l.user?.name?.toLowerCase().includes(search.value.toLowerCase()))
  if (filters.value.action) r = r.filter(l => l.action === filters.value.action)
  if (filters.value.date) r = r.filter(l => l.created_at?.startsWith(filters.value.date))
  return r.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
})

const uniqueUsers = computed(() => new Set(logs.value.map(l => l.user_id)).size)
const createCount = computed(() => logs.value.filter(l => l.action === 'create').length)
const deleteCount = computed(() => logs.value.filter(l => l.action === 'delete').length)
const formatDateTime = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(d)) : '-'
const getActionLabel = (a: string) => ({ create: 'Oluşturma', update: 'Güncelleme', delete: 'Silme', view: 'Görüntüleme' }[a] || a)
const getActionBadge = (a: string) => ({ create: 'bg-green-100 text-green-800', update: 'bg-blue-100 text-blue-800', delete: 'bg-red-100 text-red-800', view: 'bg-gray-100 text-gray-800' }[a] || 'bg-gray-100')
const getActionBg = (a: string) => ({ create: 'bg-green-100', update: 'bg-blue-100', delete: 'bg-red-100', view: 'bg-gray-100' }[a] || 'bg-gray-100')
const getActionColor = (a: string) => ({ create: 'text-green-600', update: 'text-blue-600', delete: 'text-red-600', view: 'text-gray-600' }[a] || 'text-gray-600')
const getActionIcon = (a: string) => { const icons: Record<string, any> = { create: markRaw(PlusCircleIcon), update: markRaw(PencilIcon), delete: markRaw(TrashIcon), view: markRaw(EyeIcon) }; return icons[a] || markRaw(ClipboardDocumentListIcon) }
const getDefaultDescription = (l: any) => `${l.model_type} kaydı ${getActionLabel(l.action).toLowerCase()} işlemi yapıldı`

const exportLogs = () => {
  const csv = [['Kullanıcı', 'İşlem', 'Model', 'Tarih'].join(','), ...filteredLogs.value.map(l => [l.user?.name || '', getActionLabel(l.action), l.model_type, l.created_at].join(','))].join('\n')
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a'); link.href = URL.createObjectURL(blob); link.download = `aktivite_kayitlari_${new Date().toISOString().split('T')[0]}.csv`; link.click()
}

const loadData = async () => { const r = await store.fetchAll({}); logs.value = r?.data || [] }
onMounted(() => { loadData() })
</script>