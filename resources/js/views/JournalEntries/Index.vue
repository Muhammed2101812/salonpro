<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Muhasebe Kayıtları</h1>
        <p class="mt-2 text-sm text-gray-600">Yevmiye defteri ve muhasebe kayıtlarını görüntüleyin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Kayıt
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-emerald-100"><BookOpenIcon class="h-6 w-6 text-emerald-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Kayıt</p><p class="text-2xl font-bold">{{ entries.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><ArrowTrendingUpIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Borç</p><p class="text-2xl font-bold text-green-600">{{ formatCurrency(totalDebit) }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100"><ArrowTrendingDownIcon class="h-6 w-6 text-red-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Alacak</p><p class="text-2xl font-bold text-red-600">{{ formatCurrency(totalCredit) }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><ScaleIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Bakiye</p><p :class="['text-2xl font-bold', balance >= 0 ? 'text-green-600' : 'text-red-600']">{{ formatCurrency(balance) }}</p></div>
        </div>
      </div>
    </div>

    <!-- Kayıt Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Açıklama</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hesap</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Borç</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Alacak</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="e in entries" :key="e.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(e.date || e.created_at) }}</td>
            <td class="px-6 py-4">
              <div class="font-medium text-gray-900">{{ e.description || 'Kayıt' }}</div>
              <div class="text-xs text-gray-500">{{ e.reference || '' }}</div>
            </td>
            <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">{{ e.account?.name || e.account_code || '-' }}</span></td>
            <td class="px-6 py-4 text-right font-medium text-green-600">{{ e.debit ? formatCurrency(e.debit) : '-' }}</td>
            <td class="px-6 py-4 text-right font-medium text-red-600">{{ e.credit ? formatCurrency(e.credit) : '-' }}</td>
            <td class="px-6 py-4 text-right">
              <button @click="viewEntry(e)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"><EyeIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(e.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="entries.length === 0" class="p-12 text-center">
        <BookOpenIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Kayıt bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">Yeni Kayıt</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Tarih</label><input v-model="form.date" type="date" class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Açıklama *</label><input v-model="form.description" required class="w-full rounded-lg border-gray-300" /></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Borç</label><input v-model.number="form.debit" type="number" class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Alacak</label><input v-model.number="form.credit" type="number" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Referans</label><input v-model="form.reference" class="w-full rounded-lg border-gray-300" /></div>
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
import { PlusIcon, BookOpenIcon, ArrowTrendingUpIcon, ArrowTrendingDownIcon, ScaleIcon, EyeIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useJournalEntryStore } from '@/stores/journalentry'

const store = useJournalEntryStore()
const showModal = ref(false)
const form = ref({ date: '', description: '', debit: 0, credit: 0, reference: '' })
const entries = ref<any[]>([])

const totalDebit = computed(() => entries.value.reduce((s, e) => s + (e.debit || 0), 0))
const totalCredit = computed(() => entries.value.reduce((s, e) => s + (e.credit || 0), 0))
const balance = computed(() => totalDebit.value - totalCredit.value)
const formatCurrency = (a: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(a || 0)
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR').format(new Date(d)) : '-'

const openCreateModal = () => { form.value = { date: new Date().toISOString().split('T')[0], description: '', debit: 0, credit: 0, reference: '' }; showModal.value = true }
const viewEntry = (e: any) => { alert(`Kayıt detayları görüntüleniyor...`) }
const handleSubmit = async () => { await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); entries.value = r?.data || [] }
onMounted(() => { loadData() })
</script>