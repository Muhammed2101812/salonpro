<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Stok Uyarıları</h1>
        <p class="mt-2 text-sm text-gray-600">Düşük stok ve kritik ürün uyarılarını yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Uyarı
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-amber-100"><BellAlertIcon class="h-6 w-6 text-amber-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Uyarı</p><p class="text-2xl font-bold">{{ alerts.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100"><ExclamationTriangleIcon class="h-6 w-6 text-red-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Kritik</p><p class="text-2xl font-bold text-red-600">{{ criticalCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100"><ExclamationCircleIcon class="h-6 w-6 text-yellow-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Düşük</p><p class="text-2xl font-bold text-yellow-600">{{ lowCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><CheckCircleIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Normal</p><p class="text-2xl font-bold text-green-600">{{ normalCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Uyarı Listesi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Seviye</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Mevcut Stok</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Min. Stok</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="a in alerts" :key="a.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="font-medium text-gray-900">{{ a.product?.name || a.name }}</div>
              <div class="text-xs text-gray-500">SKU: {{ a.product?.sku || a.sku || '-' }}</div>
            </td>
            <td class="px-6 py-4 text-center">
              <span :class="['px-2 py-1 text-xs rounded-full font-medium', getLevelBadge(a.level)]">{{ getLevelLabel(a.level) }}</span>
            </td>
            <td class="px-6 py-4 text-center">
              <span :class="['text-lg font-bold', a.current_stock <= (a.min_stock || 0) ? 'text-red-600' : 'text-gray-900']">{{ a.current_stock || 0 }}</span>
            </td>
            <td class="px-6 py-4 text-center text-gray-500">{{ a.min_stock || 10 }}</td>
            <td class="px-6 py-4 text-center">
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div :class="['h-2 rounded-full', getProgressColor(a)]" :style="{ width: getProgressWidth(a) }"></div>
              </div>
            </td>
            <td class="px-6 py-4 text-right">
              <button @click="resolveAlert(a)" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg"><CheckCircleIcon class="h-4 w-4" /></button>
              <button @click="editAlert(a)" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(a.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="alerts.length === 0" class="p-12 text-center">
        <BellAlertIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Stok uyarısı bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Uyarıyı Düzenle' : 'Yeni Uyarı' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Ürün Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Mevcut Stok</label><input v-model.number="form.current_stock" type="number" class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Min. Stok</label><input v-model.number="form.min_stock" type="number" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Seviye</label>
            <select v-model="form.level" class="w-full rounded-lg border-gray-300">
              <option value="critical">Kritik</option>
              <option value="low">Düşük</option>
              <option value="normal">Normal</option>
            </select>
          </div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-amber-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, BellAlertIcon, ExclamationTriangleIcon, ExclamationCircleIcon, CheckCircleIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useStockAlertStore } from '@/stores/stockalert'

const store = useStockAlertStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', current_stock: 0, min_stock: 10, level: 'low' })
const alerts = ref<any[]>([])

const criticalCount = computed(() => alerts.value.filter(a => a.level === 'critical').length)
const lowCount = computed(() => alerts.value.filter(a => a.level === 'low').length)
const normalCount = computed(() => alerts.value.filter(a => a.level === 'normal').length)
const getLevelLabel = (l: string) => ({ critical: 'Kritik', low: 'Düşük', normal: 'Normal' }[l] || l)
const getLevelBadge = (l: string) => ({ critical: 'bg-red-100 text-red-800', low: 'bg-yellow-100 text-yellow-800', normal: 'bg-green-100 text-green-800' }[l] || 'bg-gray-100')
const getProgressColor = (a: any) => { const p = (a.current_stock || 0) / (a.min_stock || 10) * 100; return p < 50 ? 'bg-red-500' : p < 100 ? 'bg-yellow-500' : 'bg-green-500' }
const getProgressWidth = (a: any) => { const p = Math.min(100, (a.current_stock || 0) / (a.min_stock || 10) * 100); return `${p}%` }

const openCreateModal = () => { form.value = { name: '', current_stock: 0, min_stock: 10, level: 'low' }; isEdit.value = false; showModal.value = true }
const editAlert = (a: any) => { form.value = { ...a }; isEdit.value = true; editingId.value = a.id; showModal.value = true }
const resolveAlert = async (a: any) => { if (confirm('Uyarıyı çözüldü olarak işaretle?')) { await store.update(a.id, { level: 'normal' }); await loadData() } }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); alerts.value = r?.data || [] }
onMounted(() => { loadData() })
</script>