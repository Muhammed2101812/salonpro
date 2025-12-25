<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Analitik Oturumları</h1>
        <p class="mt-2 text-sm text-gray-600">Kullanıcı oturumlarını ve ziyaret istatistiklerini izleyin</p>
      </div>
      <button @click="exportSessions" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
        <ArrowDownTrayIcon class="h-5 w-5 mr-2" />Dışa Aktar
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-teal-100"><GlobeAltIcon class="h-6 w-6 text-teal-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Oturum</p><p class="text-2xl font-bold">{{ sessions.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><SignalIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Aktif</p><p class="text-2xl font-bold text-green-600">{{ activeCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><ClockIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Ort. Süre</p><p class="text-2xl font-bold text-blue-600">{{ avgDuration }} dk</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><DevicePhoneMobileIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Mobil</p><p class="text-2xl font-bold text-purple-600">{{ mobileCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Oturum Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kullanıcı</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cihaz</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Konum</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Süre</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Başlangıç</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="s in sessions" :key="s.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-teal-100 flex items-center justify-center"><span class="text-teal-600 font-bold">{{ getInitials(s.user?.name) }}</span></div>
                <div>
                  <div class="font-medium text-gray-900">{{ s.user?.name || 'Anonim' }}</div>
                  <div class="text-xs text-gray-500">{{ s.ip_address || '' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center">
              <div class="flex items-center justify-center gap-2">
                <component :is="getDeviceIcon(s.device_type)" class="h-4 w-4 text-gray-500" />
                <span class="text-sm text-gray-600">{{ s.browser || 'Chrome' }}</span>
              </div>
            </td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ s.country || s.city || '-' }}</td>
            <td class="px-6 py-4 text-center text-sm font-medium text-gray-900">{{ s.duration || 0 }} dk</td>
            <td class="px-6 py-4 text-center">
              <span :class="['inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full font-medium', isActive(s) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">
                <span :class="['w-2 h-2 rounded-full', isActive(s) ? 'bg-green-500' : 'bg-gray-400']"></span>
                {{ isActive(s) ? 'Aktif' : 'Sona Erdi' }}
              </span>
            </td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ formatDateTime(s.started_at || s.created_at) }}</td>
          </tr>
        </tbody>
      </table>
      <div v-if="sessions.length === 0" class="p-12 text-center">
        <GlobeAltIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Oturum bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import { GlobeAltIcon, SignalIcon, ClockIcon, DevicePhoneMobileIcon, ComputerDesktopIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline'
import { useAnalyticsSessionStore } from '@/stores/analyticssession'

const store = useAnalyticsSessionStore()
const sessions = ref<any[]>([])

const activeCount = computed(() => sessions.value.filter(s => isActive(s)).length)
const avgDuration = computed(() => { const durations = sessions.value.filter(s => s.duration); return durations.length ? Math.round(durations.reduce((sum, s) => sum + s.duration, 0) / durations.length) : 0 })
const mobileCount = computed(() => sessions.value.filter(s => s.device_type === 'mobile').length)
const formatDateTime = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(d)) : '-'
const getInitials = (name: string) => name ? name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase() : '?'
const isActive = (s: any) => { if (!s.ended_at && s.started_at) { const diff = Date.now() - new Date(s.started_at).getTime(); return diff < 30 * 60 * 1000 } return false }
const getDeviceIcon = (t: string) => t === 'mobile' ? markRaw(DevicePhoneMobileIcon) : markRaw(ComputerDesktopIcon)

const exportSessions = () => { alert('Oturumlar dışa aktarılıyor...') }
const loadData = async () => { const r = await store.fetchAll({}); sessions.value = r?.data || [] }
onMounted(() => { loadData() })
</script>