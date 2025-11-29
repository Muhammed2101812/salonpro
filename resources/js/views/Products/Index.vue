<template>
  <div class="p-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Ürünler</h1>
      <p class="mt-2 text-gray-600">Salon ürünlerinizi ve stok durumlarını yönetin</p>
    </div>

    <!-- Action Bar -->
    <div class="mb-6 flex justify-between items-center">
      <div class="flex space-x-2">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Ürün ara..."
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
        <select
          v-model="filterCategory"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
          <option value="">Tüm Kategoriler</option>
          <option v-for="cat in uniqueCategories" :key="cat" :value="cat">{{ cat }}</option>
        </select>
      </div>
      <button
        @click="openCreateModal"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition"
      >
        Ürün Ekle
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="productStore.loading" class="text-center py-12">
      <p class="text-gray-600">Yükleniyor...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="productStore.error" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
      {{ productStore.error }}
    </div>

    <!-- Products Table -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün Adı</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barkod / SKU</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fiyat</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="product in filteredProducts" :key="product.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
              <div class="text-xs text-gray-500">{{ product.unit }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-600">{{ product.barcode || '-' }}</div>
              <div class="text-xs text-gray-500">{{ product.sku || '-' }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ product.category || '-' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ formatPrice(product.price) }}</div>
              <div v-if="product.cost_price" class="text-xs text-gray-500">Maliyet: {{ formatPrice(product.cost_price) }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div :class="getStockColorClass(product)" class="text-sm font-medium">
                {{ product.stock_quantity }}
              </div>
              <div class="text-xs text-gray-500">Min: {{ product.min_stock_quantity }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                v-if="product.is_out_of_stock"
                class="px-2 py-1 text-xs rounded-full font-semibold bg-red-100 text-red-800"
              >
                Tükendi
              </span>
              <span
                v-else-if="product.is_low_stock"
                class="px-2 py-1 text-xs rounded-full font-semibold bg-yellow-100 text-yellow-800"
              >
                Az Stok
              </span>
              <span
                v-else-if="product.is_active"
                class="px-2 py-1 text-xs rounded-full font-semibold bg-green-100 text-green-800"
              >
                Aktif
              </span>
              <span
                v-else
                class="px-2 py-1 text-xs rounded-full font-semibold bg-gray-100 text-gray-800"
              >
                Pasif
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
              <button @click="openEditModal(product)" class="text-blue-600 hover:text-blue-900">Düzenle</button>
              <button @click="handleDelete(product.id)" class="text-red-600 hover:text-red-900">Sil</button>
            </td>
          </tr>
          <tr v-if="filteredProducts.length === 0">
            <td colspan="7" class="px-6 py-12 text-center text-gray-500">Ürün bulunamadı</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Product Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-white/30 backdrop-blur-sm flex items-center justify-center z-50" @click.self="closeModal">
      <div class="bg-white rounded-lg p-8 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto shadow-xl relative">
        <button type="button" @click="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        <h2 class="text-2xl font-bold mb-6">{{ isEdit ? 'Ürün Düzenle' : 'Ürün Ekle' }}</h2>
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Ürün Adı *</label>
              <input
                v-model="form.name"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div class="col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
              <textarea
                v-model="form.description"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              ></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Barkod</label>
              <input
                v-model="form.barcode"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Stok Kodu (SKU)</label>
              <input
                v-model="form.sku"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Satış Fiyatı (TL) *</label>
              <input
                v-model="form.price"
                type="number"
                step="0.01"
                min="0"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Maliyet Fiyatı (TL)</label>
              <input
                v-model="form.cost_price"
                type="number"
                step="0.01"
                min="0"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Stok Miktarı *</label>
              <input
                v-model="form.stock_quantity"
                type="number"
                min="0"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Stok Seviyesi *</label>
              <input
                v-model="form.min_stock_quantity"
                type="number"
                min="0"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Birim *</label>
              <select
                v-model="form.unit"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="adet">Adet</option>
                <option value="kg">Kilogram</option>
                <option value="gram">Gram</option>
                <option value="litre">Litre</option>
                <option value="ml">Mililitre</option>
                <option value="paket">Paket</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
              <input
                v-model="form.category"
                type="text"
                placeholder="Örn: Şampuan, Boya, vb."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>
          <div class="flex items-center">
            <input
              v-model="form.is_active"
              type="checkbox"
              id="is_active"
              class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            />
            <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Aktif</label>
          </div>
          <div class="flex justify-end space-x-3 pt-4">
            <button
              type="button"
              @click="closeModal"
              class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
            >
              İptal
            </button>
            <button
              type="submit"
              :disabled="productStore.loading"
              class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition"
            >
              {{ productStore.loading ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useProductStore } from '@/stores/product';

const productStore = useProductStore();

// Search and filter
const searchQuery = ref('');
const filterCategory = ref('');

// Modal state
const showModal = ref(false);
const isEdit = ref(false);
const editingId = ref<string | null>(null);

const form = ref({
  name: '',
  description: '',
  barcode: '',
  sku: '',
  price: 0,
  cost_price: null as number | null,
  stock_quantity: 0,
  min_stock_quantity: 0,
  unit: 'adet',
  category: '',
  is_active: true
});

// Computed
const uniqueCategories = computed(() => {
  const categories = productStore.products
    .map(p => p.category)
    .filter(c => c);
  return [...new Set(categories)];
});

const filteredProducts = computed(() => {
  let filtered = productStore.products;

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(p =>
      p.name.toLowerCase().includes(query) ||
      p.barcode?.toLowerCase().includes(query) ||
      p.sku?.toLowerCase().includes(query)
    );
  }

  if (filterCategory.value) {
    filtered = filtered.filter(p => p.category === filterCategory.value);
  }

  return filtered;
});

// Methods
const resetForm = () => {
  form.value = {
    name: '',
    description: '',
    barcode: '',
    sku: '',
    price: 0,
    cost_price: null,
    stock_quantity: 0,
    min_stock_quantity: 0,
    unit: 'adet',
    category: '',
    is_active: true
  };
};

const openCreateModal = () => {
  resetForm();
  isEdit.value = false;
  editingId.value = null;
  showModal.value = true;
};

const openEditModal = (product: any) => {
  form.value = {
    name: product.name || '',
    description: product.description || '',
    barcode: product.barcode || '',
    sku: product.sku || '',
    price: product.price || 0,
    cost_price: product.cost_price || null,
    stock_quantity: product.stock_quantity || 0,
    min_stock_quantity: product.min_stock_quantity || 0,
    unit: product.unit || 'adet',
    category: product.category || '',
    is_active: product.is_active ?? true
  };
  isEdit.value = true;
  editingId.value = product.id;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const handleSubmit = async () => {
  try {
    if (isEdit.value && editingId.value) {
      await productStore.updateProduct(editingId.value, form.value);
    } else {
      await productStore.createProduct(form.value);
    }
    closeModal();
  } catch (error) {
    console.error('Ürün kaydedilemedi:', error);
  }
};

const handleDelete = async (id: string) => {
  if (confirm('Bu ürünü silmek istediğinizden emin misiniz?')) {
    try {
      await productStore.deleteProduct(id);
    } catch (error) {
      console.error('Ürün silinemedi:', error);
    }
  }
};

const formatPrice = (price: string | number) => {
  return `${Number(price).toFixed(2)} TL`;
};

const getStockColorClass = (product: any) => {
  if (product.is_out_of_stock) return 'text-red-600';
  if (product.is_low_stock) return 'text-yellow-600';
  return 'text-green-600';
};

// Initialize
onMounted(async () => {
  await productStore.fetchProducts();
});
</script>
