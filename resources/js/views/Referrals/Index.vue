<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Referanslar</h1>
        <p class="mt-2 text-sm text-gray-600">Müşteri referans kayıtlarını görüntüleyin</p>
      </div>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-pink-100"><UserGroupIcon class="h-6 w-6 text-pink-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Referans</p><p class="text-2xl font-bold">{{ referrals.length }}</p></div>
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
          <div class="p-3 rounded-full bg-yellow-100"><ClockIcon class="h-6 w-6 text-yellow-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Bekleyen</p><p class="text-2xl font-bold text-yellow-600">{{ pendingCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><CurrencyDollarIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Ödül</p><p class="text-2xl font-bold text-purple-600">{{ formatCurrency(totalRewards) }}</p></div>
        </div>
      </div>
    </div>

    <!-- Referans Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Davet Eden</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Davet Edilen</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ödül</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tarih</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="r in referrals" :key="r.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-pink-100 flex items-center justify-center">
                  <span class="text-pink-600 font-bold">{{ getInitials(r.referrer?.name) }}</span>
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ r.referrer?.name || 'Davet Eden' }}</div>
                  <div class="text-xs text-gray-500">{{ r.referrer?.email || '' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                  <span class="text-blue-600 font-bold">{{ getInitials(r.referred?.name) }}</span>
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ r.referred?.name || 'Davet Edilen' }}</div>
                  <div class="text-xs text-gray-500">{{ r.referred?.email || '' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', getStatusBadge(r.status)]">{{ getStatusLabel(r.status) }}</span></td>
            <td class="px-6 py-4 text-center">
              <span v-if="r.reward_amount" class="text-sm font-medium text-purple-600">{{ formatCurrency(r.reward_amount) }}</span>
              <span v-else class="text-sm text-gray-400">-</span>
            </td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ formatDate(r.created_at) }}</td>
          </tr>
        </tbody>
      </table>
      <div v-if="referrals.length === 0" class="p-12 text-center">
        <UserGroupIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Referans bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { UserGroupIcon, CheckCircleIcon, ClockIcon, CurrencyDollarIcon } from '@heroicons/vue/24/outline'
import { useReferralStore } from '@/stores/referral'

const store = useReferralStore()
const referrals = ref<any[]>([])

const successCount = computed(() => referrals.value.filter(r => r.status === 'completed' || r.status === 'converted').length)
const pendingCount = computed(() => referrals.value.filter(r => r.status === 'pending').length)
const totalRewards = computed(() => referrals.value.reduce((s, r) => s + (r.reward_amount || 0), 0))
const formatCurrency = (a: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(a || 0)
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR').format(new Date(d)) : '-'
const getInitials = (name: string) => name ? name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase() : '?'
const getStatusLabel = (s: string) => ({ pending: 'Bekliyor', completed: 'Tamamlandı', converted: 'Dönüştürüldü', expired: 'Süresi Doldu' }[s] || s || 'Bekliyor')
const getStatusBadge = (s: string) => ({ pending: 'bg-yellow-100 text-yellow-800', completed: 'bg-green-100 text-green-800', converted: 'bg-green-100 text-green-800', expired: 'bg-gray-100 text-gray-800' }[s] || 'bg-gray-100 text-gray-800')

const loadData = async () => { const r = await store.fetchAll({}); referrals.value = r?.data || [] }
onMounted(() => { loadData() })
</script>