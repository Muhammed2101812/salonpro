<template>
  <div class="p-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Hizmetler</h1>
      <p class="mt-2 text-gray-600">Hizmet kategorileri ve hizmetlerinizi yönetin</p>
    </div>

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-200">
      <nav class="-mb-px flex space-x-8">
        <button
          @click="activeTab = 'categories'"
          :class="[
            activeTab === 'categories'
              ? 'border-blue-500 text-blue-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
            'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
          ]"
        >
          Kategoriler
        </button>
        <button
          @click="activeTab = 'services'"
          :class="[
            activeTab === 'services'
              ? 'border-blue-500 text-blue-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
            'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
          ]"
        >
          Hizmetler
        </button>
      </nav>
    </div>

    <!-- Loading State -->
    <div v-if="serviceStore.loading" class="text-center py-12">
      <p class="text-gray-600">Yükleniyor...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="serviceStore.error" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
      {{ serviceStore.error }}
    </div>

    <!-- Categories Tab -->
    <div v-else-if="activeTab === 'categories'">
      <div class="mb-4 flex justify-end">
        <button @click="openCreateCategoryModal" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
          Kategori Ekle
        </button>
      </div>

      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori Adı</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Açıklama</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="category in serviceStore.categories" :key="category.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-gray-600">{{ category.description || '-' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="category.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 text-xs rounded-full font-semibold">
                  {{ category.is_active ? 'Aktif' : 'Pasif' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                <button @click="openEditCategoryModal(category)" class="text-blue-600 hover:text-blue-900">Düzenle</button>
                <button @click="handleDeleteCategory(category.id)" class="text-red-600 hover:text-red-900">Sil</button>
              </td>
            </tr>
            <tr v-if="serviceStore.categories.length === 0">
              <td colspan="4" class="px-6 py-12 text-center text-gray-500">Kategori bulunamadı</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Services Tab -->
    <div v-else-if="activeTab === 'services'">
      <div class="mb-4 flex justify-end">
        <button @click="openCreateServiceModal" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
          Hizmet Ekle
        </button>
      </div>

      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hizmet Adı</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fiyat</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Süre</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="service in serviceStore.services" :key="service.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ service.name }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-600">{{ getCategoryName(service.service_category_id) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ formatPrice(service.price) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ service.duration_minutes }} dk</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="service.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 text-xs rounded-full font-semibold">
                  {{ service.is_active ? 'Aktif' : 'Pasif' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                <button @click="openEditServiceModal(service)" class="text-blue-600 hover:text-blue-900">Düzenle</button>
                <button @click="handleDeleteService(service.id)" class="text-red-600 hover:text-red-900">Sil</button>
              </td>
            </tr>
            <tr v-if="serviceStore.services.length === 0">
              <td colspan="6" class="px-6 py-12 text-center text-gray-500">Hizmet bulunamadı</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Category Modal -->
    <div v-if="showCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h2 class="text-2xl font-bold mb-6">{{ isEditCategory ? 'Kategori Düzenle' : 'Kategori Ekle' }}</h2>
        <form @submit.prevent="handleCategorySubmit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Adı *</label>
            <input v-model="categoryForm.name" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
            <textarea v-model="categoryForm.description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
          </div>
          <div class="flex items-center">
            <input v-model="categoryForm.is_active" type="checkbox" id="category_is_active" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <label for="category_is_active" class="ml-2 text-sm font-medium text-gray-700">Aktif</label>
          </div>
          <div class="flex justify-end space-x-3 pt-4">
            <button type="button" @click="closeCategoryModal" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">İptal</button>
            <button type="submit" :disabled="serviceStore.loading" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition">
              {{ serviceStore.loading ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Service Modal -->
    <div v-if="showServiceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h2 class="text-2xl font-bold mb-6">{{ isEditService ? 'Hizmet Düzenle' : 'Hizmet Ekle' }}</h2>
        <form @submit.prevent="handleServiceSubmit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
            <select v-model="serviceForm.service_category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">Kategori Seçin</option>
              <option v-for="category in serviceStore.categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Hizmet Adı *</label>
            <input v-model="serviceForm.name" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
            <textarea v-model="serviceForm.description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Fiyat (TL) *</label>
              <input v-model="serviceForm.price" type="number" step="0.01" min="0" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Süre (Dakika) *</label>
              <input v-model="serviceForm.duration_minutes" type="number" min="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
          </div>
          <div class="flex items-center">
            <input v-model="serviceForm.is_active" type="checkbox" id="service_is_active" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <label for="service_is_active" class="ml-2 text-sm font-medium text-gray-700">Aktif</label>
          </div>
          <div class="flex justify-end space-x-3 pt-4">
            <button type="button" @click="closeServiceModal" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">İptal</button>
            <button type="submit" :disabled="serviceStore.loading" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition">
              {{ serviceStore.loading ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useServiceStore } from '@/stores/service';

const serviceStore = useServiceStore();
const activeTab = ref<'categories' | 'services'>('categories');

// Category Modal State
const showCategoryModal = ref(false);
const isEditCategory = ref(false);
const editingCategoryId = ref<string | null>(null);

const categoryForm = ref({
  name: '',
  description: '',
  is_active: true
});

// Service Modal State
const showServiceModal = ref(false);
const isEditService = ref(false);
const editingServiceId = ref<string | null>(null);

const serviceForm = ref({
  service_category_id: '',
  name: '',
  description: '',
  price: 0,
  duration_minutes: 30,
  is_active: true
});

// Category Methods
const resetCategoryForm = () => {
  categoryForm.value = {
    name: '',
    description: '',
    is_active: true
  };
};

const openCreateCategoryModal = () => {
  resetCategoryForm();
  isEditCategory.value = false;
  editingCategoryId.value = null;
  showCategoryModal.value = true;
};

const openEditCategoryModal = (category: any) => {
  categoryForm.value = {
    name: category.name || '',
    description: category.description || '',
    is_active: category.is_active ?? true
  };
  isEditCategory.value = true;
  editingCategoryId.value = category.id;
  showCategoryModal.value = true;
};

const closeCategoryModal = () => {
  showCategoryModal.value = false;
  resetCategoryForm();
};

const handleCategorySubmit = async () => {
  try {
    if (isEditCategory.value && editingCategoryId.value) {
      await serviceStore.updateCategory(editingCategoryId.value, categoryForm.value);
    } else {
      await serviceStore.createCategory(categoryForm.value);
    }
    closeCategoryModal();
  } catch (error) {
    console.error('Kategori kaydedilemedi:', error);
  }
};

const handleDeleteCategory = async (id: string) => {
  if (confirm('Bu kategoriyi silmek istediğinizden emin misiniz?')) {
    try {
      await serviceStore.deleteCategory(id);
    } catch (error) {
      console.error('Kategori silinemedi:', error);
    }
  }
};

// Service Methods
const resetServiceForm = () => {
  serviceForm.value = {
    service_category_id: '',
    name: '',
    description: '',
    price: 0,
    duration_minutes: 30,
    is_active: true
  };
};

const openCreateServiceModal = () => {
  resetServiceForm();
  isEditService.value = false;
  editingServiceId.value = null;
  showServiceModal.value = true;
};

const openEditServiceModal = (service: any) => {
  serviceForm.value = {
    service_category_id: service.service_category_id || '',
    name: service.name || '',
    description: service.description || '',
    price: service.price || 0,
    duration_minutes: service.duration_minutes || 30,
    is_active: service.is_active ?? true
  };
  isEditService.value = true;
  editingServiceId.value = service.id;
  showServiceModal.value = true;
};

const closeServiceModal = () => {
  showServiceModal.value = false;
  resetServiceForm();
};

const handleServiceSubmit = async () => {
  try {
    if (isEditService.value && editingServiceId.value) {
      await serviceStore.updateService(editingServiceId.value, serviceForm.value);
    } else {
      await serviceStore.createService(serviceForm.value);
    }
    closeServiceModal();
  } catch (error) {
    console.error('Hizmet kaydedilemedi:', error);
  }
};

const handleDeleteService = async (id: string) => {
  if (confirm('Bu hizmeti silmek istediğinizden emin misiniz?')) {
    try {
      await serviceStore.deleteService(id);
    } catch (error) {
      console.error('Hizmet silinemedi:', error);
    }
  }
};

// Helper Methods
const getCategoryName = (categoryId: string) => {
  const category = serviceStore.categories.find(c => c.id === categoryId);
  return category ? category.name : '-';
};

const formatPrice = (price: string | number) => {
  return `${Number(price).toFixed(2)} TL`;
};

// Initialize Data
onMounted(async () => {
  await serviceStore.fetchCategories();
  await serviceStore.fetchServices();
});
</script>
