<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Ürün Barkodları</h1>
        <p class="mt-2 text-sm text-gray-600">Ürün barkod tanımlarını yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Barkod
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-gray-100"><QrCodeIcon class="h-6 w-6 text-gray-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Barkod</p><p class="text-2xl font-bold">{{ barcodes.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><CubeIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Ürün Sayısı</p><p class="text-2xl font-bold text-blue-600">{{ uniqueProducts }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><CheckCircleIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">EAN-13</p><p class="text-2xl font-bold text-green-600">{{ ean13Count }}</p></div>
        </div>
      </div>
    </div>

    <!-- Barkod Tablosu -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barkod</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tür</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="b in barcodes" :key="b.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center"><CubeIcon class="h-5 w-5 text-gray-600" /></div>
                <span class="font-medium text-gray-900">{{ b.product?.name || 'Ürün #' + (b.product_id?.slice(0, 6) || '') }}</span>
              </div>
            </td>
            <td class="px-6 py-4"><code class="text-lg font-mono bg-gray-100 px-3 py-1 rounded tracking-wider">{{ b.barcode }}</code></td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', getTypeBadge(b.type)]">{{ b.type || 'EAN-13' }}</span></td>
            <td class="px-6 py-4 text-right">
              <button @click="printBarcode(b)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"><PrinterIcon class="h-4 w-4" /></button>
              <button @click="editBarcode(b)" class="p-1.5 text-gray-600 hover:bg-gray-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(b.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="barcodes.length === 0" class="p-12 text-center">
        <QrCodeIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Barkod bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Barkodu Düzenle' : 'Yeni Barkod' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Barkod Numarası *</label><input v-model="form.barcode" required class="w-full rounded-lg border-gray-300 font-mono text-lg tracking-wider" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Tür</label>
            <select v-model="form.type" class="w-full rounded-lg border-gray-300">
              <option value="EAN-13">EAN-13</option>
              <option value="EAN-8">EAN-8</option>
              <option value="UPC-A">UPC-A</option>
              <option value="CODE-128">CODE-128</option>
              <option value="QR">QR Code</option>
            </select>
          </div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, QrCodeIcon, CubeIcon, CheckCircleIcon, PrinterIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useProductBarcodeStore } from '@/stores/productbarcode'

const store = useProductBarcodeStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ barcode: '', type: 'EAN-13' })
const barcodes = ref<any[]>([])

const uniqueProducts = computed(() => new Set(barcodes.value.filter(b => b.product_id).map(b => b.product_id)).size)
const ean13Count = computed(() => barcodes.value.filter(b => !b.type || b.type === 'EAN-13').length)
const getTypeBadge = (t: string) => ({ 'EAN-13': 'bg-green-100 text-green-800', 'EAN-8': 'bg-blue-100 text-blue-800', 'UPC-A': 'bg-purple-100 text-purple-800', 'CODE-128': 'bg-orange-100 text-orange-800', 'QR': 'bg-gray-100 text-gray-800' }[t] || 'bg-gray-100 text-gray-800')

const openCreateModal = () => { form.value = { barcode: '', type: 'EAN-13' }; isEdit.value = false; showModal.value = true }
const editBarcode = (b: any) => { form.value = { ...b }; isEdit.value = true; editingId.value = b.id; showModal.value = true }
const printBarcode = (b: any) => { alert(`${b.barcode} yazdırılıyor...`) }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); barcodes.value = r?.data || [] }
onMounted(() => { loadData() })
</script>