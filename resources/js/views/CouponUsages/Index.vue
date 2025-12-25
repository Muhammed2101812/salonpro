<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Kupon Kullanımları</h1>
        <p class="mt-2 text-sm text-gray-600">Kupon kullanım geçmişini görüntüleyin</p>
      </div>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-fuchsia-100"><TicketIcon class="h-6 w-6 text-fuchsia-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Kullanım</p><p class="text-2xl font-bold">{{ usages.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><CurrencyDollarIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam İndirim</p><p class="text-2xl font-bold text-green-600">{{ formatCurrency(totalDiscount) }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><UsersIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Müşteri</p><p class="text-2xl font-bold text-blue-600">{{ uniqueCustomers }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><TagIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Kupon</p><p class="text-2xl font-bold text-purple-600">{{ uniqueCoupons }}</p></div>
        </div>
      </div>
    </div>

    <!-- Kullanım Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kupon</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">İndirim</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sipariş</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tarih</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="u in usages" :key="u.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-fuchsia-100 flex items-center justify-center">
                  <TicketIcon class="h-5 w-5 text-fuchsia-600" />
                </div>
                <div>
                  <code class="text-sm font-bold text-gray-900">{{ u.coupon?.code || 'KUPON' }}</code>
                  <div class="text-xs text-gray-500">{{ u.coupon?.name || '' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="font-medium text-gray-900">{{ u.customer?.name || 'Müşteri' }}</div>
              <div class="text-xs text-gray-500">{{ u.customer?.email || '' }}</div>
            </td>
            <td class="px-6 py-4 text-center"><span class="text-lg font-bold text-green-600">{{ formatCurrency(u.discount_amount) }}</span></td>
            <td class="px-6 py-4 text-center">
              <span v-if="u.order_id" class="text-sm text-blue-600">#{{ u.order_id.slice(0, 8) }}</span>
              <span v-else class="text-sm text-gray-400">-</span>
            </td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ formatDate(u.used_at || u.created_at) }}</td>
          </tr>
        </tbody>
      </table>
      <div v-if="usages.length === 0" class="p-12 text-center">
        <TicketIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Kullanım bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { TicketIcon, CurrencyDollarIcon, UsersIcon, TagIcon } from '@heroicons/vue/24/outline'
import { useCouponUsageStore } from '@/stores/couponusage'

const store = useCouponUsageStore()
const usages = ref<any[]>([])

const totalDiscount = computed(() => usages.value.reduce((s, u) => s + (u.discount_amount || 0), 0))
const uniqueCustomers = computed(() => new Set(usages.value.filter(u => u.customer_id).map(u => u.customer_id)).size)
const uniqueCoupons = computed(() => new Set(usages.value.filter(u => u.coupon_id).map(u => u.coupon_id)).size)
const formatCurrency = (a: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(a || 0)
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR').format(new Date(d)) : '-'

const loadData = async () => { const r = await store.fetchAll({}); usages.value = r?.data || [] }
onMounted(() => { loadData() })
</script>