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
      <!-- Controls -->
      <div class="mb-6 flex justify-between items-center">
        <div class="flex gap-4 items-center">
          <!-- View Toggle -->
          <div class="flex bg-gray-100 rounded-lg p-1">
            <button
              @click="viewMode = 'calendar'"
              :class="[
                'px-4 py-2 rounded-md text-sm font-medium transition',
                viewMode === 'calendar' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900'
              ]"
            >
              <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              Takvim
            </button>
            <button
              @click="viewMode = 'list'"
              :class="[
                'px-4 py-2 rounded-md text-sm font-medium transition',
                viewMode === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900'
              ]"
            >
              <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
              Liste
            </button>
          </div>

          <!-- Month Navigation (for calendar view) -->
          <div v-if="viewMode === 'calendar'" class="flex items-center gap-2">
            <button @click="previousMonth" class="p-2 hover:bg-gray-100 rounded-lg transition">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <span class="text-lg font-semibold px-4">{{ currentMonthYear }}</span>
            <button @click="nextMonth" class="p-2 hover:bg-gray-100 rounded-lg transition">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
            <button @click="goToToday" class="ml-2 px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">
              Bugün
            </button>
          </div>
        </div>

        <button @click="openCreateModal" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Yeni Randevu
        </button>
      </div>

      <!-- Calendar View -->
      <div v-if="viewMode === 'calendar'" class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Calendar Header -->
        <div class="grid grid-cols-7 bg-gray-50 border-b">
          <div v-for="day in weekDays" :key="day" class="px-4 py-3 text-center text-sm font-semibold text-gray-700">
            {{ day }}
          </div>
        </div>

        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 divide-x divide-y">
          <div
            v-for="day in calendarDays"
            :key="day.date"
            :class="[
              'min-h-32 p-2',
              !day.isCurrentMonth ? 'bg-gray-50' : 'bg-white',
              day.isToday ? 'bg-blue-50' : ''
            ]"
          >
            <div class="flex justify-between items-start mb-2">
              <span :class="[
                'text-sm font-semibold',
                day.isToday ? 'bg-blue-600 text-white w-6 h-6 flex items-center justify-center rounded-full' : '',
                !day.isCurrentMonth ? 'text-gray-400' : 'text-gray-700'
              ]">
                {{ day.dayNumber }}
              </span>
            </div>

            <!-- Appointments for this day -->
            <div class="space-y-1">
              <div
                v-for="appointment in day.appointments.slice(0, 3)"
                :key="appointment.id"
                @click="openEditModal(appointment)"
                :class="[
                  'text-xs p-1.5 rounded cursor-pointer hover:opacity-80 transition',
                  getStatusClass(appointment.status)
                ]"
              >
                <div class="font-medium truncate">{{ formatTime(appointment.appointment_date) }}</div>
                <div class="truncate opacity-90">{{ getCustomerName(appointment.customer_id) }}</div>
              </div>
              <div v-if="day.appointments.length > 3" class="text-xs text-gray-500 text-center py-1">
                +{{ day.appointments.length - 3 }} daha
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- List View -->
      <div v-else class="bg-white rounded-lg shadow overflow-hidden">
        <div class="mb-4 px-6 pt-6">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Müşteri, çalışan veya hizmet ara..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
        </div>

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

// View State
const viewMode = ref<'calendar' | 'list'>('calendar');
const currentDate = ref(new Date());

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

// Calendar Computed
const weekDays = ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'];

const currentMonthYear = computed(() => {
  return new Intl.DateTimeFormat('tr-TR', { month: 'long', year: 'numeric' }).format(currentDate.value);
});

const calendarDays = computed(() => {
  const year = currentDate.value.getFullYear();
  const month = currentDate.value.getMonth();

  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);

  // Adjust for Monday start (0 = Monday, 6 = Sunday)
  let startDay = firstDay.getDay() - 1;
  if (startDay === -1) startDay = 6;

  const daysInMonth = lastDay.getDate();
  const days = [];

  // Previous month days
  const prevMonthLastDay = new Date(year, month, 0).getDate();
  for (let i = startDay - 1; i >= 0; i--) {
    const date = new Date(year, month - 1, prevMonthLastDay - i);
    days.push({
      date: date.toISOString(),
      dayNumber: prevMonthLastDay - i,
      isCurrentMonth: false,
      isToday: false,
      appointments: getAppointmentsForDate(date)
    });
  }

  // Current month days
  const today = new Date();
  for (let i = 1; i <= daysInMonth; i++) {
    const date = new Date(year, month, i);
    days.push({
      date: date.toISOString(),
      dayNumber: i,
      isCurrentMonth: true,
      isToday: date.toDateString() === today.toDateString(),
      appointments: getAppointmentsForDate(date)
    });
  }

  // Next month days
  const remainingDays = 42 - days.length; // 6 weeks * 7 days
  for (let i = 1; i <= remainingDays; i++) {
    const date = new Date(year, month + 1, i);
    days.push({
      date: date.toISOString(),
      dayNumber: i,
      isCurrentMonth: false,
      isToday: false,
      appointments: getAppointmentsForDate(date)
    });
  }

  return days;
});

const getAppointmentsForDate = (date: Date) => {
  const dateStr = date.toDateString();
  return appointments.value.filter(apt => {
    const aptDate = new Date(apt.appointment_date);
    return aptDate.toDateString() === dateStr;
  }).sort((a, b) => {
    return new Date(a.appointment_date).getTime() - new Date(b.appointment_date).getTime();
  });
};

// Calendar Navigation
const previousMonth = () => {
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1);
};

const nextMonth = () => {
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1);
};

const goToToday = () => {
  currentDate.value = new Date();
};

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

const formatTime = (dateString: string) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('tr-TR', {
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
    pending: 'bg-yellow-100 text-yellow-800 border border-yellow-200',
    confirmed: 'bg-blue-100 text-blue-800 border border-blue-200',
    completed: 'bg-green-100 text-green-800 border border-green-200',
    cancelled: 'bg-red-100 text-red-800 border border-red-200'
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
