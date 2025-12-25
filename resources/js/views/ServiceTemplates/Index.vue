<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Hizmet Şablonları</h1>
        <p class="mt-2 text-sm text-gray-600">Hızlı hizmet oluşturmak için şablonları yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Şablon
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><DocumentDuplicateIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Şablon</p><p class="text-2xl font-bold">{{ templates.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><SparklesIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Kullanım</p><p class="text-2xl font-bold text-green-600">{{ totalUsage }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><TagIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Kategori</p><p class="text-2xl font-bold text-purple-600">{{ uniqueCategories }}</p></div>
        </div>
      </div>
    </div>

    <!-- Şablon Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="t in templates" :key="t.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow group">
        <div :class="['h-2', getCategoryColor(t.category)]"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center">
                <DocumentDuplicateIcon class="h-6 w-6 text-blue-600" />
              </div>
              <div>
                <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition">{{ t.name }}</h3>
                <span class="text-xs text-gray-500">{{ t.duration || 0 }} dk</span>
              </div>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', getCategoryBadge(t.category)]">{{ t.category || 'Genel' }}</span>
          </div>
          <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ t.description || 'Açıklama yok' }}</p>
          <div class="bg-gray-50 rounded-lg p-3 mb-4 space-y-2">
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-500">Fiyat</span>
              <span class="font-bold text-gray-900">{{ formatCurrency(t.price) }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-500">Kullanım</span>
              <span class="font-medium text-blue-600">{{ t.usage_count || 0 }}</span>
            </div>
          </div>
          <div class="flex justify-between items-center pt-3 border-t border-gray-100">
            <button @click="useTemplate(t)" class="text-sm font-medium text-blue-600 hover:text-blue-700">Kullan</button>
            <div class="flex gap-2">
              <button @click="editTemplate(t)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
              <button @click="handleDelete(t.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="templates.length === 0" class="col-span-full text-center py-12">
        <DocumentDuplicateIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Şablon bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Şablonu Düzenle' : 'Yeni Şablon' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Şablon Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label><textarea v-model="form.description" rows="2" class="w-full rounded-lg border-gray-300"></textarea></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label><input v-model="form.category" class="w-full rounded-lg border-gray-300" placeholder="Saç, Cilt, Masaj vb." /></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Fiyat (₺)</label><input v-model.number="form.price" type="number" class="w-full rounded-lg border-gray-300" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Süre (dk)</label><input v-model.number="form.duration" type="number" class="w-full rounded-lg border-gray-300" /></div>
          </div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, DocumentDuplicateIcon, SparklesIcon, TagIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useServiceTemplateStore } from '@/stores/servicetemplate'

const store = useServiceTemplateStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', description: '', category: '', price: 0, duration: 0 })
const templates = ref<any[]>([])

const totalUsage = computed(() => templates.value.reduce((s, t) => s + (t.usage_count || 0), 0))
const uniqueCategories = computed(() => new Set(templates.value.filter(t => t.category).map(t => t.category)).size)
const formatCurrency = (a: number) => new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(a || 0)
const getCategoryColor = (c: string) => ({ sac: 'bg-pink-500', cilt: 'bg-purple-500', masaj: 'bg-blue-500', tirnak: 'bg-red-500' }[c?.toLowerCase()] || 'bg-blue-500')
const getCategoryBadge = (c: string) => ({ sac: 'bg-pink-100 text-pink-800', cilt: 'bg-purple-100 text-purple-800', masaj: 'bg-blue-100 text-blue-800', tirnak: 'bg-red-100 text-red-800' }[c?.toLowerCase()] || 'bg-gray-100 text-gray-800')

const openCreateModal = () => { form.value = { name: '', description: '', category: '', price: 0, duration: 0 }; isEdit.value = false; showModal.value = true }
const editTemplate = (t: any) => { form.value = { ...t }; isEdit.value = true; editingId.value = t.id; showModal.value = true }
const useTemplate = (t: any) => { alert(`"${t.name}" şablonu ile yeni hizmet oluşturuluyor...`) }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); templates.value = r?.data || [] }
onMounted(() => { loadData() })
</script>