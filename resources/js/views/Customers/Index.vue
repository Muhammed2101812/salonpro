<template>
  <div class="p-8">
    <div class="mb-8 flex justify-between items-center">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Müşteriler</h1>
        <p class="mt-2 text-gray-600">Salon müşterilerinizi yönetin</p>
      </div>
      <button @click="openCreateModal" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition">
        Müşteri Ekle
      </button>
    </div>

    <div v-if="customerStore.loading" class="text-center py-12">
      <p class="text-gray-600">Yükleniyor...</p>
    </div>

    <div v-else-if="customerStore.error" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
      {{ customerStore.error }}
    </div>

    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ad Soyad</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefon</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-posta</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cinsiyet</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Şube</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="customer in customerStore.customers" :key="customer.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <router-link :to="`/customers/${customer.id}`" class="text-sm font-medium text-blue-600 hover:text-blue-900">
                {{ customer.first_name }} {{ customer.last_name }}
              </router-link>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ customer.phone }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ customer.email || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 capitalize">{{ customer.gender || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ customer.branch?.name || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
              <button @click="openEditModal(customer)" class="text-blue-600 hover:text-blue-900">Düzenle</button>
              <button @click="handleDelete(customer.id)" class="text-red-600 hover:text-red-900">Sil</button>
            </td>
          </tr>
          <tr v-if="customerStore.customers.length === 0">
            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Müşteri bulunamadı</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-white/30 backdrop-blur-sm flex items-center justify-center z-50" @click.self="closeModal">
      <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto shadow-xl relative">
        <button type="button" @click="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        <h2 class="text-2xl font-bold mb-6">{{ isEdit ? 'Müşteri Düzenle' : 'Müşteri Ekle' }}</h2>
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Ad *</label>
              <input v-model="form.first_name" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Soyad *</label>
              <input v-model="form.last_name" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Telefon *</label>
              <input v-model="form.phone" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
              <input v-model="form.email" type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Şube *</label>
              <select v-model="form.branch_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <option value="">Şube seçin</option>
                <option v-for="branch in branchStore.branches" :key="branch.id" :value="branch.id">
                  {{ branch.name }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Cinsiyet</label>
              <select v-model="form.gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <option value="">Cinsiyet seçin</option>
                <option value="male">Erkek</option>
                <option value="female">Kadın</option>
                <option value="other">Diğer</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Doğum Tarihi</label>
            <input v-model="form.date_of_birth" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
            <textarea v-model="form.notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
          </div>
          <div class="flex justify-end space-x-3 pt-4">
            <button type="button" @click="closeModal" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">İptal</button>
            <button type="submit" :disabled="customerStore.loading" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 transition">
              {{ customerStore.loading ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useCustomerStore } from '@/stores/customer';
import { useBranchStore } from '@/stores/branch';

const customerStore = useCustomerStore();
const branchStore = useBranchStore();
const showModal = ref(false);
const isEdit = ref(false);
const editingId = ref<string | null>(null);

const form = ref({
  first_name: '',
  last_name: '',
  phone: '',
  email: '',
  branch_id: '',
  gender: '',
  date_of_birth: '',
  notes: ''
});

const resetForm = () => {
  form.value = {
    first_name: '',
    last_name: '',
    phone: '',
    email: '',
    branch_id: '',
    gender: '',
    date_of_birth: '',
    notes: ''
  };
};

const openCreateModal = () => {
  resetForm();
  isEdit.value = false;
  editingId.value = null;
  showModal.value = true;
};

const openEditModal = (customer: any) => {
  form.value = {
    first_name: customer.first_name || '',
    last_name: customer.last_name || '',
    phone: customer.phone || '',
    email: customer.email || '',
    branch_id: customer.branch_id || '',
    gender: customer.gender || '',
    date_of_birth: customer.date_of_birth || '',
    notes: customer.notes || ''
  };
  isEdit.value = true;
  editingId.value = customer.id;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const handleSubmit = async () => {
  try {
    if (isEdit.value && editingId.value) {
      await customerStore.updateCustomer(editingId.value, form.value);
    } else {
      await customerStore.createCustomer(form.value);
    }
    closeModal();
  } catch (error) {
    console.error('Failed to save customer:', error);
  }
};

const handleDelete = async (id: string) => {
  if (confirm('Bu müşteriyi silmek istediğinize emin misiniz?')) {
    try {
      await customerStore.deleteCustomer(id);
    } catch (error) {
      console.error('Failed to delete customer:', error);
    }
  }
};

onMounted(() => {
  customerStore.fetchCustomers();
  branchStore.fetchBranches();
});
</script>
