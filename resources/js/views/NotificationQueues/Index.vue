<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Bildirim Kuyruğu</h1>
        <p class="mt-2 text-sm text-gray-600">Bekleyen ve işlenen bildirimleri izleyin</p>
      </div>
      <button @click="processQueue" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
        <PlayIcon class="h-5 w-5 mr-2" />Kuyruğu İşle
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-indigo-100"><QueueListIcon class="h-6 w-6 text-indigo-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam</p><p class="text-2xl font-bold">{{ queues.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100"><ClockIcon class="h-6 w-6 text-yellow-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Bekliyor</p><p class="text-2xl font-bold text-yellow-600">{{ pendingCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><ArrowPathIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">İşleniyor</p><p class="text-2xl font-bold text-blue-600">{{ processingCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100"><ExclamationCircleIcon class="h-6 w-6 text-red-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Başarısız</p><p class="text-2xl font-bold text-red-600">{{ failedCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Kuyruk Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-4 border-b border-gray-100 flex gap-4">
        <select v-model="statusFilter" class="rounded-lg border-gray-300">
          <option value="">Tüm Durumlar</option>
          <option value="pending">Bekliyor</option>
          <option value="processing">İşleniyor</option>
          <option value="completed">Tamamlandı</option>
          <option value="failed">Başarısız</option>
        </select>
      </div>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bildirim</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kanal</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Öncelik</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Deneme</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="q in filteredQueues" :key="q.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="font-medium text-gray-900">{{ q.subject || q.title || 'Bildirim' }}</div>
              <div class="text-xs text-gray-500">{{ q.recipient || '' }}</div>
            </td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', getChannelBadge(q.channel)]">{{ getChannelLabel(q.channel) }}</span></td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', getPriorityBadge(q.priority)]">{{ getPriorityLabel(q.priority) }}</span></td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', getStatusBadge(q.status)]">{{ getStatusLabel(q.status) }}</span></td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ q.attempts || 0 }}/3</td>
            <td class="px-6 py-4 text-right">
              <button v-if="q.status === 'failed'" @click="retryQueue(q)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"><ArrowPathIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(q.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="filteredQueues.length === 0" class="p-12 text-center">
        <QueueListIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Kuyrukta bildirim yok</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { QueueListIcon, ClockIcon, ArrowPathIcon, ExclamationCircleIcon, PlayIcon, TrashIcon } from '@heroicons/vue/24/outline'
import { useNotificationQueueStore } from '@/stores/notificationqueue'

const store = useNotificationQueueStore()
const statusFilter = ref('')
const queues = ref<any[]>([])

const pendingCount = computed(() => queues.value.filter(q => q.status === 'pending').length)
const processingCount = computed(() => queues.value.filter(q => q.status === 'processing').length)
const failedCount = computed(() => queues.value.filter(q => q.status === 'failed').length)
const filteredQueues = computed(() => queues.value.filter(q => !statusFilter.value || q.status === statusFilter.value))
const getChannelLabel = (c: string) => ({ email: 'E-posta', sms: 'SMS', push: 'Push' }[c] || c)
const getChannelBadge = (c: string) => ({ email: 'bg-blue-100 text-blue-800', sms: 'bg-green-100 text-green-800', push: 'bg-purple-100 text-purple-800' }[c] || 'bg-gray-100 text-gray-800')
const getPriorityLabel = (p: string) => ({ high: 'Yüksek', normal: 'Normal', low: 'Düşük' }[p] || p || 'Normal')
const getPriorityBadge = (p: string) => ({ high: 'bg-red-100 text-red-800', normal: 'bg-gray-100 text-gray-800', low: 'bg-blue-100 text-blue-800' }[p] || 'bg-gray-100 text-gray-800')
const getStatusLabel = (s: string) => ({ pending: 'Bekliyor', processing: 'İşleniyor', completed: 'Tamamlandı', failed: 'Başarısız' }[s] || s)
const getStatusBadge = (s: string) => ({ pending: 'bg-yellow-100 text-yellow-800', processing: 'bg-blue-100 text-blue-800', completed: 'bg-green-100 text-green-800', failed: 'bg-red-100 text-red-800' }[s] || 'bg-gray-100 text-gray-800')

const processQueue = () => { alert('Kuyruk işleniyor...') }
const retryQueue = (q: any) => { alert(`${q.subject || 'Bildirim'} yeniden deneniyor...`) }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); queues.value = r?.data || [] }
onMounted(() => { loadData() })
</script>