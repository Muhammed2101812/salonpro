<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Mobil Cihazlar</h1>
        <p class="mt-2 text-sm text-gray-600">Kayıtlı mobil cihazları ve oturumları yönetin</p>
      </div>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-slate-100"><DevicePhoneMobileIcon class="h-6 w-6 text-slate-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Cihaz</p><p class="text-2xl font-bold">{{ devices.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><SignalIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Çevrimiçi</p><p class="text-2xl font-bold text-green-600">{{ onlineCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><ComputerDesktopIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">iOS</p><p class="text-2xl font-bold text-blue-600">{{ iosCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><DeviceTabletIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Android</p><p class="text-2xl font-bold text-green-600">{{ androidCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Cihaz Listesi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cihaz</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Platform</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kullanıcı</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Son Aktiflik</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="d in devices" :key="d.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div :class="['h-10 w-10 rounded-lg flex items-center justify-center', getPlatformBg(d.platform)]">
                  <component :is="getPlatformIcon(d.platform)" :class="['h-5 w-5', getPlatformColor(d.platform)]" />
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ d.device_name || d.model || 'Bilinmeyen Cihaz' }}</div>
                  <div class="text-xs text-gray-500">{{ d.device_id?.slice(0, 12) || '-' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', getPlatformBadge(d.platform)]">{{ d.platform || 'Bilinmeyen' }}</span></td>
            <td class="px-6 py-4 text-center text-sm text-gray-900">{{ d.user?.name || '-' }}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ formatDateTime(d.last_active_at || d.updated_at) }}</td>
            <td class="px-6 py-4 text-center">
              <span :class="['inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full font-medium', isOnline(d) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">
                <span :class="['w-2 h-2 rounded-full', isOnline(d) ? 'bg-green-500' : 'bg-gray-400']"></span>
                {{ isOnline(d) ? 'Çevrimiçi' : 'Çevrimdışı' }}
              </span>
            </td>
            <td class="px-6 py-4 text-right">
              <button @click="revokeDevice(d)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg" title="Oturumu Kapat"><XMarkIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="devices.length === 0" class="p-12 text-center">
        <DevicePhoneMobileIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Kayıtlı cihaz bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import { DevicePhoneMobileIcon, SignalIcon, ComputerDesktopIcon, DeviceTabletIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useMobileDeviceStore } from '@/stores/mobiledevice'

const store = useMobileDeviceStore()
const devices = ref<any[]>([])

const onlineCount = computed(() => devices.value.filter(d => isOnline(d)).length)
const iosCount = computed(() => devices.value.filter(d => d.platform?.toLowerCase() === 'ios').length)
const androidCount = computed(() => devices.value.filter(d => d.platform?.toLowerCase() === 'android').length)
const formatDateTime = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(d)) : '-'
const isOnline = (d: any) => { if (!d.last_active_at) return false; const diff = Date.now() - new Date(d.last_active_at).getTime(); return diff < 5 * 60 * 1000 }
const getPlatformBg = (p: string) => ({ ios: 'bg-gray-100', android: 'bg-green-100' }[p?.toLowerCase()] || 'bg-slate-100')
const getPlatformColor = (p: string) => ({ ios: 'text-gray-600', android: 'text-green-600' }[p?.toLowerCase()] || 'text-slate-600')
const getPlatformBadge = (p: string) => ({ ios: 'bg-gray-100 text-gray-800', android: 'bg-green-100 text-green-800' }[p?.toLowerCase()] || 'bg-slate-100 text-slate-800')
const getPlatformIcon = (p: string) => { const icons: Record<string, any> = { ios: markRaw(ComputerDesktopIcon), android: markRaw(DeviceTabletIcon) }; return icons[p?.toLowerCase()] || markRaw(DevicePhoneMobileIcon) }

const revokeDevice = async (d: any) => { if (confirm('Bu cihazın oturumunu kapatmak istiyor musunuz?')) { await store.delete(d.id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); devices.value = r?.data || [] }
onMounted(() => { loadData() })
</script>