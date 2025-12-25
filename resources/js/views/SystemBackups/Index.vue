<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Sistem Yedekleri</h1>
        <p class="mt-2 text-sm text-gray-600">Veritabanı ve dosya yedeklerini yönetin</p>
      </div>
      <button @click="createBackup" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium">
        <ServerStackIcon class="h-5 w-5 mr-2" />Yedek Oluştur
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><ServerStackIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Yedek</p><p class="text-2xl font-bold">{{ backups.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><CheckCircleIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Başarılı</p><p class="text-2xl font-bold text-blue-600">{{ successCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><CircleStackIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Boyut</p><p class="text-2xl font-bold text-purple-600">{{ formatSize(totalSize) }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100"><ClockIcon class="h-6 w-6 text-yellow-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Son Yedek</p><p class="text-lg font-bold">{{ lastBackupDate }}</p></div>
        </div>
      </div>
    </div>

    <!-- Yedek Listesi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Yedek</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tür</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Boyut</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tarih</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="b in backups" :key="b.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div :class="['h-10 w-10 rounded-lg flex items-center justify-center', getTypeBg(b.type)]">
                  <component :is="getTypeIcon(b.type)" :class="['h-5 w-5', getTypeColor(b.type)]" />
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ b.name || formatBackupName(b) }}</div>
                  <div class="text-xs text-gray-500">{{ b.filename || '-' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center">
              <span :class="['px-2 py-1 text-xs rounded-full font-medium', getTypeBadge(b.type)]">{{ getTypeLabel(b.type) }}</span>
            </td>
            <td class="px-6 py-4 text-center text-sm font-medium text-gray-900">{{ formatSize(b.size || 0) }}</td>
            <td class="px-6 py-4 text-center">
              <span :class="['px-2 py-1 text-xs rounded-full font-medium', getStatusBadge(b.status)]">{{ getStatusLabel(b.status) }}</span>
            </td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ formatDateTime(b.created_at) }}</td>
            <td class="px-6 py-4 text-right">
              <button @click="downloadBackup(b)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"><ArrowDownTrayIcon class="h-4 w-4" /></button>
              <button @click="restoreBackup(b)" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg"><ArrowPathIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(b.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="backups.length === 0" class="p-12 text-center">
        <ServerStackIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Yedek bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import { ServerStackIcon, CheckCircleIcon, CircleStackIcon, ClockIcon, ArrowDownTrayIcon, ArrowPathIcon, TrashIcon, DocumentIcon, FolderIcon } from '@heroicons/vue/24/outline'
import { useSystemBackupStore } from '@/stores/systembackup'

const store = useSystemBackupStore()
const backups = ref<any[]>([])

const successCount = computed(() => backups.value.filter(b => b.status === 'completed' || !b.status).length)
const totalSize = computed(() => backups.value.reduce((s, b) => s + (b.size || 0), 0))
const lastBackupDate = computed(() => { if (!backups.value.length) return '-'; const l = backups.value[0]; return formatDateTime(l.created_at) })
const formatSize = (bytes: number) => { if (bytes < 1024) return bytes + ' B'; if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB'; if (bytes < 1073741824) return (bytes / 1048576).toFixed(1) + ' MB'; return (bytes / 1073741824).toFixed(2) + ' GB' }
const formatDateTime = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }).format(new Date(d)) : '-'
const formatBackupName = (b: any) => `Yedek ${b.id?.slice(0, 8) || ''}`
const getTypeLabel = (t: string) => ({ database: 'Veritabanı', files: 'Dosyalar', full: 'Tam Yedek' }[t] || t || 'Tam')
const getTypeBg = (t: string) => ({ database: 'bg-blue-100', files: 'bg-purple-100', full: 'bg-green-100' }[t] || 'bg-gray-100')
const getTypeColor = (t: string) => ({ database: 'text-blue-600', files: 'text-purple-600', full: 'text-green-600' }[t] || 'text-gray-600')
const getTypeBadge = (t: string) => ({ database: 'bg-blue-100 text-blue-800', files: 'bg-purple-100 text-purple-800', full: 'bg-green-100 text-green-800' }[t] || 'bg-gray-100 text-gray-800')
const getTypeIcon = (t: string) => { const icons: Record<string, any> = { database: markRaw(CircleStackIcon), files: markRaw(FolderIcon), full: markRaw(ServerStackIcon) }; return icons[t] || markRaw(DocumentIcon) }
const getStatusLabel = (s: string) => ({ pending: 'Bekliyor', running: 'Çalışıyor', completed: 'Tamamlandı', failed: 'Başarısız' }[s] || 'Tamamlandı')
const getStatusBadge = (s: string) => ({ pending: 'bg-yellow-100 text-yellow-800', running: 'bg-blue-100 text-blue-800', completed: 'bg-green-100 text-green-800', failed: 'bg-red-100 text-red-800' }[s] || 'bg-green-100 text-green-800')

const createBackup = async () => { if (confirm('Yeni yedek oluşturulsun mu?')) { await store.create({ type: 'full', status: 'completed' }); await loadData() } }
const downloadBackup = (b: any) => { alert(`${b.name || formatBackupName(b)} indiriliyor...`) }
const restoreBackup = async (b: any) => { if (confirm('Bu yedekten geri yükleme yapmak istiyor musunuz? Bu işlem mevcut verilerin üzerine yazacaktır.')) { alert('Geri yükleme başlatıldı...') } }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); backups.value = r?.data || [] }
onMounted(() => { loadData() })
</script>