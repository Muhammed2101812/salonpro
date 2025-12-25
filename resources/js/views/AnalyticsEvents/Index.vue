<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Analitik Olayları</h1>
        <p class="mt-2 text-sm text-gray-600">Kullanıcı etkileşimlerini ve sistem olaylarını izleyin</p>
      </div>
      <button @click="exportEvents" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
        <ArrowDownTrayIcon class="h-5 w-5 mr-2" />Dışa Aktar
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-cyan-100"><ChartBarIcon class="h-6 w-6 text-cyan-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Olay</p><p class="text-2xl font-bold">{{ events.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><CursorArrowRaysIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Sayfa Görüntüleme</p><p class="text-2xl font-bold text-blue-600">{{ pageViewCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><HandRaisedIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Tıklama</p><p class="text-2xl font-bold text-green-600">{{ clickCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><UsersIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Kullanıcı</p><p class="text-2xl font-bold text-purple-600">{{ uniqueUsers }}</p></div>
        </div>
      </div>
    </div>

    <!-- Olay Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-4 border-b border-gray-100 flex gap-4">
        <input v-model="search" type="text" placeholder="Olay ara..." class="flex-1 rounded-lg border-gray-300" />
        <select v-model="typeFilter" class="rounded-lg border-gray-300">
          <option value="">Tüm Türler</option>
          <option value="page_view">Sayfa Görüntüleme</option>
          <option value="click">Tıklama</option>
          <option value="form_submit">Form Gönderim</option>
          <option value="error">Hata</option>
        </select>
      </div>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Olay</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tür</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kullanıcı</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tarih</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="e in filteredEvents" :key="e.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div :class="['h-10 w-10 rounded-lg flex items-center justify-center', getTypeBg(e.type)]">
                  <component :is="getTypeIcon(e.type)" :class="['h-5 w-5', getTypeColor(e.type)]" />
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ e.name || e.event_name || 'Olay' }}</div>
                  <div class="text-xs text-gray-500">{{ e.page || e.url || '' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', getTypeBadge(e.type)]">{{ getTypeLabel(e.type) }}</span></td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ e.user?.name || 'Anonim' }}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ formatDateTime(e.created_at) }}</td>
          </tr>
        </tbody>
      </table>
      <div v-if="filteredEvents.length === 0" class="p-12 text-center">
        <ChartBarIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Olay bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import { ChartBarIcon, CursorArrowRaysIcon, HandRaisedIcon, UsersIcon, ArrowDownTrayIcon, EyeIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline'
import { useAnalyticsEventStore } from '@/stores/analyticsevent'

const store = useAnalyticsEventStore()
const search = ref('')
const typeFilter = ref('')
const events = ref<any[]>([])

const pageViewCount = computed(() => events.value.filter(e => e.type === 'page_view').length)
const clickCount = computed(() => events.value.filter(e => e.type === 'click').length)
const uniqueUsers = computed(() => new Set(events.value.filter(e => e.user_id).map(e => e.user_id)).size)
const filteredEvents = computed(() => events.value.filter(e => (!typeFilter.value || e.type === typeFilter.value) && (!search.value || e.name?.includes(search.value) || e.event_name?.includes(search.value))))
const formatDateTime = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(d)) : '-'
const getTypeLabel = (t: string) => ({ page_view: 'Görüntüleme', click: 'Tıklama', form_submit: 'Form', error: 'Hata' }[t] || t || 'Olay')
const getTypeBg = (t: string) => ({ page_view: 'bg-blue-100', click: 'bg-green-100', form_submit: 'bg-purple-100', error: 'bg-red-100' }[t] || 'bg-gray-100')
const getTypeColor = (t: string) => ({ page_view: 'text-blue-600', click: 'text-green-600', form_submit: 'text-purple-600', error: 'text-red-600' }[t] || 'text-gray-600')
const getTypeBadge = (t: string) => ({ page_view: 'bg-blue-100 text-blue-800', click: 'bg-green-100 text-green-800', form_submit: 'bg-purple-100 text-purple-800', error: 'bg-red-100 text-red-800' }[t] || 'bg-gray-100 text-gray-800')
const getTypeIcon = (t: string) => { const icons: Record<string, any> = { page_view: markRaw(EyeIcon), click: markRaw(CursorArrowRaysIcon), error: markRaw(ExclamationTriangleIcon) }; return icons[t] || markRaw(ChartBarIcon) }

const exportEvents = () => { alert('Olaylar dışa aktarılıyor...') }
const loadData = async () => { const r = await store.fetchAll({}); events.value = r?.data || [] }
onMounted(() => { loadData() })
</script>