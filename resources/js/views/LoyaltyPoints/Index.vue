<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Sadakat Puanları</h1>
        <p class="mt-2 text-sm text-gray-600">Müşteri sadakat puanlarını görüntüleyin ve yönetin</p>
      </div>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-amber-100"><StarIcon class="h-6 w-6 text-amber-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Puan</p><p class="text-2xl font-bold">{{ totalPoints.toLocaleString() }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><UsersIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Müşteri Sayısı</p><p class="text-2xl font-bold text-blue-600">{{ points.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><ArrowTrendingUpIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Ort. Puan</p><p class="text-2xl font-bold text-green-600">{{ avgPoints }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><TrophyIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">En Yüksek</p><p class="text-2xl font-bold text-purple-600">{{ maxPoints.toLocaleString() }}</p></div>
        </div>
      </div>
    </div>

    <!-- Puan Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-4 border-b border-gray-100">
        <input v-model="search" type="text" placeholder="Müşteri ara..." class="w-full rounded-lg border-gray-300" />
      </div>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Puan</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Seviye</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Son İşlem</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="p in filteredPoints" :key="p.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center"><span class="text-amber-600 font-bold">{{ getInitials(p.customer?.name) }}</span></div>
                <div>
                  <div class="font-medium text-gray-900">{{ p.customer?.name || 'Müşteri' }}</div>
                  <div class="text-xs text-gray-500">{{ p.customer?.email || '' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center">
              <div class="flex items-center justify-center gap-1">
                <StarIcon class="h-4 w-4 text-amber-500" />
                <span class="text-lg font-bold text-gray-900">{{ (p.balance || 0).toLocaleString() }}</span>
              </div>
            </td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', getLevelBadge(p.balance)]">{{ getLevelLabel(p.balance) }}</span></td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ formatDate(p.updated_at) }}</td>
            <td class="px-6 py-4 text-right">
              <button @click="addPoints(p)" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg"><PlusIcon class="h-4 w-4" /></button>
              <button @click="viewHistory(p)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"><EyeIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="filteredPoints.length === 0" class="p-12 text-center">
        <StarIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Puan kaydı bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { StarIcon, UsersIcon, ArrowTrendingUpIcon, TrophyIcon, PlusIcon, EyeIcon } from '@heroicons/vue/24/outline'
import { useLoyaltyPointStore } from '@/stores/loyaltypoint'

const store = useLoyaltyPointStore()
const search = ref('')
const points = ref<any[]>([])

const totalPoints = computed(() => points.value.reduce((s, p) => s + (p.balance || 0), 0))
const avgPoints = computed(() => points.value.length ? Math.round(totalPoints.value / points.value.length) : 0)
const maxPoints = computed(() => Math.max(...points.value.map(p => p.balance || 0), 0))
const filteredPoints = computed(() => points.value.filter(p => !search.value || p.customer?.name?.toLowerCase().includes(search.value.toLowerCase())))
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR').format(new Date(d)) : '-'
const getInitials = (name: string) => name ? name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase() : '?'
const getLevelLabel = (pts: number) => pts >= 10000 ? 'Platin' : pts >= 5000 ? 'Altın' : pts >= 1000 ? 'Gümüş' : 'Bronz'
const getLevelBadge = (pts: number) => pts >= 10000 ? 'bg-purple-100 text-purple-800' : pts >= 5000 ? 'bg-amber-100 text-amber-800' : pts >= 1000 ? 'bg-gray-200 text-gray-700' : 'bg-orange-100 text-orange-800'

const addPoints = (p: any) => { alert(`${p.customer?.name || 'Müşteri'} için puan ekleniyor...`) }
const viewHistory = (p: any) => { alert(`${p.customer?.name || 'Müşteri'} puan geçmişi görüntüleniyor...`) }
const loadData = async () => { const r = await store.fetchAll({}); points.value = r?.data || [] }
onMounted(() => { loadData() })
</script>