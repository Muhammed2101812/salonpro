<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Ürün Varyantları</h1>
        <p class="mt-2 text-sm text-gray-600">Ürün boyut, renk ve diğer varyantlarını yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Varyant
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100"><SwatchIcon class="h-6 w-6 text-yellow-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Varyant</p><p class="text-2xl font-bold">{{ variants.length }}</p></div>
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
          <div class="ml-4"><p class="text-sm text-gray-500">Stokta</p><p class="text-2xl font-bold text-green-600">{{ inStockCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Tablo -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Varyant</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">SKU</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Fiyat</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stok</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlem</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="v in variants" :key="v.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div :class="['h-10 w-10 rounded-lg flex items-center justify-center', getColorBg(v.color)]">
                  <SwatchIcon class="h-5 w-5 text-white" />
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ v.name || formatVariantName(v) }}</div>
                  <div class="text-xs text-gray-500">{{ v.product?.name || '-' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center"><code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ v.sku || '-' }}</code></td>
            <td class="px-6 py-4 text-center text-sm font-bold text-gray-900">{{ formatCurrency(v.price) }}</td>
            <td class="px-6 py-4 text-center"><span :class="['px-2 py-1 text-xs rounded-full font-medium', v.stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">{{ v.stock || 0 }}</span></td>
            <td class="px-6 py-4 text-right">
              <button @click="editVariant(v)" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(v.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="variants.length === 0" class="p-12 text-center">
        <SwatchIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Varyant bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Varyantı Düzenle' : 'Yeni Varyant' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Varyant Adı</label><input v-model="form.name" class="w-full rounded-lg border-gray-300" /></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">SKU</label><input v-model="form.sku" class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Fiyat</label><input v-model.number="form.price" type="number" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Renk</label><input v-model="form.color" class="w-full rounded-lg border-gray-300" placeholder="Kırmızı" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Beden</label><input v-model="form.size" class="w-full rounded-lg border-gray-300" placeholder="M" /></div>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Stok</label><input v-model.number="form.stock" type="number" class="w-full rounded-lg border-gray-300" /></div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-yellow-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, SwatchIcon, CubeIcon, CheckCircleIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useProductVariantStore } from '@/stores/productvariant'

const store = useProductVariantStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', sku: '', price: 0, color: '', size: '', stock: 0 })
const variants = ref<any[]>([])

const uniqueProducts = computed(() => new Set(variants.value.filter(v => v.product_id).map(v => v.product_id)).size)
const inStockCount = computed(() => variants.value.filter(v => v.stock > 0).length)
const formatCurrency = (a: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(a || 0)
const formatVariantName = (v: any) => [v.color, v.size].filter(Boolean).join(' / ') || 'Standart'
const getColorBg = (c: string) => ({ kirmizi: 'bg-red-500', mavi: 'bg-blue-500', yesil: 'bg-green-500', sari: 'bg-yellow-500', siyah: 'bg-gray-900', beyaz: 'bg-gray-100' }[c?.toLowerCase()] || 'bg-yellow-500')

const openCreateModal = () => { form.value = { name: '', sku: '', price: 0, color: '', size: '', stock: 0 }; isEdit.value = false; showModal.value = true }
const editVariant = (v: any) => { form.value = { ...v }; isEdit.value = true; editingId.value = v.id; showModal.value = true }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); variants.value = r?.data || [] }
onMounted(() => { loadData() })
</script>