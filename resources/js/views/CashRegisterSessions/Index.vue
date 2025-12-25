<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Kasa Oturumları</h1>
        <p class="mt-2 text-sm text-gray-600">Kasa açılış ve kapanış işlemlerini takip edin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Oturum
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-cyan-100"><ClockIcon class="h-6 w-6 text-cyan-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Oturum</p><p class="text-2xl font-bold">{{ sessions.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><LockOpenIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Açık</p><p class="text-2xl font-bold text-green-600">{{ openCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><BanknotesIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Bugün Çekim</p><p class="text-2xl font-bold text-blue-600">{{ formatCurrency(todayTotal) }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><ArrowsRightLeftIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Fark</p><p :class="['text-2xl font-bold', totalDiff >= 0 ? 'text-green-600' : 'text-red-600']">{{ formatCurrency(totalDiff) }}</p></div>
        </div>
      </div>
    </div>

    <!-- Oturum Listesi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kasa</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Açılış</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kapanış</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Açılış Bakiye</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Kapanış Bakiye</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="s in sessions" :key="s.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ s.cash_register?.name || 'Kasa' }}</td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ formatDateTime(s.opened_at) }}</td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ s.closed_at ? formatDateTime(s.closed_at) : '-' }}</td>
            <td class="px-6 py-4 text-center">
              <span :class="['px-2 py-1 text-xs rounded-full font-medium', s.closed_at ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800']">
                {{ s.closed_at ? 'Kapalı' : 'Açık' }}
              </span>
            </td>
            <td class="px-6 py-4 text-right text-sm font-medium">{{ formatCurrency(s.opening_balance || 0) }}</td>
            <td class="px-6 py-4 text-right text-sm font-medium">{{ s.closed_at ? formatCurrency(s.closing_balance || 0) : '-' }}</td>
            <td class="px-6 py-4 text-right">
              <button v-if="!s.closed_at" @click="closeSession(s)" class="text-sm font-medium text-orange-600 hover:text-orange-700">Kapat</button>
              <button @click="viewDetails(s)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg ml-2"><EyeIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="sessions.length === 0" class="p-12 text-center">
        <ClockIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Oturum bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">Yeni Oturum Aç</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Açılış Bakiyesi *</label><input v-model.number="form.opening_balance" type="number" step="0.01" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Not</label><textarea v-model="form.notes" rows="2" class="w-full rounded-lg border-gray-300"></textarea></div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-cyan-600 text-white rounded-lg">Oturum Aç</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, ClockIcon, LockOpenIcon, BanknotesIcon, ArrowsRightLeftIcon, EyeIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useCashRegisterSessionStore } from '@/stores/cashregistersession'

const store = useCashRegisterSessionStore()
const showModal = ref(false)
const form = ref({ opening_balance: 0, notes: '' })
const sessions = ref<any[]>([])

const openCount = computed(() => sessions.value.filter(s => !s.closed_at).length)
const todayTotal = computed(() => { const t = new Date().toISOString().split('T')[0]; return sessions.value.filter(s => s.opened_at?.startsWith(t)).reduce((sum, s) => sum + (parseFloat(s.closing_balance) || 0), 0) })
const totalDiff = computed(() => sessions.value.reduce((sum, s) => sum + ((parseFloat(s.closing_balance) || 0) - (parseFloat(s.opening_balance) || 0)), 0))
const formatCurrency = (n: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(n)
const formatDateTime = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(d)) : '-'

const openCreateModal = () => { form.value = { opening_balance: 0, notes: '' }; showModal.value = true }
const handleSubmit = async () => { await store.create({ ...form.value, opened_at: new Date().toISOString() }); showModal.value = false; await loadData() }
const closeSession = async (s: any) => { if (confirm('Oturumu kapatmak istiyor musunuz?')) { await store.update(s.id, { closed_at: new Date().toISOString() }); await loadData() } }
const viewDetails = (s: any) => { alert(`Oturum: ${s.cash_register?.name}\nAçılış: ${formatCurrency(s.opening_balance || 0)}\nKapanış: ${s.closing_balance ? formatCurrency(s.closing_balance) : 'Açık'}`) }
const loadData = async () => { const r = await store.fetchAll({}); sessions.value = r?.data || [] }
onMounted(() => { loadData() })
</script>