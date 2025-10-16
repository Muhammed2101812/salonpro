<template>
  <div class="p-8">
    <div class="mb-8 flex justify-between items-center">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Çalışanlar</h1>
        <p class="mt-2 text-gray-600">Salon çalışanlarınızı yönetin</p>
      </div>
      <button @click="openCreateModal" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition">
        Çalışan Ekle
      </button>
    </div>

    <div v-if="employeeStore.loading" class="text-center py-12">
      <p class="text-gray-600">Yükleniyor...</p>
    </div>

    <div v-else-if="employeeStore.error" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
      {{ employeeStore.error }}
    </div>

    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ad Soyad</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefon</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Şube</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uzmanlık Alanları</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komisyon</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="employee in employeeStore.employees" :key="employee.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ employee.first_name }} {{ employee.last_name }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ employee.phone || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ employee.branch?.name || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              <span v-if="employee.specialties?.length" class="text-xs">
                {{ employee.specialties.join(', ') }}
              </span>
              <span v-else>-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ employee.commission_rate }}%</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="employee.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 text-xs rounded-full font-semibold">
                {{ employee.is_active ? 'Aktif' : 'Pasif' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
              <button @click="openEditModal(employee)" class="text-blue-600 hover:text-blue-900">Düzenle</button>
              <button @click="handleDelete(employee.id)" class="text-red-600 hover:text-red-900">Sil</button>
            </td>
          </tr>
          <tr v-if="employeeStore.employees.length === 0">
            <td colspan="7" class="px-6 py-12 text-center text-gray-500">Çalışan bulunamadı</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h2 class="text-2xl font-bold mb-6">{{ isEdit ? 'Çalışan Düzenle' : 'Çalışan Ekle' }}</h2>
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Ad *</label>
              <input v-model="form.first_name" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Soyad *</label>
              <input v-model="form.last_name" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
              <input v-model="form.phone" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
              <input v-model="form.email" type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Şube *</label>
              <select v-model="form.branch_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                <option value="">Şube seçin</option>
                <option v-for="branch in branchStore.branches" :key="branch.id" :value="branch.id">
                  {{ branch.name }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Komisyon Oranı (%)</label>
              <input v-model="form.commission_rate" type="number" step="0.01" min="0" max="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Uzmanlık Alanları</label>
            <input v-model="specialtiesInput" @keydown.enter.prevent="addSpecialty" type="text" placeholder="Yazın ve eklemek için Enter'a basın" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            <div v-if="form.specialties.length > 0" class="mt-2 flex flex-wrap gap-2">
              <span v-for="(specialty, index) in form.specialties" :key="index" class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">
                {{ specialty }}
                <button type="button" @click="removeSpecialty(index)" class="ml-2 text-purple-600 hover:text-purple-900">×</button>
              </span>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">İşe Başlama Tarihi</label>
            <input v-model="form.hire_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
          </div>
          <div class="flex items-center">
            <input v-model="form.is_active" type="checkbox" id="is_active" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
            <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Aktif</label>
          </div>
          <div class="flex justify-end space-x-3 pt-4">
            <button type="button" @click="closeModal" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">İptal</button>
            <button type="submit" :disabled="employeeStore.loading" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 transition">
              {{ employeeStore.loading ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useEmployeeStore } from '@/stores/employee';
import { useBranchStore } from '@/stores/branch';

const employeeStore = useEmployeeStore();
const branchStore = useBranchStore();
const showModal = ref(false);
const isEdit = ref(false);
const editingId = ref<string | null>(null);
const specialtiesInput = ref('');

const form = ref({
  first_name: '',
  last_name: '',
  phone: '',
  email: '',
  branch_id: '',
  specialties: [] as string[],
  commission_rate: 0,
  hire_date: '',
  is_active: true
});

const resetForm = () => {
  form.value = {
    first_name: '',
    last_name: '',
    phone: '',
    email: '',
    branch_id: '',
    specialties: [],
    commission_rate: 0,
    hire_date: '',
    is_active: true
  };
  specialtiesInput.value = '';
};

const addSpecialty = () => {
  if (specialtiesInput.value.trim()) {
    form.value.specialties.push(specialtiesInput.value.trim());
    specialtiesInput.value = '';
  }
};

const removeSpecialty = (index: number) => {
  form.value.specialties.splice(index, 1);
};

const openCreateModal = () => {
  resetForm();
  isEdit.value = false;
  editingId.value = null;
  showModal.value = true;
};

const openEditModal = (employee: any) => {
  form.value = {
    first_name: employee.first_name || '',
    last_name: employee.last_name || '',
    phone: employee.phone || '',
    email: employee.email || '',
    branch_id: employee.branch_id || '',
    specialties: employee.specialties || [],
    commission_rate: employee.commission_rate || 0,
    hire_date: employee.hire_date || '',
    is_active: employee.is_active ?? true
  };
  isEdit.value = true;
  editingId.value = employee.id;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const handleSubmit = async () => {
  try {
    if (isEdit.value && editingId.value) {
      await employeeStore.updateEmployee(editingId.value, form.value);
    } else {
      await employeeStore.createEmployee(form.value);
    }
    closeModal();
  } catch (error) {
    console.error('Failed to save employee:', error);
  }
};

const handleDelete = async (id: string) => {
  if (confirm('Bu çalışanı silmek istediğinize emin misiniz?')) {
    try {
      await employeeStore.deleteEmployee(id);
    } catch (error) {
      console.error('Failed to delete employee:', error);
    }
  }
};

onMounted(() => {
  employeeStore.fetchEmployees();
  branchStore.fetchBranches();
});
</script>
