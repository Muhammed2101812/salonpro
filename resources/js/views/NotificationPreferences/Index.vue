<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Bildirim Tercihleri</h1>
        <p class="mt-2 text-sm text-gray-600">Kullanıcı bildirim tercihlerini görüntüleyin</p>
      </div>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-indigo-100"><BellIcon class="h-6 w-6 text-indigo-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Kullanıcı</p><p class="text-2xl font-bold">{{ preferences.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><EnvelopeIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">E-posta Açık</p><p class="text-2xl font-bold text-blue-600">{{ emailEnabledCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><DevicePhoneMobileIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">SMS Açık</p><p class="text-2xl font-bold text-green-600">{{ smsEnabledCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><BellAlertIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Push Açık</p><p class="text-2xl font-bold text-purple-600">{{ pushEnabledCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Tercih Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-4 border-b border-gray-100">
        <input v-model="search" type="text" placeholder="Kullanıcı ara..." class="w-full rounded-lg border-gray-300" />
      </div>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kullanıcı</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">E-posta</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">SMS</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Push</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pazarlama</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Güncelleme</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="p in filteredPreferences" :key="p.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                  <span class="text-indigo-600 font-bold">{{ getInitials(p.user?.name) }}</span>
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ p.user?.name || 'Kullanıcı' }}</div>
                  <div class="text-xs text-gray-500">{{ p.user?.email || '' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center"><span :class="['inline-flex h-6 w-6 items-center justify-center rounded-full', p.email_enabled ? 'bg-green-100' : 'bg-gray-100']"><CheckIcon v-if="p.email_enabled" class="h-4 w-4 text-green-600" /><XMarkIcon v-else class="h-4 w-4 text-gray-400" /></span></td>
            <td class="px-6 py-4 text-center"><span :class="['inline-flex h-6 w-6 items-center justify-center rounded-full', p.sms_enabled ? 'bg-green-100' : 'bg-gray-100']"><CheckIcon v-if="p.sms_enabled" class="h-4 w-4 text-green-600" /><XMarkIcon v-else class="h-4 w-4 text-gray-400" /></span></td>
            <td class="px-6 py-4 text-center"><span :class="['inline-flex h-6 w-6 items-center justify-center rounded-full', p.push_enabled ? 'bg-green-100' : 'bg-gray-100']"><CheckIcon v-if="p.push_enabled" class="h-4 w-4 text-green-600" /><XMarkIcon v-else class="h-4 w-4 text-gray-400" /></span></td>
            <td class="px-6 py-4 text-center"><span :class="['inline-flex h-6 w-6 items-center justify-center rounded-full', p.marketing_enabled ? 'bg-green-100' : 'bg-gray-100']"><CheckIcon v-if="p.marketing_enabled" class="h-4 w-4 text-green-600" /><XMarkIcon v-else class="h-4 w-4 text-gray-400" /></span></td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ formatDate(p.updated_at) }}</td>
          </tr>
        </tbody>
      </table>
      <div v-if="filteredPreferences.length === 0" class="p-12 text-center">
        <BellIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Tercih bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { BellIcon, EnvelopeIcon, DevicePhoneMobileIcon, BellAlertIcon, CheckIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useNotificationPreferenceStore } from '@/stores/notificationpreference'

const store = useNotificationPreferenceStore()
const search = ref('')
const preferences = ref<any[]>([])

const emailEnabledCount = computed(() => preferences.value.filter(p => p.email_enabled).length)
const smsEnabledCount = computed(() => preferences.value.filter(p => p.sms_enabled).length)
const pushEnabledCount = computed(() => preferences.value.filter(p => p.push_enabled).length)
const filteredPreferences = computed(() => preferences.value.filter(p => !search.value || p.user?.name?.toLowerCase().includes(search.value.toLowerCase())))
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR').format(new Date(d)) : '-'
const getInitials = (name: string) => name ? name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase() : '?'

const loadData = async () => { const r = await store.fetchAll({}); preferences.value = r?.data || [] }
onMounted(() => { loadData() })
</script>