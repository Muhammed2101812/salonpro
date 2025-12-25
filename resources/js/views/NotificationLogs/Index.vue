<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Bildirim Kayıtları</h1>
        <p class="mt-2 text-sm text-gray-600">Gönderilen bildirimlerin geçmişini görüntüleyin</p>
      </div>
      <button @click="exportLogs" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
        <ArrowDownTrayIcon class="h-5 w-5 mr-2" />Dışa Aktar
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-indigo-100"><BellIcon class="h-6 w-6 text-indigo-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam</p><p class="text-2xl font-bold">{{ logs.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><CheckCircleIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Başarılı</p><p class="text-2xl font-bold text-green-600">{{ successCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100"><XCircleIcon class="h-6 w-6 text-red-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Başarısız</p><p class="text-2xl font-bold text-red-600">{{ failedCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><EnvelopeIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">E-posta</p><p class="text-2xl font-bold text-blue-600">{{ emailCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Log Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-4 border-b border-gray-100 flex gap-4">
        <select v-model="channelFilter" class="rounded-lg border-gray-300">
          <option value="">Tüm Kanallar</option>
          <option value="email">E-posta</option>
          <option value="sms">SMS</option>
          <option value="push">Push</option>
        </select>
        <select v-model="statusFilter" class="rounded-lg border-gray-300">
          <option value="">Tüm Durumlar</option>
          <option value="sent">Gönderildi</option>
          <option value="failed">Başarısız</option>
          <option value="pending">Bekliyor</option>
        </select>
      </div>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bildirim</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kanal</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Alıcı</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tarih</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="l in filteredLogs" :key="l.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div :class="['h-10 w-10 rounded-lg flex items-center justify-center', getChannelBg(l.channel)]">
                  <component :is="getChannelIcon(l.channel)" class="h-5 w-5 text-white" />
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ l.subject || l.title || 'Bildirim' }}</div>
                  <div class="text-xs text-gray-500 line-clamp-1">{{ l.message?.slice(0, 50) || '' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', getChannelBadge(l.channel)]">{{ getChannelLabel(l.channel) }}</span></td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ l.recipient || l.to || '-' }}</td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', getStatusBadge(l.status)]">{{ getStatusLabel(l.status) }}</span></td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ formatDateTime(l.sent_at || l.created_at) }}</td>
          </tr>
        </tbody>
      </table>
      <div v-if="filteredLogs.length === 0" class="p-12 text-center">
        <BellIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Kayıt bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import { BellIcon, CheckCircleIcon, XCircleIcon, EnvelopeIcon, DevicePhoneMobileIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline'
import { useNotificationLogStore } from '@/stores/notificationlog'

const store = useNotificationLogStore()
const channelFilter = ref('')
const statusFilter = ref('')
const logs = ref<any[]>([])

const successCount = computed(() => logs.value.filter(l => l.status === 'sent' || l.status === 'delivered').length)
const failedCount = computed(() => logs.value.filter(l => l.status === 'failed').length)
const emailCount = computed(() => logs.value.filter(l => l.channel === 'email').length)
const filteredLogs = computed(() => logs.value.filter(l => (!channelFilter.value || l.channel === channelFilter.value) && (!statusFilter.value || l.status === statusFilter.value)))
const formatDateTime = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(d)) : '-'
const getChannelLabel = (c: string) => ({ email: 'E-posta', sms: 'SMS', push: 'Push' }[c] || c)
const getChannelBg = (c: string) => ({ email: 'bg-blue-500', sms: 'bg-green-500', push: 'bg-purple-500' }[c] || 'bg-gray-500')
const getChannelBadge = (c: string) => ({ email: 'bg-blue-100 text-blue-800', sms: 'bg-green-100 text-green-800', push: 'bg-purple-100 text-purple-800' }[c] || 'bg-gray-100 text-gray-800')
const getChannelIcon = (c: string) => { const icons: Record<string, any> = { email: markRaw(EnvelopeIcon), sms: markRaw(DevicePhoneMobileIcon), push: markRaw(BellIcon) }; return icons[c] || markRaw(BellIcon) }
const getStatusLabel = (s: string) => ({ sent: 'Gönderildi', delivered: 'Teslim Edildi', failed: 'Başarısız', pending: 'Bekliyor' }[s] || s)
const getStatusBadge = (s: string) => ({ sent: 'bg-green-100 text-green-800', delivered: 'bg-green-100 text-green-800', failed: 'bg-red-100 text-red-800', pending: 'bg-yellow-100 text-yellow-800' }[s] || 'bg-gray-100 text-gray-800')

const exportLogs = () => { alert('Kayıtlar dışa aktarılıyor...') }
const loadData = async () => { const r = await store.fetchAll({}); logs.value = r?.data || [] }
onMounted(() => { loadData() })
</script>