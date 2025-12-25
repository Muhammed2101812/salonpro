<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Müşteri Etiketleri</h1>
        <p class="mt-2 text-sm text-gray-600">Müşterileri kategorize etmek için etiketleri yönetin</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium">
        <PlusIcon class="h-5 w-5 mr-2" />Yeni Etiket
      </button>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-amber-100"><TagIcon class="h-6 w-6 text-amber-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Toplam Etiket</p><p class="text-2xl font-bold">{{ tags.length }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100"><UsersIcon class="h-6 w-6 text-blue-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">Etiketli Müşteri</p><p class="text-2xl font-bold text-blue-600">{{ totalCustomers }}</p></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100"><StarIcon class="h-6 w-6 text-green-600" /></div>
          <div class="ml-4"><p class="text-sm text-gray-500">En Popüler</p><p class="text-2xl font-bold text-green-600">{{ mostPopularTag }}</p></div>
        </div>
      </div>
    </div>

    <!-- Etiket Kartları -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
      <div v-for="t in tags" :key="t.id" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-lg transition-shadow text-center group">
        <div :class="['h-16 w-16 rounded-full mx-auto mb-3 flex items-center justify-center', 'bg-' + (t.color || 'amber') + '-100']">
          <TagIcon :class="['h-8 w-8', 'text-' + (t.color || 'amber') + '-600']" />
        </div>
        <h3 class="font-semibold text-gray-900 mb-1">{{ t.name }}</h3>
        <p class="text-sm text-gray-500 mb-3">{{ t.customers_count || 0 }} müşteri</p>
        <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
          <button @click="editTag(t)" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg"><PencilIcon class="h-4 w-4" /></button>
          <button @click="handleDelete(t.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"><TrashIcon class="h-4 w-4" /></button>
        </div>
      </div>
      <div v-if="tags.length === 0" class="col-span-full text-center py-12">
        <TagIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" /><p class="text-gray-500">Etiket bulunamadı</p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-md w-full">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold">{{ isEdit ? 'Etiketi Düzenle' : 'Yeni Etiket' }}</h2>
          <button @click="showModal = false"><XMarkIcon class="h-6 w-6 text-gray-400" /></button>
        </div>
        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Etiket Adı *</label><input v-model="form.name" required class="w-full rounded-lg border-gray-300" /></div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Renk</label>
            <div class="flex gap-2">
              <button v-for="c in colors" :key="c" type="button" @click="form.color = c" :class="['w-8 h-8 rounded-full', 'bg-' + c + '-500', form.color === c ? 'ring-2 ring-offset-2 ring-' + c + '-500' : '']"></button>
            </div>
          </div>
          <div><label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label><textarea v-model="form.description" rows="2" class="w-full rounded-lg border-gray-300"></textarea></div>
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
import { PlusIcon, TagIcon, UsersIcon, StarIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useCustomerTagStore } from '@/stores/customertag'

const store = useCustomerTagStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const form = ref({ name: '', color: 'amber', description: '' })
const tags = ref<any[]>([])
const colors = ['red', 'orange', 'amber', 'green', 'blue', 'indigo', 'purple', 'pink']

const totalCustomers = computed(() => tags.value.reduce((s, t) => s + (t.customers_count || 0), 0))
const mostPopularTag = computed(() => { const sorted = [...tags.value].sort((a, b) => (b.customers_count || 0) - (a.customers_count || 0)); return sorted[0]?.name || '-' })

const openCreateModal = () => { form.value = { name: '', color: 'amber', description: '' }; isEdit.value = false; showModal.value = true }
const editTag = (t: any) => { form.value = { ...t }; isEdit.value = true; editingId.value = t.id; showModal.value = true }
const handleSubmit = async () => { if (isEdit.value && editingId.value) await store.update(editingId.value, form.value); else await store.create(form.value); showModal.value = false; await loadData() }
const handleDelete = async (id: string) => { if (confirm('Silmek istediğinizden emin misiniz?')) { await store.delete(id); await loadData() } }
const loadData = async () => { const r = await store.fetchAll({}); tags.value = r?.data || [] }
onMounted(() => { loadData() })
</script>