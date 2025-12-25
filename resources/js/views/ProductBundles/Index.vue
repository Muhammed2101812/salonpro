<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Ürün Paketleri</h1>
        <p class="mt-2 text-sm text-gray-600">Birden fazla ürünü içeren paketleri yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Paket
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-indigo-100"><ArchiveBoxIcon class="h-6 w-6 text-indigo-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Paket</p><p class="text-2xl font-bold">{{ bundles.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><CurrencyDollarIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Değer</p><p class="text-2xl font-bold text-green-600">{{ formatCurrency(totalValue) }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-orange-100"><TagIcon class="h-6 w-6 text-orange-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Ort. İndirim</p><p class="text-2xl font-bold text-orange-600">%{{ avgDiscount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Paket Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="b in bundles" :key="b.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="h-2 bg-indigo-500"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 class="font-semibold text-gray-900">{{ b.name }}</h3>
              <span class="text-xs text-gray-500">{{ b.products?.length || 0 }} ürün</span>
            </div>
            <span v-if="b.discount" class="px-2 py-1 text-xs rounded-full font-medium bg-orange-100 text-orange-800">%{{ b.discount }} indirim</span>
          </div>
          <div class="bg-gray-50 rounded-lg p-3 mb-4 space-y-2">
            <div class="flex justify-between text-sm"><span class="text-gray-500">Normal Fiyat</span><span class="line-through text-gray-400">{{ formatCurrency(b.original_price) }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Paket Fiyatı</span><span class="font-bold text-indigo-600">{{ formatCurrency(b.price) }}</span></div>
          </div>
          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button @click="editBundle(b)" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
            <button @click="handleDelete(b.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
          </div>
        </div>
      </div>
      <div v-if="bundles.length === 0" class="col-span-full text-center py-12">
        <ArchiveBoxIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Paket bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Paketi Düzenle' : 'Yeni Paket' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Paket Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Paket Fiyatı</label><input v-model.number="form.price" type="number" class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">İndirim (%)</label><input v-model.number="form.discount" type="number" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, ArchiveBoxIcon, CurrencyDollarIcon, TagIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useProductBundleStore } from '@/stores/productbundle'

const store = useProductBundleStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', price: 0, discount: 0 })
const bundles = ref<any[]>([])

const totalValue = computed(() => bundles.value.reduce((s, b) => s + (b.price || 0), 0))
const avgDiscount = computed(() => { const discounts = bundles.value.filter(b => b.discount); return discounts.length ? Math.round(discounts.reduce((s, b) => s + b.discount, 0) / discounts.length) : 0 })
const formatCurrency = (a: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(a || 0)

const openCreateModal = () => { form.value = { name: '', price: 0, discount: 0 }; isEdit.value = false; showModal.value = true }
const editBundle = (b: any) => { form.value = { ...b }; isEdit.value = true; editingId.value = b.id; showModal.value = true }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); bundles.value = r?.data || [] }
onMounted(() => { loadData() })
</script>