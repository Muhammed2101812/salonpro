<template>
  <div class="p-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Randevular</h1>
      <p class="mt-2 text-gray-600">Randevularınızı yönetin</p>
    </div>

    <!-- Loading State -->
    <div v-if="appointmentStore.loading && appointments.length === 0" class="text-center py-12">
      <p class="text-gray-600">Yükleniyor...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="appointmentStore.error" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
      {{ appointmentStore.error }}
    </div>

    <!-- Main Content -->
    <div v-else>
      <div class="mb-4 flex justify-between items-center">
        <div class="w-96">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Müşteri, çalışan veya hizmet ara..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
        </div>
        <button @click="openCreateModal" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
          Yeni Randevu
        </button>
      </div>

      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih/Saat</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Çalışan</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hizmet</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Şube</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Süre</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fiyat</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="appointment in filteredAppointments" :key="appointment.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ formatDate(appointment.appointment_date) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-600">{{ getCustomerName(appointment.customer_id) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-600">{{ getEmployeeName(appointment.employee_id) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-600">{{ getServiceName(appointment.service_id) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-600">{{ getBranchName(appointment.branch_id) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ appointment.duration_minutes }} dk</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ formatPrice(appointment.price) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(appointment.status)" class="px-2 py-1 text-xs rounded-full font-semibold">
                  {{ getStatusLabel(appointment.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                <button @click="openEditModal(appointment)" class="text-blue-600 hover:text-blue-900">Düzenle</button>
                <button @click="handleDelete(appointment.id)" class="text-red-600 hover:text-red-900">Sil</button>
              </td>
            </tr>
            <tr v-if="filteredAppointments.length === 0">
              <td colspan="9" class="px-6 py-12 text-center text-gray-500">Randevu bulunamadı</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Appointment Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h2 class="text-2xl font-bold mb-6">{{ isEdit ? 'Randevu Düzenle' : 'Yeni Randevu' }}</h2>
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Şube *</label>
            <select v-model="appointmentForm.branch_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">Şube Seçin</option>
              <option v-for="branch in branchStore.branches" :key="branch.id" :value="branch.id">
                {{ branch.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri *</label>
            <select v-model="appointmentForm.customer_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">Müşteri Seçin</option>
              <option v-for="customer in customerStore.customers" :key="customer.id" :value="customer.id">
                {{ customer.first_name }} {{ customer.last_name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Çalışan *</label>
            <select v-model="appointmentForm.employee_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">Çalışan Seçin</option>
              <option v-for="employee in employeeStore.employees" :key="employee.id" :value="employee.id">
                {{ employee.first_name }} {{ employee.last_name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Hizmet *</label>
            <select v-model="appointmentForm.service_id" @change="onServiceChange" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">Hizmet Seçin</option>
              <option v-for="service in serviceStore.services" :key="service.id" :value="service.id">
                {{ service.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Randevu Tarihi *</label>
            <input v-model="appointmentForm.appointment_date" type="datetime-local" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Süre (Dakika) *</label>
              <input v-model="appointmentForm.duration_minutes" type="number" min="1" required readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Fiyat (TL) *</label>
              <input v-model="appointmentForm.price" type="number" step="0.01" min="0" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Durum *</label>
            <select v-model="appointmentForm.status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="pending">Bekliyor</option>
              <option value="confirmed">Onaylandı</option>
              <option value="completed">Tamamlandı</option>
              <option value="cancelled">İptal Edildi</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
            <textarea v-model="appointmentForm.notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
          </div>
          <div class="flex justify-end space-x-3 pt-4">
            <button type="button" @click="closeModal" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">İptal</button>
            <button type="submit" :disabled="appointmentStore.loading" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition">
              {{ appointmentStore.loading ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useAppointmentStore } from '@/stores/appointment';
import { useCustomerStore } from '@/stores/customer';
import { useEmployeeStore } from '@/stores/employee';
import { useServiceStore } from '@/stores/service';
import { useBranchStore } from '@/stores/branch';

const appointmentStore = useAppointmentStore();
const customerStore = useCustomerStore();
const employeeStore = useEmployeeStore();
const serviceStore = useServiceStore();
const branchStore = useBranchStore();

// Modal State
const showModal = ref(false);
const isEdit = ref(false);
const editingId = ref<string | null>(null);

// Search
const searchQuery = ref('');

// Form
const appointmentForm = ref({
  branch_id: '',
  customer_id: '',
  employee_id: '',
  service_id: '',
  appointment_date: '',
  duration_minutes: 0,
  price: 0,
  status: 'pending' as 'pending' | 'confirmed' | 'cancelled' | 'completed',
  notes: ''
});

// Computed
const appointments = computed(() => appointmentStore.appointments);

const filteredAppointments = computed(() => {
  if (!searchQuery.value) return appointments.value;

  const query = searchQuery.value.toLowerCase();
  return appointments.value.filter(appointment => {
    const customerName = getCustomerName(appointment.customer_id).toLowerCase();
    const employeeName = getEmployeeName(appointment.employee_id).toLowerCase();
    const serviceName = getServiceName(appointment.service_id).toLowerCase();

    return customerName.includes(query) ||
           employeeName.includes(query) ||
           serviceName.includes(query);
  });
});

// Methods
const resetForm = () => {
  appointmentForm.value = {
    branch_id: '',
    customer_id: '',
    employee_id: '',
    service_id: '',
    appointment_date: '',
    duration_minutes: 0,
    price: 0,
    status: 'pending',
    notes: ''
  };
};

const openCreateModal = () => {
  resetForm();
  isEdit.value = false;
  editingId.value = null;
  showModal.value = true;
};

const openEditModal = (appointment: any) => {
  appointmentForm.value = {
    branch_id: appointment.branch_id || '',
    customer_id: appointment.customer_id || '',
    employee_id: appointment.employee_id || '',
    service_id: appointment.service_id || '',
    appointment_date: appointment.appointment_date ? formatDateTimeLocal(appointment.appointment_date) : '',
    duration_minutes: appointment.duration_minutes || 0,
    price: appointment.price || 0,
    status: appointment.status || 'pending',
    notes: appointment.notes || ''
  };
  isEdit.value = true;
  editingId.value = appointment.id;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const handleSubmit = async () => {
  try {
    if (isEdit.value && editingId.value) {
      await appointmentStore.updateAppointment(editingId.value, appointmentForm.value);
    } else {
      await appointmentStore.createAppointment(appointmentForm.value);
    }
    closeModal();
  } catch (error) {
    console.error('Randevu kaydedilemedi:', error);
  }
};

const handleDelete = async (id: string) => {
  if (confirm('Bu randevuyu silmek istediğinizden emin misiniz?')) {
    try {
      await appointmentStore.deleteAppointment(id);
    } catch (error) {
      console.error('Randevu silinemedi:', error);
    }
  }
};

const onServiceChange = () => {
  const selectedService = serviceStore.services.find(s => s.id === appointmentForm.value.service_id);
  if (selectedService) {
    appointmentForm.value.duration_minutes = selectedService.duration_minutes;
    appointmentForm.value.price = Number(selectedService.price);
  }
};

// Helper Methods
const getCustomerName = (customerId: string) => {
  const customer = customerStore.customers.find(c => c.id === customerId);
  return customer ? `${customer.first_name} ${customer.last_name}` : '-';
};

const getEmployeeName = (employeeId: string) => {
  const employee = employeeStore.employees.find(e => e.id === employeeId);
  return employee ? `${employee.first_name} ${employee.last_name}` : '-';
};

const getServiceName = (serviceId: string) => {
  const service = serviceStore.services.find(s => s.id === serviceId);
  return service ? service.name : '-';
};

const getBranchName = (branchId: string) => {
  const branch = branchStore.branches.find(b => b.id === branchId);
  return branch ? branch.name : '-';
};

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('tr-TR', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
};

const formatDateTimeLocal = (dateString: string) => {
  const date = new Date(dateString);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${year}-${month}-${day}T${hours}:${minutes}`;
};

const formatPrice = (price: string | number) => {
  return `${Number(price).toFixed(2)} TL`;
};

const getStatusClass = (status: string) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  };
  return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status: string) => {
  const labels = {
    pending: 'Bekliyor',
    confirmed: 'Onaylandı',
    completed: 'Tamamlandı',
    cancelled: 'İptal Edildi'
  };
  return labels[status as keyof typeof labels] || status;
};

// Initialize Data
onMounted(async () => {
  try {
    await Promise.all([
      appointmentStore.fetchAppointments(),
      branchStore.fetchBranches(),
      customerStore.fetchCustomers(),
      employeeStore.fetchEmployees(),
      serviceStore.fetchServices()
    ]);
  } catch (error) {
    console.error('Veriler yüklenirken hata oluştu:', error);
  }
});
</script>
