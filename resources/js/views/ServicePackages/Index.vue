<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Hizmet Paketleri</h1>
        <p class="mt-2 text-sm text-gray-600">Hizmetlerinizi paket halinde sunun ve indirim sağlayın</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-fuchsia-600 hover:bg-fuchsia-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Paket
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-fuchsia-100"><CubeIcon class="h-6 w-6 text-fuchsia-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Paket</p><p class="text-2xl font-bold">{{ packages.length }}</p></div>
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
          <div class="p-3 rounded-full bg-blue-100"><ShoppingCartIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Satılan</p><p class="text-2xl font-bold text-blue-600">{{ totalSold }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100"><PercentBadgeIcon class="h-6 w-6 text-yellow-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Ort. İndirim</p><p class="text-2xl font-bold text-yellow-600">%{{ avgDiscount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Paket Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="p in packages" :key="p.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="bg-gradient-to-r from-fuchsia-500 to-purple-600 p-4">
          <div class="flex items-center justify-between">
            <h3 class="font-bold text-white text-lg">{{ p.name }}</h3>
            <span v-if="p.discount_percent" class="px-2 py-1 bg-white/20 text-white text-xs rounded-full font-medium">%{{ p.discount_percent }} İndirim</span>
          </div>
        </div>
        <div class="p-5">
          <div class="flex items-baseline gap-2 mb-4">
            <span class="text-3xl font-bold text-gray-900">{{ formatCurrency(p.price || 0) }}</span>
            <span v-if="p.original_price" class="text-sm text-gray-400 line-through">{{ formatCurrency(p.original_price) }}</span>
          </div>
          <div class="mb-4">
            <p class="text-xs text-gray-500 mb-2">Dahil Hizmetler:</p>
            <div class="flex flex-wrap gap-1">
              <span v-for="s in (p.services || []).slice(0, 3)" :key="s.id" class="px-2 py-1 text-xs bg-fuchsia-50 text-fuchsia-700 rounded">{{ s.name }}</span>
              <span v-if="(p.services?.length || 0) > 3" class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">+{{ p.services.length - 3 }}</span>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-2 mb-4">
            <div class="p-2 bg-blue-50 rounded text-center">
              <p class="text-lg font-bold text-blue-600">{{ p.sold_count || 0 }}</p>
              <p class="text-xs text-blue-600">Satılan</p>
            </div>
            <div class="p-2 bg-green-50 rounded text-center">
              <p class="text-lg font-bold text-green-600">{{ p.duration_days || 30 }} gün</p>
              <p class="text-xs text-green-600">Geçerlilik</p>
            </div>
          </div>
          <div class="flex justify-between items-center pt-3 border-t border-gray-100">
            <span :class="['text-xs font-medium', p.is_active ? 'text-green-600' : 'text-gray-400']">{{ p.is_active ? 'Aktif' : 'Pasif' }}</span>
            <div class="flex gap-2">
              <button @click="editPackage(p)" class="p-1.5 text-fuchsia-600 hover:bg-fuchsia-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(p.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="packages.length === 0" class="col-span-full text-center py-12">
        <CubeIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Paket bulunamadı</p>
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
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Fiyat *</label><input v-model.number="form.price" type="number" step="0.01" required class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">İndirim %</label><input v-model.number="form.discount_percent" type="number" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Geçerlilik (gün)</label><input v-model.number="form.duration_days" type="number" class="w-full rounded-lg border-gray-300" /></div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-fuchsia-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, CubeIcon, CheckCircleIcon, ShoppingCartIcon, PercentBadgeIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useServicePackageStore } from '@/stores/servicepackage'

const store = useServicePackageStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', price: 0, discount_percent: 0, duration_days: 30, is_active: true })
const packages = ref<any[]>([])

const activeCount = computed(() => packages.value.filter(p => p.is_active).length)
const totalSold = computed(() => packages.value.reduce((s, p) => s + (p.sold_count || 0), 0))
const avgDiscount = computed(() => { const d = packages.value.filter(p => p.discount_percent); return d.length ? Math.round(d.reduce((s, p) => s + p.discount_percent, 0) / d.length) : 0 })
const formatCurrency = (n: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(n)

const openCreateModal = () => { form.value = { name: '', price: 0, discount_percent: 0, duration_days: 30, is_active: true }; isEdit.value = false; showModal.value = true }
const editPackage = (p: any) => { form.value = { ...p }; isEdit.value = true; editingId.value = p.id; showModal.value = true }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); packages.value = r?.data || [] }
onMounted(() => { loadData() })
</script>