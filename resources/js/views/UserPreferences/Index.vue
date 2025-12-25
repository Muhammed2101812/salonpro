<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Kullanıcı Tercihleri</h1>
        <p class="mt-2 text-sm text-gray-600">Kullanıcı tercih ayarlarını görüntüleyin ve yönetin</p>
      </div>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-gray-100"><Cog6ToothIcon class="h-6 w-6 text-gray-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Tercih</p><p class="text-2xl font-bold">{{ preferences.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><UsersIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Kullanıcı</p><p class="text-2xl font-bold text-blue-600">{{ uniqueUsers }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><SwatchIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Tema</p><p class="text-2xl font-bold text-purple-600">{{ darkModeCount }} koyu</p></div>
        </div>
      </div>
    </div>

    <!-- Tercih Grupları -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Genel Tercihler -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 border-b border-gray-100">
          <h2 class="font-semibold text-gray-900">Genel Tercihler</h2>
        </div>
        <div class="p-4 space-y-4">
          <div v-for="p in generalPreferences" :key="p.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-3">
              <div class="h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center">
                <Cog6ToothIcon class="h-4 w-4 text-blue-600" />
              </div>
              <div>
                <p class="font-medium text-gray-900">{{ p.key }}</p>
                <p class="text-xs text-gray-500">{{ p.user?.name || 'Sistem' }}</p>
              </div>
            </div>
            <span class="px-2 py-1 text-sm font-medium bg-white rounded border">{{ p.value }}</span>
          </div>
          <div v-if="generalPreferences.length === 0" class="text-center py-6 text-gray-400">Tercih bulunamadı</div>
        </div>
      </div>

      <!-- Bildirim Tercihleri -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 border-b border-gray-100">
          <h2 class="font-semibold text-gray-900">Bildirim Tercihleri</h2>
        </div>
        <div class="p-4 space-y-4">
          <div v-for="p in notificationPreferences" :key="p.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-3">
              <div class="h-8 w-8 rounded-lg bg-green-100 flex items-center justify-center">
                <BellIcon class="h-4 w-4 text-green-600" />
              </div>
              <div>
                <p class="font-medium text-gray-900">{{ p.key }}</p>
                <p class="text-xs text-gray-500">{{ p.user?.name || 'Sistem' }}</p>
              </div>
            </div>
            <span :class="['px-2 py-1 text-xs font-medium rounded-full', p.value === 'true' || p.value === true ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">
              {{ p.value === 'true' || p.value === true ? 'Açık' : 'Kapalı' }}
            </span>
          </div>
          <div v-if="notificationPreferences.length === 0" class="text-center py-6 text-gray-400">Tercih bulunamadı</div>
        </div>
      </div>
    </div>

    <!-- Tüm Tercihler Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-900">Tüm Tercihler</h2>
        <input v-model="search" type="text" placeholder="Ara..." class="rounded-lg border-gray-300 text-sm" />
      </div>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anahtar</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Değer</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kullanıcı</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="p in filteredPreferences" :key="p.id" class="hover:bg-gray-50">
            <td class="px-6 py-4"><code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ p.key }}</code></td>
            <td class="px-6 py-4 text-sm text-gray-900">{{ p.value }}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ p.user?.name || 'Sistem' }}</td>
            <td class="px-6 py-4 text-right">
              <button @click="handleDelete(p.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="filteredPreferences.length === 0" class="p-12 text-center">
        <Cog6ToothIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Tercih bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Cog6ToothIcon, UsersIcon, SwatchIcon, BellIcon, TrashIcon } from '@heroicons/vue/24/outline'
import { useUserPreferenceStore } from '@/stores/userpreference'

const store = useUserPreferenceStore()
const search = ref('')
const preferences = ref<any[]>([])

const uniqueUsers = computed(() => new Set(preferences.value.filter(p => p.user_id).map(p => p.user_id)).size)
const darkModeCount = computed(() => preferences.value.filter(p => p.key === 'theme' && p.value === 'dark').length)
const generalPreferences = computed(() => preferences.value.filter(p => !p.key?.includes('notification')).slice(0, 5))
const notificationPreferences = computed(() => preferences.value.filter(p => p.key?.includes('notification')).slice(0, 5))
const filteredPreferences = computed(() => preferences.value.filter(p => !search.value || p.key?.includes(search.value) || p.value?.includes(search.value)))

const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); preferences.value = r?.data || [] }
onMounted(() => { loadData() })
</script>