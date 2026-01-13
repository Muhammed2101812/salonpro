<template>
  <div class="space-y-6">
    <!-- Başlık -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Müşteri Kategorileri</h1>
        <p class="mt-2 text-sm text-gray-600">Müşteri kategorilerinizi oluşturun ve yönetin</p>
      </div>
      <Button variant="success" @click="openCreateModal" :icon="PlusIcon" label="Yeni Kategori" />
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <Card class="p-5">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-emerald-100 ring-4 ring-emerald-50">
            <TagIcon class="h-6 w-6 text-emerald-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Kategori</p>
            <p class="text-2xl font-bold text-gray-900">{{ categories.length }}</p>
          </div>
        </div>
      </Card>
      <Card class="p-5">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100 ring-4 ring-blue-50">
            <UserGroupIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Toplam Müşteri</p>
            <p class="text-2xl font-bold text-gray-900">{{ totalCustomers }}</p>
          </div>
        </div>
      </Card>
      <Card class="p-5">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-purple-100 ring-4 ring-purple-50">
            <ChartBarIcon class="h-6 w-6 text-purple-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-500">Ort. Müşteri</p>
            <p class="text-2xl font-bold text-gray-900">{{ avgCustomers }}</p>
          </div>
        </div>
      </Card>
    </div>

    <!-- Kategori Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <Card
        v-for="cat in categories"
        :key="cat.id"
        class="p-5 hover:shadow-md transition-shadow group border-gray-100"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-4">
            <div
              class="h-12 w-12 rounded-xl flex items-center justify-center shadow-sm transition-transform group-hover:scale-105"
              :style="{ backgroundColor: cat.color || '#10b981' }"
            >
              <TagIcon class="h-6 w-6 text-white" />
            </div>
            <div>
              <h3 class="font-bold text-gray-900 text-lg">{{ cat.name }}</h3>
              <p class="text-sm text-gray-500 line-clamp-1">{{ cat.description || 'Açıklama yok' }}</p>
            </div>
          </div>
        </div>
        
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-4 border border-gray-100">
          <span class="text-sm font-medium text-gray-600">Müşteri Sayısı</span>
          <span class="text-lg font-bold text-primary">{{ cat.customer_count || 0 }}</span>
        </div>

        <div class="flex justify-end gap-2 pt-4 border-t border-gray-50">
          <Button variant="ghost" size="sm" @click="editCategory(cat)">
            <PencilIcon class="h-4 w-4 text-emerald-600" />
          </Button>
          <Button variant="ghost" size="sm" @click="handleDelete(cat.id)">
            <TrashIcon class="h-4 w-4 text-red-600" />
          </Button>
        </div>
      </Card>

      <div v-if="categories.length === 0" class="col-span-full bg-white rounded-xl border border-dashed border-gray-300 py-16 text-center">
        <TagIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-500 font-medium">Kategori bulunamadı</p>
        <p class="text-sm text-gray-400 mt-1">Hemen bir kategori ekleyerek başlayın.</p>
        <Button variant="outline" size="sm" class="mt-4" @click="openCreateModal" label="Yeni Kategori" />
      </div>
    </div>

    <!-- Modal -->
    <Modal
      v-model="showModal"
      :title="isEdit ? 'Kategoriyi Düzenle' : 'Yeni Kategori'"
    >
      <form @submit.prevent="handleSubmit" class="space-y-5">
        <Input
          v-model="form.name"
          label="Kategori Adı"
          placeholder="Örn: VIP Müşteriler"
          required
        />
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Açıklama</label>
          <textarea
            v-model="form.description"
            rows="3"
            placeholder="Kategori hakkında kısa bir açıklama..."
            class="block w-full rounded-lg border border-gray-300 shadow-sm transition-all focus:outline-none text-sm px-3 py-2 placeholder-gray-400 focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white"
          ></textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Renk</label>
          <div class="flex flex-wrap gap-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
            <button
              v-for="c in colors"
              :key="c"
              type="button"
              @click="form.color = c"
              :class="[
                'h-9 w-9 rounded-full transition-all ring-offset-2',
                form.color === c ? 'ring-2 ring-gray-900 scale-110 shadow-sm' : 'hover:scale-105'
              ]"
              :style="{ backgroundColor: c }"
            ></button>
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
          <Button variant="secondary" @click="showModal = false" label="İptal" />
          <Button type="submit" variant="success" label="Kaydet" />
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, TagIcon, UserGroupIcon, ChartBarIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useCustomerCategoryStore } from '@/stores/customercategory'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Card from '@/components/ui/Card.vue'
import Modal from '@/components/ui/Modal.vue'

const store = useCustomerCategoryStore()
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<string | null>(null)
const colors = ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444', '#ec4899', '#06b6d4', '#f43f5e', '#84cc16']
const form = ref({ name: '', description: '', color: '#10b981' })
const categories = ref<any[]>([])

const totalCustomers = computed(() => categories.value.reduce((s, c) => s + (c.customer_count || 0), 0))
const avgCustomers = computed(() => categories.value.length ? Math.round(totalCustomers.value / categories.value.length) : 0)

const openCreateModal = () => {
  form.value = { name: '', description: '', color: '#10b981' }
  isEdit.value = false
  showModal.value = true
}

const editCategory = (cat: any) => {
  form.value = { ...cat }
  isEdit.value = true
  editingId.value = cat.id
  showModal.value = true
}

const handleSubmit = async () => {
  try {
    if (isEdit.value && editingId.value) {
      await store.update(editingId.value, form.value)
    } else {
      await store.create(form.value)
    }
    showModal.value = false
    await loadData()
  } catch (error) {
    console.error('Kategori kaydedilemedi:', error)
  }
}

const handleDelete = async (id: string) => {
  if (confirm('Silmek istediğinizden emin misiniz?')) {
    try {
      await store.delete(id)
      await loadData()
    } catch (error) {
      console.error('Kategori silinemedi:', error)
    }
  }
}

const loadData = async () => {
  const r = await store.fetchAll({})
  categories.value = r?.data || []
}

onMounted(() => {
  loadData()
})
</script>