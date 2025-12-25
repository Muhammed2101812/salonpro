<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Vergi Oranları</h1>
        <p class="mt-2 text-sm text-gray-600">Ürün ve hizmet vergi oranlarını yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Vergi Oranı
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-slate-100"><ReceiptPercentIcon class="h-6 w-6 text-slate-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Oran</p><p class="text-2xl font-bold">{{ rates.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><CheckCircleIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Aktif</p><p class="text-2xl font-bold text-green-600">{{ activeCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><CalculatorIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Ort. Oran</p><p class="text-2xl font-bold text-blue-600">%{{ avgRate }}</p></div>
        </div>
      </div>
    </div>

    <!-- Vergi Oranları Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vergi Adı</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Oran</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tür</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="r in rates" :key="r.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-slate-100 flex items-center justify-center">
                  <ReceiptPercentIcon class="h-5 w-5 text-slate-600" />
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ r.name }}</div>
                  <code class="text-xs text-gray-500">{{ r.code || '' }}</code>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center"><span class="text-xl font-bold text-slate-700">%{{ r.rate }}</span></td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', getTypeBadge(r.type)]">{{ getTypeLabel(r.type) }}</span></td>
            <td class="px-6 py-4 text-center">
              <button @click="toggleRate(r)" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', r.is_active ? 'bg-green-500' : 'bg-gray-300']">
                <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', r.is_active ? 'translate-x-6' : 'translate-x-1']" />
              </button>
            </td>
            <td class="px-6 py-4 text-right">
              <button @click="editRate(r)" class="p-1.5 text-slate-600 hover:bg-slate-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(r.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="rates.length === 0" class="p-12 text-center">
        <ReceiptPercentIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Vergi oranı bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Vergi Oranını Düzenle' : 'Yeni Vergi Oranı' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Vergi Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" placeholder="KDV, ÖTV vb." /></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Oran (%) *</label><input v-model.number="form.rate" required type="number" step="0.01" class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Kod</label><input v-model="form.code" class="w-full rounded-lg border-gray-300 font-mono" placeholder="KDV18" /></div>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Tür</label>
            <select v-model="form.type" class="w-full rounded-lg border-gray-300">
              <option value="vat">KDV</option>
              <option value="service">Hizmet Vergisi</option>
              <option value="special">Özel Vergi</option>
            </select>
          </div>
          <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_active" class="rounded border-gray-300 text-slate-600" /><span class="text-sm">Aktif</span></label>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-slate-700 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, ReceiptPercentIcon, CheckCircleIcon, CalculatorIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useTaxRateStore } from '@/stores/taxrate'

const store = useTaxRateStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', rate: 18, code: '', type: 'vat', is_active: true })
const rates = ref<any[]>([])

const activeCount = computed(() => rates.value.filter(r => r.is_active).length)
const avgRate = computed(() => rates.value.length ? Math.round(rates.value.reduce((s, r) => s + (r.rate || 0), 0) / rates.value.length) : 0)
const getTypeLabel = (t: string) => ({ vat: 'KDV', service: 'Hizmet', special: 'Özel' }[t] || t)
const getTypeBadge = (t: string) => ({ vat: 'bg-blue-100 text-blue-800', service: 'bg-green-100 text-green-800', special: 'bg-purple-100 text-purple-800' }[t] || 'bg-gray-100 text-gray-800')

const openCreateModal = () => { form.value = { name: '', rate: 18, code: '', type: 'vat', is_active: true }; isEdit.value = false; showModal.value = true }
const editRate = (r: any) => { form.value = { ...r }; isEdit.value = true; editingId.value = r.id; showModal.value = true }
const toggleRate = async (r: any) => { await store.update(r.id, { is_active: !r.is_active }); await loadData() }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); rates.value = r?.data || [] }
onMounted(() => { loadData() })
</script>