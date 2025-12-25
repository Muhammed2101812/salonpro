<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Ürün Görselleri</h1>
        <p class="mt-2 text-sm text-gray-600">Ürün görsellerini yönetin</p>
      </div>
      <button @click="openUploadModal" class="inline-flex items-center px-4 py-2 bg-fuchsia-600 hover:bg-fuchsia-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Görsel Yükle
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-fuchsia-100"><PhotoIcon class="h-6 w-6 text-fuchsia-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Görsel</p><p class="text-2xl font-bold">{{ images.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><CubeIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Ürün</p><p class="text-2xl font-bold text-blue-600">{{ uniqueProducts }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><ServerIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Boyut</p><p class="text-2xl font-bold text-green-600">{{ formatSize(totalSize) }}</p></div>
        </div>
      </div>
    </div>

    <!-- Görsel Galerisi -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
      <div v-for="img in images" :key="img.id" class="group relative bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="aspect-square bg-gray-100">
          <img :src="img.url || img.path || '/placeholder.jpg'" :alt="img.alt || 'Ürün'" class="w-full h-full object-cover" />
        </div>
        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
          <div class="flex gap-2">
            <button @click="viewImage(img)" class="p-2 bg-white rounded-lg hover:bg-gray-100"><EyeIcon class="h-5 w-5 text-gray-700" /></button>
            <button @click="handleDelete(img.id)" class="p-2 bg-white rounded-lg hover:bg-gray-100"><TrashIcon class="h-5 w-5 text-red-600" /></button>
          </div>
        </div>
        <div v-if="img.is_primary" class="absolute top-2 left-2">
          <span class="px-2 py-1 text-xs rounded bg-fuchsia-500 text-white font-medium">Ana</span>
        </div>
        <div class="p-2">
          <p class="text-xs text-gray-600 truncate">{{ img.product?.name || 'Ürün' }}</p>
        </div>
      </div>
      <div v-if="images.length === 0" class="col-span-full text-center py-12">
        <PhotoIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Görsel bulunamadı</p>
      </div>
    </div>

    <!-- Yükleme Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">Görsel Yükle</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleUpload" class="p-6 space-y-4">
          <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
            <PhotoIcon class="h-12 w-12 text-gray-400 mx-auto mb-4" />
            <p class="text-sm text-gray-500 mb-2">Görsel sürükleyin veya seçin</p>
            <input type="file" accept="image/*" class="hidden" id="file-input" />
            <label for="file-input" class="inline-flex items-center px-4 py-2 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200">Dosya Seç</label>
          </div>
          <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_primary" class="rounded border-gray-300 text-fuchsia-600" /><span class="text-sm">Ana görsel olarak ayarla</span></label>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-fuchsia-600 text-white rounded-lg">Yükle</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, PhotoIcon, CubeIcon, ServerIcon, EyeIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useProductImageStore } from '@/stores/productimage'

const store = useProductImageStore()
const showModal = ref(false)
const form = ref({ is_primary: false })
const images = ref<any[]>([])

const uniqueProducts = computed(() => new Set(images.value.filter(i => i.product_id).map(i => i.product_id)).size)
const totalSize = computed(() => images.value.reduce((s, i) => s + (i.size || 0), 0))
const formatSize = (bytes: number) => { if (!bytes) return '0 KB'; const k = 1024; const sizes = ['B', 'KB', 'MB', 'GB']; const i = Math.floor(Math.log(bytes) / Math.log(k)); return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i] }

const openUploadModal = () => { form.value = { is_primary: false }; showModal.value = true }
const viewImage = (img: any) => { window.open(img.url || img.path, '_blank') }
const handleUpload = async () => { alert('Görsel yükleniyor...'); showModal.value = false }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); images.value = r?.data || [] }
onMounted(() => { loadData() })
</script>