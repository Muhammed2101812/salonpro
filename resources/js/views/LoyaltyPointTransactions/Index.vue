<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Sadakat Puan İşlemleri</h1>
        <p class="mt-2 text-sm text-gray-600">Puan kazanma ve harcama geçmişini görüntüleyin</p>
      </div>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-amber-100"><ArrowsRightLeftIcon class="h-6 w-6 text-amber-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam İşlem</p><p class="text-2xl font-bold">{{ transactions.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><ArrowUpIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Kazanılan</p><p class="text-2xl font-bold text-green-600">{{ totalEarned.toLocaleString() }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100"><ArrowDownIcon class="h-6 w-6 text-red-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Harcanan</p><p class="text-2xl font-bold text-red-600">{{ totalSpent.toLocaleString() }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><StarIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Net Bakiye</p><p class="text-2xl font-bold text-blue-600">{{ netBalance.toLocaleString() }}</p></div>
        </div>
      </div>
    </div>

    <!-- İşlem Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-4 border-b border-gray-100 flex gap-4">
        <input v-model="search" type="text" placeholder="Müşteri ara..." class="flex-1 rounded-lg border-gray-300" />
        <select v-model="typeFilter" class="rounded-lg border-gray-300">
          <option value="">Tüm Türler</option>
          <option value="earn">Kazanım</option>
          <option value="spend">Harcama</option>
          <option value="expire">Süre Dolumu</option>
        </select>
      </div>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tür</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Puan</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Açıklama</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tarih</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="t in filteredTransactions" :key="t.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center">
                  <span class="text-amber-600 font-bold">{{ getInitials(t.customer?.name) }}</span>
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ t.customer?.name || 'Müşteri' }}</div>
                  <div class="text-xs text-gray-500">{{ t.customer?.email || '' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', getTypeBadge(t.type)]">{{ getTypeLabel(t.type) }}</span></td>
            <td class="px-6 py-4 text-center">
              <span :class="['text-lg font-bold', t.type === 'earn' ? 'text-green-600' : 'text-red-600']">
                {{ t.type === 'earn' ? '+' : '-' }}{{ Math.abs(t.points || 0).toLocaleString() }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600">{{ t.description || '-' }}</td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ formatDate(t.created_at) }}</td>
          </tr>
        </tbody>
      </table>
      <div v-if="filteredTransactions.length === 0" class="p-12 text-center">
        <ArrowsRightLeftIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">İşlem bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { ArrowsRightLeftIcon, ArrowUpIcon, ArrowDownIcon, StarIcon } from '@heroicons/vue/24/outline'
import { useLoyaltyPointTransactionStore } from '@/stores/loyaltypointtransaction'

const store = useLoyaltyPointTransactionStore()
const search = ref('')
const typeFilter = ref('')
const transactions = ref<any[]>([])

const totalEarned = computed(() => transactions.value.filter(t => t.type === 'earn').reduce((s, t) => s + (t.points || 0), 0))
const totalSpent = computed(() => transactions.value.filter(t => t.type === 'spend').reduce((s, t) => s + Math.abs(t.points || 0), 0))
const netBalance = computed(() => totalEarned.value - totalSpent.value)
const filteredTransactions = computed(() => transactions.value.filter(t => (!typeFilter.value || t.type === typeFilter.value) && (!search.value || t.customer?.name?.toLowerCase().includes(search.value.toLowerCase()))))
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR').format(new Date(d)) : '-'
const getInitials = (name: string) => name ? name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase() : '?'
const getTypeLabel = (t: string) => ({ earn: 'Kazanım', spend: 'Harcama', expire: 'Süre Dolumu', adjust: 'Düzeltme' }[t] || t)
const getTypeBadge = (t: string) => ({ earn: 'bg-green-100 text-green-800', spend: 'bg-red-100 text-red-800', expire: 'bg-gray-100 text-gray-800', adjust: 'bg-blue-100 text-blue-800' }[t] || 'bg-gray-100 text-gray-800')

const loadData = async () => { const r = await store.fetchAll({}); transactions.value = r?.data || [] }
onMounted(() => { loadData() })
</script>