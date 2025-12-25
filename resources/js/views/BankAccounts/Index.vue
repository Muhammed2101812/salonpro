<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Banka Hesapları</h1>
        <p class="mt-2 text-sm text-gray-600">Banka hesaplarınızı ve bakiyelerinizi yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />
        Yeni Hesap
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-emerald-100"><BuildingLibraryIcon class="h-6 w-6 text-emerald-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Hesap Sayısı</p><p class="text-2xl font-bold">{{ accounts.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><BanknotesIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Bakiye</p><p class="text-2xl font-bold text-green-600">{{ formatCurrency(totalBalance) }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><CheckCircleIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Aktif</p><p class="text-2xl font-bold">{{ activeCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><CreditCardIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">En Yüksek</p><p class="text-2xl font-bold">{{ formatCurrency(maxBalance) }}</p></div>
        </div>
      </div>
    </div>

    <!-- Hesap Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="account in accounts" :key="account.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', account.is_active ? 'bg-emerald-500' : 'bg-gray-300']"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center">
                <BuildingLibraryIcon class="h-6 w-6 text-white" />
              </div>
              <div>
                <h3 class="font-semibold text-gray-900">{{ account.bank_name }}</h3>
                <p class="text-sm text-gray-500">{{ account.account_name }}</p>
              </div>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', account.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">
              {{ account.is_active ? 'Aktif' : 'Pasif' }}
            </span>
          </div>

          <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <p class="text-xs text-gray-500 mb-1">Güncel Bakiye</p>
            <p class="text-2xl font-bold" :class="account.balance >= 0 ? 'text-emerald-600' : 'text-red-600'">{{ formatCurrency(account.balance) }}</p>
          </div>

          <div class="space-y-2 text-sm mb-4">
            <div class="flex justify-between"><span class="text-gray-500">IBAN</span><span class="font-mono text-xs">{{ account.iban || '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Hesap No</span><span class="font-medium">{{ account.account_number || '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Para Birimi</span><span class="font-medium">{{ account.currency || 'TRY' }}</span></div>
          </div>

          <div class="flex justify-between items-center pt-3 border-t border-gray-100">
            <button @click="viewTransactions(account)" class="text-sm font-medium text-blue-600 hover:text-blue-700">İşlemleri Gör</button>
            <div class="flex gap-2">
              <button @click="editAccount(account)" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(account.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="accounts.length === 0" class="col-span-full text-center py-12">
        <BuildingLibraryIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Hesap bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Hesabı Düzenle' : 'Yeni Hesap' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Banka Adı *</label><input v-model="form.bank_name" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Hesap Adı *</label><input v-model="form.account_name" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">IBAN</label><input v-model="form.iban" class="w-full rounded-lg border-gray-300 font-mono text-sm" /></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Hesap No</label><input v-model="form.account_number" class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Bakiye</label><input v-model.number="form.balance" type="number" step="0.01" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, BuildingLibraryIcon, BanknotesIcon, CheckCircleIcon, CreditCardIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useBankAccountStore } from '@/stores/bankaccount'

const store = useBankAccountStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ bank_name: '', account_name: '', iban: '', account_number: '', balance: 0, is_active: true })
const accounts = ref<any[]>([])

const totalBalance = computed(() => accounts.value.reduce((s, a) => s + (parseFloat(a.balance) || 0), 0))
const activeCount = computed(() => accounts.value.filter(a => a.is_active).length)
const maxBalance = computed(() => Math.max(...accounts.value.map(a => parseFloat(a.balance) || 0), 0))
const formatCurrency = (n: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(n)

const openCreateModal = () => { form.value = { bank_name: '', account_name: '', iban: '', account_number: '', balance: 0, is_active: true }; isEdit.value = false; showModal.value = true }
const editAccount = (a: any) => { form.value = { ...a }; isEdit.value = true; editingId.value = a.id; showModal.value = true }
const viewTransactions = (a: any) => { window.location.href = `/bank-transactions?account_id=${a.id}` }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); accounts.value = r?.data || [] }
onMounted(() => { loadData() })
</script>