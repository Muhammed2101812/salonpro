<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">KPI Tanımları</h1>
        <p class="mt-2 text-sm text-gray-600">Anahtar performans göstergelerini tanımlayın</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni KPI
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-indigo-100"><ChartBarSquareIcon class="h-6 w-6 text-indigo-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam KPI</p><p class="text-2xl font-bold">{{ kpis.length }}</p></div>
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
          <div class="p-3 rounded-full bg-purple-100"><TagIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Kategori</p><p class="text-2xl font-bold text-purple-600">{{ uniqueCategories }}</p></div>
        </div>
      </div>
    </div>

    <!-- KPI Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="k in kpis" :key="k.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', getCategoryColor(k.category)]"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 class="font-semibold text-gray-900">{{ k.name }}</h3>
              <code class="text-xs text-gray-500">{{ k.key }}</code>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', getCategoryBadge(k.category)]">{{ k.category || 'Genel' }}</span>
          </div>
          <p class="text-sm text-gray-600 mb-4">{{ k.description || 'Açıklama yok' }}</p>
          <div class="bg-gray-50 rounded-lg p-3 mb-4 space-y-2">
            <div class="flex justify-between text-sm"><span class="text-gray-500">Birim</span><span class="font-medium text-gray-900">{{ k.unit || '-' }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Hedef</span><span class="font-medium text-indigo-600">{{ k.target || '-' }}</span></div>
          </div>
          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button @click="editKpi(k)" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
            <button @click="handleDelete(k.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
          </div>
        </div>
      </div>
      <div v-if="kpis.length === 0" class="col-span-full text-center py-12">
        <ChartBarSquareIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">KPI bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'KPI Düzenle' : 'Yeni KPI' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">İsim *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Anahtar *</label><input v-model="form.key" required class="w-full rounded-lg border-gray-300 font-mono" placeholder="monthly_revenue" /></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label><input v-model="form.category" class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Birim</label><input v-model="form.unit" class="w-full rounded-lg border-gray-300" placeholder="₺, %, adet" /></div>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Hedef</label><input v-model="form.target" class="w-full rounded-lg border-gray-300" /></div>
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
import { PlusIcon, ChartBarSquareIcon, CheckCircleIcon, TagIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useKpiDefinitionStore } from '@/stores/kpidefinition'

const store = useKpiDefinitionStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', key: '', category: '', unit: '', target: '' })
const kpis = ref<any[]>([])

const activeCount = computed(() => kpis.value.filter(k => k.is_active !== false).length)
const uniqueCategories = computed(() => new Set(kpis.value.filter(k => k.category).map(k => k.category)).size)
const getCategoryColor = (c: string) => ({ sales: 'bg-green-500', finance: 'bg-blue-500', customer: 'bg-purple-500', employee: 'bg-orange-500' }[c?.toLowerCase()] || 'bg-indigo-500')
const getCategoryBadge = (c: string) => ({ sales: 'bg-green-100 text-green-800', finance: 'bg-blue-100 text-blue-800', customer: 'bg-purple-100 text-purple-800', employee: 'bg-orange-100 text-orange-800' }[c?.toLowerCase()] || 'bg-gray-100 text-gray-800')

const openCreateModal = () => { form.value = { name: '', key: '', category: '', unit: '', target: '' }; isEdit.value = false; showModal.value = true }
const editKpi = (k: any) => { form.value = { ...k }; isEdit.value = true; editingId.value = k.id; showModal.value = true }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); kpis.value = r?.data || [] }
onMounted(() => { loadData() })
</script>