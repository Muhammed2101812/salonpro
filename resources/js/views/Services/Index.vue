<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Hizmetler</h1>
        <p class="mt-2 text-sm text-gray-600">Hizmet kategorileri ve hizmetlerinizi yönetin</p>
      </div>
      <div class="flex gap-3">
        <div v-if="activeTab === 'services'" class="flex rounded-lg border border-gray-200 overflow-hidden bg-white">
            <button
              @click="viewMode = 'grid'"
              :class="[ 'p-2 transition-colors', viewMode === 'grid' ? 'bg-primary text-white' : 'hover:bg-gray-50 text-gray-600' ]"
              title="Kart Görünümü"
            >
              <Squares2X2Icon class="h-5 w-5" />
            </button>
            <button
              @click="viewMode = 'table'"
              :class="[ 'p-2 transition-colors', viewMode === 'table' ? 'bg-primary text-white' : 'hover:bg-gray-50 text-gray-600' ]"
              title="Liste Görünümü"
            >
              <ListBulletIcon class="h-5 w-5" />
            </button>
        </div>
        <Button 
            v-if="activeTab === 'categories'"
            variant="primary" 
            @click="openCreateCategoryModal" 
            :icon="PlusIcon" 
            label="Kategori Ekle"
            class="bg-teal-600 hover:bg-teal-700 text-white"
        />
        <Button 
            v-else
            variant="primary" 
            @click="openCreateServiceModal" 
            :icon="PlusIcon" 
            label="Hizmet Ekle"
            class="bg-teal-600 hover:bg-teal-700 text-white"
        />
      </div>
    </div>

    <!-- Stats -->
    <ServiceStats :stats="stats" />

    <!-- Tabs -->
    <Card class="overflow-hidden">
      <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
          <button
            @click="activeTab = 'categories'"
            :class="[
              activeTab === 'categories'
                ? 'border-teal-500 text-teal-600 bg-teal-50'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'flex-1 sm:flex-none whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors flex items-center justify-center'
            ]"
          >
            <FolderIcon class="h-5 w-5 mr-2 inline" />
            Kategoriler
            <span class="ml-2 bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs">
              {{ serviceStore.categories.length }}
            </span>
          </button>
          <button
            @click="activeTab = 'services'"
            :class="[
              activeTab === 'services'
                ? 'border-teal-500 text-teal-600 bg-teal-50'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'flex-1 sm:flex-none whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors flex items-center justify-center'
            ]"
          >
            <SparklesIcon class="h-5 w-5 mr-2 inline" />
            Hizmetler
            <span class="ml-2 bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs">
              {{ serviceStore.services.length }}
            </span>
          </button>
        </nav>
      </div>

      <!-- Filters (Services Tab Only) -->
      <div v-if="activeTab === 'services'" class="p-4 border-b border-gray-100 flex flex-col lg:flex-row gap-4 justify-between">
           <div class="flex flex-wrap gap-3 items-center w-full lg:w-auto">
                <Input v-model="search" placeholder="Hizmet ara..." class="w-full lg:w-64">
                    <template #prefix>
                        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                    </template>
                </Input>

               <select
                 v-model="filters.categoryId"
                 class="rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm py-2 px-3"
               >
                 <option value="">Tüm Kategoriler</option>
                 <option v-for="cat in serviceStore.categories" :key="cat.id" :value="cat.id">
                   {{ cat.name }}
                 </option>
               </select>

               <div class="flex rounded-lg border border-gray-200 overflow-hidden">
                 <button
                   @click="filters.status = ''"
                   :class="[
                     'px-3 py-2 text-xs font-medium transition-colors',
                     filters.status === '' ? 'bg-teal-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
                   ]"
                 >
                   Tümü
                 </button>
                 <button
                   @click="filters.status = 'active'"
                   :class="[
                     'px-3 py-2 text-xs font-medium transition-colors',
                     filters.status === 'active' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
                   ]"
                 >
                   Aktif
                 </button>
                 <button
                   @click="filters.status = 'inactive'"
                   :class="[
                     'px-3 py-2 text-xs font-medium transition-colors',
                     filters.status === 'inactive' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
                   ]"
                 >
                   Pasif
                 </button>
               </div>
           </div>
      </div>

      <!-- Content -->
      <div v-if="serviceStore.loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-teal-600"></div>
      </div>

      <div v-else class="p-6">
          <!-- Categories Tab -->
          <div v-if="activeTab === 'categories'">
               <div v-if="serviceStore.categories.length === 0" class="text-center py-12 text-gray-500">
                  <FolderIcon class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                  Kategori bulunamadı. İlk kategoriyi ekleyin.
               </div>
               <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div
                    v-for="category in serviceStore.categories"
                    :key="category.id"
                    :class="[
                      'rounded-xl border p-5 transition-all cursor-pointer hover:shadow-md bg-white',
                      category.is_active ? 'border-gray-200' : 'border-gray-100 opacity-75'
                    ]"
                  >
                    <div class="flex items-start justify-between mb-3">
                      <div class="flex items-center gap-3">
                        <div :class="['p-2 rounded-lg', getCategoryColor(category.id)]">
                          <FolderIcon class="h-6 w-6 text-white" />
                        </div>
                        <div>
                          <h4 class="font-semibold text-gray-900">{{ category.name }}</h4>
                          <p class="text-xs text-gray-500">{{ getCategoryServiceCount(category.id) }} hizmet</p>
                        </div>
                      </div>
                      <span
                        :class="[
                          'px-2 py-1 text-xs font-medium rounded-full',
                          category.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                        ]"
                      >
                        {{ category.is_active ? 'Aktif' : 'Pasif' }}
                      </span>
                    </div>
                     <p v-if="category.description" class="text-sm text-gray-600 mb-4 line-clamp-2">
                      {{ category.description }}
                    </p>
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                       <Button variant="ghost" size="sm" @click="openEditCategoryModal(category)">
                          <PencilIcon class="h-4 w-4 text-teal-600" />
                      </Button>
                      <Button variant="ghost" size="sm" @click="handleDeleteCategory(category.id)">
                          <TrashIcon class="h-4 w-4 text-red-600" />
                      </Button>
                    </div>
                  </div>
               </div>
          </div>

          <!-- Services Tab -->
          <div v-else>
               <!-- Grid View -->
               <div v-if="viewMode === 'grid'">
                    <div v-if="filteredServices.length === 0" class="text-center py-12 text-gray-500">
                       <SparklesIcon class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                       Hizmet bulunamadı.
                    </div>
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                         <div
                            v-for="service in filteredServices"
                            :key="service.id"
                            :class="[
                              'rounded-xl border overflow-hidden transition-all hover:shadow-md bg-white',
                              service.is_active ? 'border-gray-200' : 'border-gray-100 opacity-75'
                            ]"
                          >
                            <div :class="['h-2', getCategoryColorByServiceId(service.service_category_id)]"></div>
                            <div class="p-5">
                              <div class="flex items-start justify-between mb-3">
                                <div>
                                  <h4 class="font-semibold text-gray-900">{{ service.name }}</h4>
                                  <p class="text-xs text-gray-500">{{ getCategoryName(service.service_category_id) }}</p>
                                </div>
                                <span
                                  :class="[
                                    'px-2 py-1 text-xs font-medium rounded-full',
                                    service.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                  ]"
                                >
                                  {{ service.is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                              </div>
                               <p v-if="service.description" class="text-sm text-gray-600 mb-4 line-clamp-2">
                                {{ service.description }}
                              </p>
                               <div class="flex items-center justify-between py-3 border-t border-gray-100">
                                <div class="flex items-center gap-4">
                                  <div class="text-center">
                                    <p class="text-lg font-bold text-teal-600">{{ formatCurrency(service.price) }}</p>
                                    <p class="text-xs text-gray-500">Fiyat</p>
                                  </div>
                                  <div class="text-center">
                                    <p class="text-lg font-bold text-gray-900">{{ service.duration_minutes }}</p>
                                    <p class="text-xs text-gray-500">Dakika</p>
                                  </div>
                                </div>
                              </div>
                               <div class="flex items-center justify-end gap-2 pt-3">
                                <Button variant="ghost" size="sm" @click="openEditServiceModal(service)">
                                  <PencilIcon class="h-4 w-4 text-teal-600" />
                                </Button>
                                <Button variant="ghost" size="sm" @click="handleDeleteService(service.id)">
                                  <TrashIcon class="h-4 w-4 text-red-600" />
                                </Button>
                              </div>
                            </div>
                         </div>
                    </div>
               </div>
               
               <!-- Table View -->
               <div v-else>
                   <DataTable
                        :columns="tableColumns"
                        :data="filteredServices"
                    >
                        <template #cell-name="{ row }">
                            <div class="flex items-center gap-3">
                              <div :class="['w-2 h-10 rounded', getCategoryColorByServiceId(row.service_category_id)]"></div>
                              <div>
                                <p class="text-sm font-medium text-gray-900">{{ row.name }}</p>
                                <p v-if="row.description" class="text-xs text-gray-500 truncate max-w-xs">{{ row.description }}</p>
                              </div>
                            </div>
                        </template>
                        <template #cell-category="{ row }">
                            {{ getCategoryName(row.service_category_id) }}
                        </template>
                        <template #cell-price="{ row }">
                            <span class="text-sm font-medium text-teal-600">{{ formatCurrency(row.price) }}</span>
                        </template>
                        <template #cell-duration="{ row }">
                            {{ row.duration_minutes }} dk
                        </template>
                        <template #cell-status="{ row }">
                            <span
                              :class="[
                                'px-2 py-1 text-xs font-medium rounded-full',
                                row.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                              ]"
                            >
                              {{ row.is_active ? 'Aktif' : 'Pasif' }}
                            </span>
                        </template>
                        <template #actions="{ row }">
                            <div class="flex items-center justify-end gap-2">
                                <Button variant="ghost" size="sm" @click="openEditServiceModal(row)">
                                  <PencilIcon class="h-4 w-4 text-teal-600" />
                                </Button>
                                <Button variant="ghost" size="sm" @click="handleDeleteService(row.id)">
                                  <TrashIcon class="h-4 w-4 text-red-600" />
                                </Button>
                            </div>
                        </template>
                   </DataTable>
               </div>
          </div>
      </div>
    </Card>

    <!-- Modals -->
    <CategoryModal
        v-model="showCategoryModal"
        :is-edit="isEditCategory"
        :initial-data="categoryFormData"
        :loading="serviceStore.loading"
        @submit="handleCategorySubmit"
    />
    <ServiceModal
        v-model="showServiceModal"
        :is-edit="isEditService"
        :initial-data="serviceFormData"
        :loading="serviceStore.loading"
        :categories="serviceStore.categories"
        @submit="handleServiceSubmit"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import {
  PlusIcon,
  SparklesIcon,
  FolderIcon,
  MagnifyingGlassIcon,
  PencilIcon,
  TrashIcon,
  ListBulletIcon,
  Squares2X2Icon
} from '@heroicons/vue/24/outline'

import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Card from '@/components/ui/Card.vue'
import DataTable from '@/components/ui/DataTable.vue'
import ServiceStats from '@/components/service/ServiceStats.vue'
import CategoryModal from '@/components/service/CategoryModal.vue'
import ServiceModal from '@/components/service/ServiceModal.vue'

import { useServiceStore } from '@/stores/service'

const serviceStore = useServiceStore()

// State
const activeTab = ref<'categories' | 'services'>('services')
const viewMode = ref<'grid' | 'table'>('grid')
const search = ref('')

const filters = ref({
  categoryId: '',
  status: ''
})

const stats = ref({
  totalServices: 0,
  totalCategories: 0,
  activeServices: 0,
  avgPrice: 0,
  avgDuration: 0
})

// Modals
const showCategoryModal = ref(false)
const isEditCategory = ref(false)
const categoryFormData = ref<any>(null)
const editingCategoryId = ref<string | null>(null)

const showServiceModal = ref(false)
const isEditService = ref(false)
const serviceFormData = ref<any>(null)
const editingServiceId = ref<string | null>(null)

// Category colors
const categoryColors = [
  'bg-pink-500', 'bg-purple-500', 'bg-indigo-500', 'bg-blue-500',
  'bg-cyan-500', 'bg-teal-500', 'bg-green-500', 'bg-yellow-500', 'bg-orange-500'
]

const tableColumns = [
    { key: 'name', label: 'Hizmet' },
    { key: 'category', label: 'Kategori' },
    { key: 'price', label: 'Fiyat' },
    { key: 'duration', label: 'Süre' },
    { key: 'status', label: 'Durum' }
]

// Computed
const filteredServices = computed(() => {
  let result = serviceStore.services

  if (search.value) {
    const searchLower = search.value.toLowerCase()
    result = result.filter(s => s.name?.toLowerCase().includes(searchLower))
  }

  if (filters.value.categoryId) {
    result = result.filter(s => s.service_category_id === filters.value.categoryId)
  }

  if (filters.value.status === 'active') {
    result = result.filter(s => s.is_active)
  } else if (filters.value.status === 'inactive') {
    result = result.filter(s => !s.is_active)
  }

  return result
})

// Helpers
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount || 0)
}

const getCategoryName = (categoryId: string) => {
  const category = serviceStore.categories.find(c => c.id === categoryId)
  return category ? category.name : '-'
}

const getCategoryServiceCount = (categoryId: string) => {
  return serviceStore.services.filter(s => s.service_category_id === categoryId).length
}

const getCategoryColor = (categoryId: string) => {
  const index = serviceStore.categories.findIndex(c => c.id === categoryId)
  return categoryColors[index % categoryColors.length]
}

const getCategoryColorByServiceId = (categoryId: string) => {
  return getCategoryColor(categoryId)
}

const updateStats = () => {
  stats.value.totalServices = serviceStore.services.length
  stats.value.totalCategories = serviceStore.categories.length
  stats.value.activeServices = serviceStore.services.filter(s => s.is_active).length
  
  const prices = serviceStore.services.map(s => Number(s.price) || 0)
  stats.value.avgPrice = prices.length > 0 ? prices.reduce((a, b) => a + b, 0) / prices.length : 0
  
  const durations = serviceStore.services.map(s => s.duration_minutes || 0)
  stats.value.avgDuration = durations.length > 0 ? Math.round(durations.reduce((a, b) => a + b, 0) / durations.length) : 0
}

// Category Actions
const openCreateCategoryModal = () => {
  categoryFormData.value = null
  isEditCategory.value = false
  editingCategoryId.value = null
  showCategoryModal.value = true
}

const openEditCategoryModal = (category: any) => {
  categoryFormData.value = { ...category }
  isEditCategory.value = true
  editingCategoryId.value = category.id
  showCategoryModal.value = true
}

const handleCategorySubmit = async (data: any) => {
  try {
    if (isEditCategory.value && editingCategoryId.value) {
      await serviceStore.updateCategory(editingCategoryId.value, data)
    } else {
      await serviceStore.createCategory(data)
    }
    showCategoryModal.value = false
    updateStats()
  } catch (error) {
    console.error('Kategori kaydedilemedi:', error)
  }
}

const handleDeleteCategory = async (id: string) => {
  if (!confirm('Bu kategoriyi silmek istediğinizden emin misiniz?')) return
  try {
    await serviceStore.deleteCategory(id)
    updateStats()
  } catch (error) {
    console.error('Kategori silinemedi:', error)
  }
}

// Service Actions
const openCreateServiceModal = () => {
  serviceFormData.value = null
  isEditService.value = false
  editingServiceId.value = null
  showServiceModal.value = true
}

const openEditServiceModal = (service: any) => {
  serviceFormData.value = { ...service }
  isEditService.value = true
  editingServiceId.value = service.id
  showServiceModal.value = true
}

const handleServiceSubmit = async (data: any) => {
  try {
     if (isEditService.value && editingServiceId.value) {
      await serviceStore.updateService(editingServiceId.value, data)
    } else {
      await serviceStore.createService(data)
    }
    showServiceModal.value = false
    updateStats()
  } catch (error) {
    console.error('Hizmet kaydedilemedi:', error)
  }
}

const handleDeleteService = async (id: string) => {
  if (!confirm('Bu hizmeti silmek istediğinizden emin misiniz?')) return
  try {
    await serviceStore.deleteService(id)
    updateStats()
  } catch (error) {
    console.error('Hizmet silinemedi:', error)
  }
}

onMounted(async () => {
    await serviceStore.fetchCategories()
    await serviceStore.fetchServices()
    updateStats()
})
</script>
