<template>
  <div class="p-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Stok Hareketleri</h1>
      <p class="mt-2 text-gray-600">Ürün stok giriş ve çıkışlarını takip edin</p>
    </div>

    <div class="mb-6 flex justify-end">
      <button @click="openCreateModal" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
        Stok Hareketi Ekle
      </button>
    </div>

    <!-- Loading / Error -->
    <div v-if="inventoryStore.loading" class="text-center py-12">
      <p class="text-gray-600">Yükleniyor...</p>
    </div>
    <div v-else-if="inventoryStore.error" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
      {{ inventoryStore.error }}
    </div>

    <!-- Movements Table -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tip</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Miktar</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Değişimi</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Açıklama</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="movement in inventoryStore.movements" :key="movement.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ formatDate(movement.movement_date) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ movement.product?.name || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getTypeClass(movement.type)" class="px-2 py-1 text-xs rounded-full font-semibold">
                {{ getTypeLabel(movement.type) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ movement.quantity }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ movement.quantity_before }} → {{ movement.quantity_after }}</td>
            <td class="px-6 py-4 text-sm text-gray-600">{{ movement.reason || '-' }}</td>
          </tr>
          <tr v-if="inventoryStore.movements.length === 0">
            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Stok hareketi bulunamadı</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h2 class="text-2xl font-bold mb-6">Stok Hareketi Ekle</h2>
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ürün *</label>
            <select v-model="form.product_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
              <option value="">Ürün Seçin</option>
              <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }} (Stok: {{ product.stock_quantity }})</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Hareket Tipi *</label>
            <select v-model="form.type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
              <option value="in">Giriş</option>
              <option value="out">Çıkış</option>
              <option value="adjustment">Düzeltme</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Miktar *</label>
            <input v-model="form.quantity" type="number" required min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tarih *</label>
            <input v-model="form.movement_date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
            <textarea v-model="form.reason" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
          </div>
          <div class="flex justify-end space-x-3 pt-4">
            <button type="button" @click="closeModal" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">İptal</button>
            <button type="submit" :disabled="inventoryStore.loading" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition">
              {{ inventoryStore.loading ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useInventoryStore } from '@/stores/inventory';
import { useProductStore } from '@/stores/product';

const inventoryStore = useInventoryStore();
const productStore = useProductStore();
const showModal = ref(false);
const products = ref<any[]>([]);

const form = ref({
  product_id: '',
  type: 'in' as 'in' | 'out' | 'adjustment',
  quantity: 0,
  reason: '',
  movement_date: new Date().toISOString().split('T')[0]
});

const openCreateModal = () => {
  form.value = {
    product_id: '',
    type: 'in',
    quantity: 0,
    reason: '',
    movement_date: new Date().toISOString().split('T')[0]
  };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const handleSubmit = async () => {
  try {
    await inventoryStore.createMovement(form.value);
    closeModal();
    await inventoryStore.fetchMovements();
    await productStore.fetchProducts(); // Refresh products
  } catch (error) {
    console.error('Stok hareketi eklenemedi:', error);
  }
};

const getTypeLabel = (type: string) => {
  const labels = { in: 'Giriş', out: 'Çıkış', adjustment: 'Düzeltme' };
  return labels[type as keyof typeof labels] || type;
};

const getTypeClass = (type: string) => {
  const classes = {
    in: 'bg-green-100 text-green-800',
    out: 'bg-red-100 text-red-800',
    adjustment: 'bg-blue-100 text-blue-800'
  };
  return classes[type as keyof typeof classes] || '';
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR');
};

onMounted(async () => {
  await inventoryStore.fetchMovements();
  const response = await productStore.fetchProducts();
  products.value = response.data || [];
});
</script>
