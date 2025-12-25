<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Hizmet Kategorileri</h1>
        <p class="mt-2 text-sm text-gray-600">Sunduğunuz hizmetleri kategorilere ayırın</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Kategori
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-pink-100"><Squares2X2Icon class="h-6 w-6 text-pink-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Kategori</p><p class="text-2xl font-bold">{{ categories.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><SparklesIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Hizmet</p><p class="text-2xl font-bold text-purple-600">{{ totalServices }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><CheckCircleIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Aktif</p><p class="text-2xl font-bold text-green-600">{{ activeCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Kategori Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="cat in categories" :key="cat.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="h-2" :style="{ backgroundColor: cat.color || '#ec4899' }"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
              <div class="h-12 w-12 rounded-lg flex items-center justify-center" :style="{ backgroundColor: (cat.color || '#ec4899') + '20' }">
                <SparklesIcon class="h-6 w-6" :style="{ color: cat.color || '#ec4899' }" />
              </div>
              <div>
                <h3 class="font-semibold text-gray-900">{{ cat.name }}</h3>
                <p class="text-sm text-gray-500">{{ cat.description || 'Açıklama yok' }}</p>
              </div>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', cat.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600']">
              {{ cat.is_active !== false ? 'Aktif' : 'Pasif' }}
            </span>
          </div>
          <div class="flex items-center justify-between p-3 rounded-lg mb-4" :style="{ backgroundColor: (cat.color || '#ec4899') + '10' }">
            <span class="text-sm" :style="{ color: cat.color || '#ec4899' }">Hizmet Sayısı</span>
            <span class="font-bold" :style="{ color: cat.color || '#ec4899' }">{{ cat.services_count || 0 }}</span>
          </div>
          <div v-if="cat.parent" class="text-xs text-gray-500 mb-3">Üst Kategori: {{ cat.parent?.name }}</div>
          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button @click="editCategory(cat)" class="p-1.5 text-pink-600 hover:bg-pink-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
            <button @click="handleDelete(cat.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
          </div>
        </div>
      </div>
      <div v-if="categories.length === 0" class="col-span-full text-center py-12">
        <Squares2X2Icon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Kategori bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Kategoriyi Düzenle' : 'Yeni Kategori' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Kategori Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label><textarea v-model="form.description" rows="2" class="w-full rounded-lg border-gray-300"></textarea></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Renk</label>
            <div class="flex gap-2">
              <button v-for="c in colors" :key="c" type="button" @click="form.color = c" :class="['h-8 w-8 rounded-full border-2', form.color === c ? 'border-gray-900 scale-110' : 'border-transparent']" :style="{ backgroundColor: c }"></button>
            </div>
          </div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-pink-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, Squares2X2Icon, SparklesIcon, CheckCircleIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useServiceCategoryStore } from '@/stores/servicecategory'

const store = useServiceCategoryStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const colors = ['#ec4899', '#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#84cc16']
const form = ref({ name: '', description: '', color: '#ec4899', is_active: true })
const categories = ref<any[]>([])

const totalServices = computed(() => categories.value.reduce((s, c) => s + (c.services_count || 0), 0))
const activeCount = computed(() => categories.value.filter(c => c.is_active !== false).length)

const openCreateModal = () => { form.value = { name: '', description: '', color: '#ec4899', is_active: true }; isEdit.value = false; showModal.value = true }
const editCategory = (c: any) => { form.value = { ...c }; isEdit.value = true; editingId.value = c.id; showModal.value = true }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); categories.value = r?.data || [] }
onMounted(() => { loadData() })
</script>