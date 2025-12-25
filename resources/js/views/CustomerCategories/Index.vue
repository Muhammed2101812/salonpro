<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Müşteri Kategorileri</h1>
        <p class="mt-2 text-sm text-gray-600">Müşteri kategorilerini yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />
        Yeni Kategori
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-emerald-100"><TagIcon class="h-6 w-6 text-emerald-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Kategori</p><p class="text-2xl font-bold">{{ categories.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><UserGroupIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Müşteri</p><p class="text-2xl font-bold">{{ totalCustomers }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><ChartBarIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Ort. Müşteri</p><p class="text-2xl font-bold">{{ avgCustomers }}</p></div>
        </div>
      </div>
    </div>

    <!-- Kategori Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="cat in categories" :key="cat.id" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-lg transition-shadow">
        <div class="flex items-start justify-between mb-3">
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg flex items-center justify-center" :style="{ backgroundColor: cat.color || '#10b981' }">
              <TagIcon class="h-5 w-5 text-white" />
            </div>
            <div><h3 class="font-semibold text-gray-900">{{ cat.name }}</h3><p class="text-xs text-gray-500">{{ cat.description || 'Açıklama yok' }}</p></div>
          </div>
        </div>
        <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg mb-4">
          <span class="text-sm text-emerald-700">Müşteri Sayısı</span>
          <span class="text-lg font-bold text-emerald-600">{{ cat.customer_count || 0 }}</span>
        </div>
        <div class="flex justify-end gap-2">
          <button @click="editCategory(cat)" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
          <button @click="handleDelete(cat.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
        </div>
      </div>
      <div v-if="categories.length === 0" class="col-span-full text-center py-12">
        <TagIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500">Kategori bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Düzenle' : 'Yeni Kategori' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Ad *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label><textarea v-model="form.description" rows="2" class="w-full rounded-lg border-gray-300"></textarea></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Renk</label>
            <div class="flex gap-2">
              <button v-for="c in colors" :key="c" type="button" @click="form.color = c" :class="['h-8 w-8 rounded-full border-2', form.color === c ? 'border-gray-900 scale-110' : 'border-transparent']" :style="{ backgroundColor: c }"></button>
            </div>
          </div>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, TagIcon, UserGroupIcon, ChartBarIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useCustomerCategoryStore } from '@/stores/customercategory'

const store = useCustomerCategoryStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const colors = ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444', '#ec4899']
const form = ref({ name: '', description: '', color: '#10b981' })
const categories = ref<any[]>([])

const totalCustomers = computed(() => categories.value.reduce((s, c) => s + (c.customer_count || 0), 0))
const avgCustomers = computed(() => categories.value.length ? Math.round(totalCustomers.value / categories.value.length) : 0)

const openCreateModal = () => { form.value = { name: '', description: '', color: '#10b981' }; isEdit.value = false; showModal.value = true }
const editCategory = (cat: any) => { form.value = { ...cat }; isEdit.value = true; editingId.value = cat.id; showModal.value = true }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); categories.value = r?.data || [] }
onMounted(() => { loadData() })
</script>