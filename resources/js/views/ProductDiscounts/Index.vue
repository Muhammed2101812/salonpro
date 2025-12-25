<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Ürün İndirimleri</h1>
        <p class="mt-2 text-sm text-gray-600">Ürün indirim kampanyalarını yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni İndirim
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100"><TagIcon class="h-6 w-6 text-red-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam İndirim</p><p class="text-2xl font-bold">{{ discounts.length }}</p></div>
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
          <div class="p-3 rounded-full bg-orange-100"><PercentBadgeIcon class="h-6 w-6 text-orange-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Ort. İndirim</p><p class="text-2xl font-bold text-orange-600">%{{ avgDiscount }}</p></div>
        </div>
      </div>
    </div>

    <!-- İndirim Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">İndirim</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tarih Aralığı</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="d in discounts" :key="d.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-red-100 flex items-center justify-center"><TagIcon class="h-5 w-5 text-red-600" /></div>
                <span class="font-medium text-gray-900">{{ d.product?.name || d.name || 'İndirim' }}</span>
              </div>
            </td>
            <td class="px-6 py-4 text-center">
              <span class="text-lg font-bold text-red-600">
                {{ d.type === 'percentage' ? '%' + d.value : formatCurrency(d.value) }}
              </span>
            </td>
            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ formatDate(d.start_date) }} - {{ formatDate(d.end_date) }}</td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', getStatusBadge(d)]">{{ getStatusLabel(d) }}</span></td>
            <td class="px-6 py-4 text-right">
              <button @click="editDiscount(d)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(d.id)" class="p-1.5 text-gray-600 hover:bg-gray-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="discounts.length === 0" class="p-12 text-center">
        <TagIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">İndirim bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'İndirimi Düzenle' : 'Yeni İndirim' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">İndirim Adı</label><input v-model="form.name" class="w-full rounded-lg border-gray-300" /></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Tür</label>
              <select v-model="form.type" class="w-full rounded-lg border-gray-300">
                <option value="percentage">Yüzde (%)</option>
                <option value="fixed">Sabit (₺)</option>
              </select>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Değer</label><input v-model.number="form.value" type="number" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Başlangıç</label><input v-model="form.start_date" type="date" class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Bitiş</label><input v-model="form.end_date" type="date" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, TagIcon, CheckCircleIcon, PercentBadgeIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useProductDiscountStore } from '@/stores/productdiscount'

const store = useProductDiscountStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', type: 'percentage', value: 0, start_date: '', end_date: '' })
const discounts = ref<any[]>([])

const activeCount = computed(() => discounts.value.filter(d => isActive(d)).length)
const avgDiscount = computed(() => { const pcts = discounts.value.filter(d => d.type === 'percentage'); return pcts.length ? Math.round(pcts.reduce((s, d) => s + d.value, 0) / pcts.length) : 0 })
const formatCurrency = (a: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(a || 0)
const formatDate = (d: string) => d ? new Intl.DateTimeFormat('tr-TR').format(new Date(d)) : '-'
const isActive = (d: any) => { const now = new Date(); return (!d.start_date || new Date(d.start_date) <= now) && (!d.end_date || new Date(d.end_date) >= now) }
const getStatusLabel = (d: any) => isActive(d) ? 'Aktif' : 'Pasif'
const getStatusBadge = (d: any) => isActive(d) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'

const openCreateModal = () => { form.value = { name: '', type: 'percentage', value: 0, start_date: '', end_date: '' }; isEdit.value = false; showModal.value = true }
const editDiscount = (d: any) => { form.value = { ...d }; isEdit.value = true; editingId.value = d.id; showModal.value = true }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); discounts.value = r?.data || [] }
onMounted(() => { loadData() })
</script>