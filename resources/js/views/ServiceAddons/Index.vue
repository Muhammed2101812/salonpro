<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Hizmet Eklentileri</h1>
        <p class="mt-2 text-sm text-gray-600">Hizmetlere eklenebilecek opsiyonel seçenekleri yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Eklenti
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-rose-100"><PuzzlePieceIcon class="h-6 w-6 text-rose-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Eklenti</p><p class="text-2xl font-bold">{{ addons.length }}</p></div>
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
          <div class="p-3 rounded-full bg-blue-100"><CurrencyDollarIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Ort. Fiyat</p><p class="text-2xl font-bold text-blue-600">{{ formatCurrency(avgPrice) }}</p></div>
        </div>
      </div>
    </div>

    <!-- Eklenti Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="a in addons" :key="a.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="h-2 bg-rose-500"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="h-12 w-12 rounded-lg bg-rose-100 flex items-center justify-center">
                <PuzzlePieceIcon class="h-6 w-6 text-rose-600" />
              </div>
              <div>
                <h3 class="font-semibold text-gray-900">{{ a.name }}</h3>
                <span class="text-xs text-gray-500">{{ a.duration || 0 }} dk</span>
              </div>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', a.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">{{ a.is_active !== false ? 'Aktif' : 'Pasif' }}</span>
          </div>
          <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ a.description || 'Açıklama yok' }}</p>
          <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-4">
            <span class="text-sm text-gray-500">Fiyat</span>
            <span class="text-lg font-bold text-rose-600">{{ formatCurrency(a.price) }}</span>
          </div>
          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button @click="editAddon(a)" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
            <button @click="handleDelete(a.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
          </div>
        </div>
      </div>
      <div v-if="addons.length === 0" class="col-span-full text-center py-12">
        <PuzzlePieceIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Eklenti bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Eklentiyi Düzenle' : 'Yeni Eklenti' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Eklenti Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label><textarea v-model="form.description" rows="2" class="w-full rounded-lg border-gray-300"></textarea></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Fiyat (₺)</label><input v-model.number="form.price" type="number" class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Süre (dk)</label><input v-model.number="form.duration" type="number" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_active" class="rounded border-gray-300 text-rose-600" /><span class="text-sm">Aktif</span></label>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-rose-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, PuzzlePieceIcon, CheckCircleIcon, CurrencyDollarIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useServiceAddonStore } from '@/stores/serviceaddon'

const store = useServiceAddonStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', description: '', price: 0, duration: 0, is_active: true })
const addons = ref<any[]>([])

const activeCount = computed(() => addons.value.filter(a => a.is_active !== false).length)
const avgPrice = computed(() => { if (!addons.value.length) return 0; return addons.value.reduce((s, a) => s + (a.price || 0), 0) / addons.value.length })
const formatCurrency = (a: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(a || 0)

const openCreateModal = () => { form.value = { name: '', description: '', price: 0, duration: 0, is_active: true }; isEdit.value = false; showModal.value = true }
const editAddon = (a: any) => { form.value = { ...a }; isEdit.value = true; editingId.value = a.id; showModal.value = true }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); addons.value = r?.data || [] }
onMounted(() => { loadData() })
</script>