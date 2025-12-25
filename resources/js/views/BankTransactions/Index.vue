<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Banka İşlemleri</h1>
        <p class="mt-2 text-sm text-gray-600">Banka hesaplarınızdaki hareketleri takip edin</p>
      </div>
      <div class="flex gap-3">
        <button @click="exportData" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
          <ArrowDownTrayIcon class="h-5 w-5 mr-2" />Dışa Aktar
        </button>
        <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
          <PlusIcon class="h-5 w-5 mr-2" />Yeni İşlem
        </button>
      </div>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><ArrowsRightLeftIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam İşlem</p><p class="text-2xl font-bold">{{ transactions.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><ArrowUpIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Gelen</p><p class="text-2xl font-bold text-green-600">{{ formatCurrency(totalIncome) }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100"><ArrowDownIcon class="h-6 w-6 text-red-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Giden</p><p class="text-2xl font-bold text-red-600">{{ formatCurrency(totalExpense) }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><ChartBarIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Net</p><p :class="['text-2xl font-bold', netAmount >= 0 ? 'text-green-600' : 'text-red-600']">{{ formatCurrency(netAmount) }}</p></div>
        </div>
      </div>
    </div>

    <!-- Filtreler -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
      <div class="flex flex-wrap gap-3 items-center">
        <select v-model="filters.account_id" class="rounded-lg border-gray-300 text-sm">
          <option value="">Tüm Hesaplar</option>
          <option v-for="a in accounts" :key="a.id" :value="a.id">{{ a.bank_name }} - {{ a.account_name }}</option>
        </select>
        <div class="flex rounded-lg border border-gray-200 overflow-hidden">
          <button v-for="t in typeFilters" :key="t.value" @click="filters.type = filters.type === t.value ? '' : t.value" :class="['px-3 py-2 text-xs font-medium', filters.type === t.value ? t.activeClass : 'bg-white text-gray-700 hover:bg-gray-50']">{{ t.label }}</button>
        </div>
        <input v-model="filters.date" type="date" class="rounded-lg border-gray-300 text-sm" />
      </div>
    </div>

    <!-- İşlem Listesi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hesap</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Açıklama</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tip</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tutar</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="tx in filteredTransactions" :key="tx.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm text-gray-900">{{ formatDate(tx.transaction_date) }}</td>
            <td class="px-6 py-4 text-sm text-gray-600">{{ tx.bank_account?.bank_name }}</td>
            <td class="px-6 py-4 text-sm text-gray-900">{{ tx.description || '-' }}</td>
            <td class="px-6 py-4 text-center">
              <span :class="['px-2 py-1 text-xs rounded-full font-medium', tx.type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                {{ tx.type === 'income' ? 'Gelir' : 'Gider' }}
              </span>
            </td>
            <td class="px-6 py-4 text-right text-sm font-medium" :class="tx.type === 'income' ? 'text-green-600' : 'text-red-600'">
              {{ tx.type === 'income' ? '+' : '-' }}{{ formatCurrency(tx.amount) }}
            </td>
            <td class="px-6 py-4 text-right">
              <button @click="handleDelete(tx.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="filteredTransactions.length === 0" class="p-12 text-center">
        <ArrowsRightLeftIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">İşlem bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">Yeni İşlem</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Hesap *</label>
            <select v-model="form.bank_account_id" required class="w-full rounded-lg border-gray-300">
              <option value="">Seçin</option>
              <option v-for="a in accounts" :key="a.id" :value="a.id">{{ a.bank_name }} - {{ a.account_name }}</option>
            </select>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-2">Tip *</label>
            <div class="grid grid-cols-2 gap-2">
              <button type="button" @click="form.type = 'income'" :class="['p-3 rounded-lg border text-center', form.type === 'income' ? 'bg-green-50 border-green-500 text-green-700' : 'border-gray-200']">Gelir</button>
              <button type="button" @click="form.type = 'expense'" :class="['p-3 rounded-lg border text-center', form.type === 'expense' ? 'bg-red-50 border-red-500 text-red-700' : 'border-gray-200']">Gider</button>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Tutar *</label><input v-model.number="form.amount" type="number" step="0.01" required class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Tarih *</label><input v-model="form.transaction_date" type="date" required class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label><input v-model="form.description" class="w-full rounded-lg border-gray-300" /></div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, ArrowsRightLeftIcon, ArrowUpIcon, ArrowDownIcon, ChartBarIcon, TrashIcon, XMarkIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline'
import { useBankTransactionStore } from '@/stores/banktransaction'
import { useBankAccountStore } from '@/stores/bankaccount'

const store = useBankTransactionStore()
const accountStore = useBankAccountStore()
const showModal = ref(false)
const filters = ref({ account_id: '', type: '', date: '' })
const typeFilters = [{ value: '', label: 'Tümü', activeClass: 'bg-blue-600 text-white' }, { value: 'income', label: 'Gelir', activeClass: 'bg-green-600 text-white' }, { value: 'expense', label: 'Gider', activeClass: 'bg-red-600 text-white' }]
const form = ref({ bank_account_id: '', type: 'income', amount: 0, transaction_date: new Date().toISOString().split('T')[0], description: '' })
const transactions = ref<any[]>([])
const accounts = computed(() => accountStore.items || [])

const filteredTransactions = computed(() => {
  let r = transactions.value
  if (filters.value.account_id) r = r.filter(t => t.bank_account_id === filters.value.account_id)
  if (filters.value.type) r = r.filter(t => t.type === filters.value.type)
  if (filters.value.date) r = r.filter(t => t.transaction_date?.startsWith(filters.value.date))
  return r.sort((a, b) => new Date(b.transaction_date).getTime() - new Date(a.transaction_date).getTime())
})

const totalIncome = computed(() => transactions.value.filter(t => t.type === 'income').reduce((s, t) => s + (parseFloat(t.amount) || 0), 0))
const totalExpense = computed(() => transactions.value.filter(t => t.type === 'expense').reduce((s, t) => s + (parseFloat(t.amount) || 0), 0))
const netAmount = computed(() => totalIncome.value - totalExpense.value)
const formatCurrency = (n: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(n)
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(new Date(d)) : '-'

const openCreateModal = () => { form.value = { bank_account_id: '', type: 'income', amount: 0, transaction_date: new Date().toISOString().split('T')[0], description: '' }; showModal.value = true }
const handleSubmit = async () => { await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const exportData = () => {
  const csv = [['Tarih', 'Hesap', 'Tip', 'Tutar', 'Açıklama'].join(','), ...filteredTransactions.value.map(t => [t.transaction_date, t.bank_account?.bank_name, t.type === 'income' ? 'Gelir' : 'Gider', t.amount, t.description || ''].join(','))].join('\n')
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a'); link.href = URL.createObjectURL(blob); link.download = `banka_islemleri_${new Date().toISOString().split('T')[0]}.csv`; link.click()
}
const loadData = async () => { const r = await store.fetchAll({}); transactions.value = r?.data || []; await accountStore.fetchAll({}) }
onMounted(() => { loadData() })
</script>