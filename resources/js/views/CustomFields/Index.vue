<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Özel Alanlar</h1>
        <p class="mt-2 text-sm text-gray-600">Dinamik özel alan tanımlarını yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Alan
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-teal-100"><RectangleStackIcon class="h-6 w-6 text-teal-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Alan</p><p class="text-2xl font-bold">{{ fields.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><DocumentTextIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Metin</p><p class="text-2xl font-bold text-blue-600">{{ textCount }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100"><ListBulletIcon class="h-6 w-6 text-purple-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Seçim</p><p class="text-2xl font-bold text-purple-600">{{ selectCount }}</p></div>
        </div>
      </div>
    </div>

    <!-- Alan Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="f in fields" :key="f.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
        <div :class="['h-2', getTypeBg(f.type)]"></div>
        <div class="p-5">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div :class="['h-10 w-10 rounded-lg flex items-center justify-center', getTypeIconBg(f.type)]">
                <component :is="getTypeIcon(f.type)" class="h-5 w-5 text-white" />
              </div>
              <div>
                <h3 class="font-semibold text-gray-900">{{ f.label || f.name }}</h3>
                <code class="text-xs text-gray-500">{{ f.key || f.name }}</code>
              </div>
            </div>
            <span :class="['px-2 py-1 text-xs rounded-full font-medium', f.is_required ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600']">{{ f.is_required ? 'Zorunlu' : 'Opsiyonel' }}</span>
          </div>
          <div class="bg-gray-50 rounded-lg p-3 mb-4 space-y-2">
            <div class="flex justify-between text-sm"><span class="text-gray-500">Tür</span><span class="font-medium text-gray-900">{{ getTypeLabel(f.type) }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Varlık</span><span class="text-gray-700">{{ f.entity || 'Genel' }}</span></div>
          </div>
          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button @click="editField(f)" class="p-1.5 text-teal-600 hover:bg-teal-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
            <button @click="handleDelete(f.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
          </div>
        </div>
      </div>
      <div v-if="fields.length === 0" class="col-span-full text-center py-12">
        <RectangleStackIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Özel alan bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Alanı Düzenle' : 'Yeni Alan' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Etiket *</label><input v-model="form.label" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Anahtar *</label><input v-model="form.key" required class="w-full rounded-lg border-gray-300 font-mono" placeholder="custom_field_name" /></div>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Tür</label>
              <select v-model="form.type" class="w-full rounded-lg border-gray-300">
                <option value="text">Metin</option>
                <option value="number">Sayı</option>
                <option value="date">Tarih</option>
                <option value="select">Seçim</option>
                <option value="checkbox">Onay Kutusu</option>
              </select>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Varlık</label>
              <select v-model="form.entity" class="w-full rounded-lg border-gray-300">
                <option value="customer">Müşteri</option>
                <option value="product">Ürün</option>
                <option value="service">Hizmet</option>
                <option value="appointment">Randevu</option>
              </select>
            </div>
          </div>
          <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_required" class="rounded border-gray-300 text-teal-600" /><span class="text-sm">Zorunlu alan</span></label>
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-100 rounded-lg">İptal</button>
            <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-lg">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, markRaw } from 'vue'
import { PlusIcon, RectangleStackIcon, DocumentTextIcon, ListBulletIcon, HashtagIcon, CalendarIcon, CheckIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useCustomFieldStore } from '@/stores/customfield'

const store = useCustomFieldStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ label: '', key: '', type: 'text', entity: 'customer', is_required: false })
const fields = ref<any[]>([])

const textCount = computed(() => fields.value.filter(f => f.type === 'text').length)
const selectCount = computed(() => fields.value.filter(f => f.type === 'select').length)
const getTypeLabel = (t: string) => ({ text: 'Metin', number: 'Sayı', date: 'Tarih', select: 'Seçim', checkbox: 'Onay Kutusu' }[t] || t)
const getTypeBg = (t: string) => ({ text: 'bg-blue-500', number: 'bg-green-500', date: 'bg-orange-500', select: 'bg-purple-500', checkbox: 'bg-teal-500' }[t] || 'bg-gray-500')
const getTypeIconBg = (t: string) => ({ text: 'bg-blue-500', number: 'bg-green-500', date: 'bg-orange-500', select: 'bg-purple-500', checkbox: 'bg-teal-500' }[t] || 'bg-gray-500')
const getTypeIcon = (t: string) => { const icons: Record<string, any> = { text: markRaw(DocumentTextIcon), number: markRaw(HashtagIcon), date: markRaw(CalendarIcon), select: markRaw(ListBulletIcon), checkbox: markRaw(CheckIcon) }; return icons[t] || markRaw(RectangleStackIcon) }

const openCreateModal = () => { form.value = { label: '', key: '', type: 'text', entity: 'customer', is_required: false }; isEdit.value = false; showModal.value = true }
const editField = (f: any) => { form.value = { ...f }; isEdit.value = true; editingId.value = f.id; showModal.value = true }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); fields.value = r?.data || [] }
onMounted(() => { loadData() })
</script>