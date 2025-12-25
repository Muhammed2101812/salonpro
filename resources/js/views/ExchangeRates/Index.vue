<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Döviz Kurları</h1>
        <p class="mt-2 text-sm text-gray-600">Güncel döviz kurlarını görüntüleyin ve yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Kur Ekle
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><ArrowsRightLeftIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Kur</p><p class="text-2xl font-bold">{{ rates.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><ArrowTrendingUpIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">USD/TRY</p><p class="text-2xl font-bold text-green-600">{{ usdRate }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><ArrowTrendingUpIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">EUR/TRY</p><p class="text-2xl font-bold text-purple-600">{{ eurRate }}</p></div>
        </div>
      </div>
    </div>

    <!-- Kur Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Para Birimi</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kaynak</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Hedef</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Kur</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Güncelleme</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="r in rates" :key="r.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                  <span class="text-sm font-bold text-white">{{ r.source_currency?.substring(0, 2) || '₺' }}</span>
                </div>
                <span class="font-medium text-gray-900">{{ r.source_currency }}/{{ r.target_currency }}</span>
              </div>
            </td>
            <td class="px-6 py-4 text-center"><span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">{{ r.source_currency || 'TRY' }}</span></td>
            <td class="px-6 py-4 text-center"><span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">{{ r.target_currency || 'USD' }}</span></td>
            <td class="px-6 py-4 text-right"><span class="text-lg font-bold text-gray-900">{{ formatNumber(r.rate || 0) }}</span></td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ formatDateTime(r.updated_at) }}</td>
            <td class="px-6 py-4 text-right">
              <button @click="editRate(r)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(r.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="rates.length === 0" class="p-12 text-center">
        <ArrowsRightLeftIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Döviz kuru bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Kuru Düzenle' : 'Yeni Kur' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Kaynak *</label><input v-model="form.source_currency" required class="w-full rounded-lg border-gray-300 uppercase" maxlength="3" placeholder="TRY" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Hedef *</label><input v-model="form.target_currency" required class="w-full rounded-lg border-gray-300 uppercase" maxlength="3" placeholder="USD" /></div>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Kur *</label><input v-model.number="form.rate" type="number" step="0.0001" required class="w-full rounded-lg border-gray-300" /></div>
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
import { PlusIcon, ArrowsRightLeftIcon, ArrowTrendingUpIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useExchangeRateStore } from '@/stores/exchangerate'

const store = useExchangeRateStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ source_currency: '', target_currency: '', rate: 0 })
const rates = ref<any[]>([])

const usdRate = computed(() => { const r = rates.value.find(r => r.source_currency === 'USD' && r.target_currency === 'TRY'); return r?.rate?.toFixed(4) || '0.0000' })
const eurRate = computed(() => { const r = rates.value.find(r => r.source_currency === 'EUR' && r.target_currency === 'TRY'); return r?.rate?.toFixed(4) || '0.0000' })
const formatNumber = (n: number) => n.toFixed(4)
const formatDateTime = (d: string) => d ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).format(new Date(d)) : '-'

const openCreateModal = () => { form.value = { source_currency: '', target_currency: '', rate: 0 }; isEdit.value = false; showModal.value = true }
const editRate = (r: any) => { form.value = { ...r }; isEdit.value = true; editingId.value = r.id; showModal.value = true }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); rates.value = r?.data || [] }
onMounted(() => { loadData() })
</script>