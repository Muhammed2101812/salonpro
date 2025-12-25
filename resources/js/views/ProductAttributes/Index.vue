<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Ürün Özellikleri</h1>
        <p class="mt-2 text-sm text-gray-600">Ürün özellik tanımlarını yönetin (renk, beden, malzeme vb.)</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Özellik
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-amber-100"><AdjustmentsHorizontalIcon class="h-6 w-6 text-amber-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Özellik</p><p class="text-2xl font-bold">{{ attributes.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><ListBulletIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Değer Sayısı</p><p class="text-2xl font-bold text-blue-600">{{ totalValues }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><CheckCircleIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Aktif</p><p class="text-2xl font-bold text-green-600">{{ activeCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Özellik Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="a in attributes" :key="a.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="h-2 bg-amber-500"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="h-12 w-12 rounded-lg bg-amber-100 flex items-center justify-center">
                <AdjustmentsHorizontalIcon class="h-6 w-6 text-amber-600" />
              </div>
              <div>
                <h3 class="font-semibold text-gray-900">{{ a.name }}</h3>
                <span class="text-xs text-gray-500">{{ getTypeLabel(a.type) }}</span>
              </div>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', a.is_required ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600']">{{ a.is_required ? 'Zorunlu' : 'Opsiyonel' }}</span>
          </div>
          <div v-if="a.values?.length" class="flex flex-wrap gap-1 mb-4">
            <span v-for="(v, i) in a.values.slice(0, 5)" :key="i" class="px-2 py-1 text-xs bg-gray-100 rounded">{{ v }}</span>
            <span v-if="a.values.length > 5" class="px-2 py-1 text-xs text-gray-500">+{{ a.values.length - 5 }}</span>
          </div>
          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button @click="editAttribute(a)" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
            <button @click="handleDelete(a.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
          </div>
        </div>
      </div>
      <div v-if="attributes.length === 0" class="col-span-full text-center py-12">
        <AdjustmentsHorizontalIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Özellik bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Özelliği Düzenle' : 'Yeni Özellik' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Özellik Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" placeholder="Renk, Beden, Malzeme" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Tür</label>
            <select v-model="form.type" class="w-full rounded-lg border-gray-300">
              <option value="select">Seçim Listesi</option>
              <option value="text">Metin</option>
              <option value="number">Sayı</option>
              <option value="color">Renk</option>
            </select>
          </div>
          <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_required" class="rounded border-gray-300 text-amber-600" /><span class="text-sm">Zorunlu alan</span></label>
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
import { PlusIcon, AdjustmentsHorizontalIcon, ListBulletIcon, CheckCircleIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useProductAttributeStore } from '@/stores/productattribute'

const store = useProductAttributeStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', type: 'select', is_required: false })
const attributes = ref<any[]>([])

const totalValues = computed(() => attributes.value.reduce((s, a) => s + (a.values?.length || 0), 0))
const activeCount = computed(() => attributes.value.filter(a => a.is_active !== false).length)
const getTypeLabel = (t: string) => ({ select: 'Seçim Listesi', text: 'Metin', number: 'Sayı', color: 'Renk' }[t] || t)

const openCreateModal = () => { form.value = { name: '', type: 'select', is_required: false }; isEdit.value = false; showModal.value = true }
const editAttribute = (a: any) => { form.value = { ...a }; isEdit.value = true; editingId.value = a.id; showModal.value = true }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); attributes.value = r?.data || [] }
onMounted(() => { loadData() })
</script>