<template>
  <div class="p-8 bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Gösterge Paneli</h1>
      <p class="mt-2 text-gray-600">SalonPro Yönetim Sistemine Hoş Geldiniz</p>
    </div>

    <!-- Date Filter -->
    <div class="mb-6 flex gap-2 bg-white p-4 rounded-lg shadow">
      <button
        v-for="period in periods"
        :key="period.value"
        @click="selectedPeriod = period.value"
        :class="[
          'px-4 py-2 rounded-lg font-medium transition',
          selectedPeriod === period.value
            ? 'bg-blue-600 text-white'
            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
        ]"
      >
        {{ period.label }}
      </button>
    </div>

    <!-- Financial Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-green-100 text-sm font-medium">Toplam Gelir</p>
            <p class="text-3xl font-bold mt-2">{{ formatCurrency(totalIncome) }}</p>
            <p class="text-green-100 text-xs mt-1">{{ selectedPeriodLabel }}</p>
          </div>
          <div class="bg-white/20 rounded-full p-3">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-red-100 text-sm font-medium">Toplam Gider</p>
            <p class="text-3xl font-bold mt-2">{{ formatCurrency(totalExpense) }}</p>
            <p class="text-red-100 text-xs mt-1">{{ selectedPeriodLabel }}</p>
          </div>
          <div class="bg-white/20 rounded-full p-3">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-blue-100 text-sm font-medium">Net Kar/Zarar</p>
            <p class="text-3xl font-bold mt-2">{{ formatCurrency(netProfit) }}</p>
            <p class="text-blue-100 text-xs mt-1">{{ profitMargin }}% Kar Marjı</p>
          </div>
          <div class="bg-white/20 rounded-full p-3">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-purple-100 text-sm font-medium">Ortalama Satış</p>
            <p class="text-3xl font-bold mt-2">{{ formatCurrency(averageSale) }}</p>
            <p class="text-purple-100 text-xs mt-1">{{ filteredSales.length }} Satış</p>
          </div>
          <div class="bg-white/20 rounded-full p-3">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Toplam Müşteri</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ customerStore.customers.length }}</p>
          </div>
          <div class="bg-green-100 rounded-full p-3">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Toplam Randevu</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ filteredAppointments.length }}</p>
            <p class="text-xs text-gray-500 mt-1">Bekleyen: {{ pendingAppointments }}</p>
          </div>
          <div class="bg-blue-100 rounded-full p-3">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Toplam Ürün</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ productStore.products.length }}</p>
            <p class="text-xs text-red-600 mt-1">{{ lowStockProducts.length }} Düşük Stok</p>
          </div>
          <div class="bg-purple-100 rounded-full p-3">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Toplam Hizmet</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ serviceStore.services.length }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ activeServices }} Aktif</p>
          </div>
          <div class="bg-yellow-100 rounded-full p-3">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts and Lists Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Income vs Expense Chart -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Gelir vs Gider Analizi</h2>
        <div class="h-64 flex items-end justify-around gap-2">
          <div class="flex-1 flex flex-col items-center">
            <div class="w-full bg-green-500 rounded-t-lg transition-all" :style="{ height: incomeBarHeight }"></div>
            <p class="text-sm font-medium text-gray-700 mt-2">Gelir</p>
            <p class="text-xs text-gray-500">{{ formatCurrency(totalIncome) }}</p>
          </div>
          <div class="flex-1 flex flex-col items-center">
            <div class="w-full bg-red-500 rounded-t-lg transition-all" :style="{ height: expenseBarHeight }"></div>
            <p class="text-sm font-medium text-gray-700 mt-2">Gider</p>
            <p class="text-xs text-gray-500">{{ formatCurrency(totalExpense) }}</p>
          </div>
          <div class="flex-1 flex flex-col items-center">
            <div class="w-full bg-blue-500 rounded-t-lg transition-all" :style="{ height: profitBarHeight }"></div>
            <p class="text-sm font-medium text-gray-700 mt-2">Kar</p>
            <p class="text-xs text-gray-500">{{ formatCurrency(netProfit) }}</p>
          </div>
        </div>
      </div>

      <!-- Today's Appointments -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Bugünkü Randevular</h2>
        <div class="max-h-64 overflow-y-auto">
          <div v-if="todayAppointments.length > 0" class="space-y-3">
            <div v-for="appointment in todayAppointments.slice(0, 5)" :key="appointment.id"
                 class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
              <div class="flex-1">
                <p class="font-medium text-gray-900">{{ appointment.customer?.name || 'Bilinmiyor' }}</p>
                <p class="text-sm text-gray-600">{{ appointment.service?.name }} - {{ appointment.appointment_time }}</p>
              </div>
              <span :class="['px-3 py-1 rounded-full text-xs font-medium', getStatusClass(appointment.status)]">
                {{ getStatusLabel(appointment.status) }}
              </span>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p>Bugün randevu yok</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Low Stock Products -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Düşük Stoklu Ürünler</h2>
        <div class="max-h-64 overflow-y-auto">
          <div v-if="lowStockProducts.length > 0" class="space-y-3">
            <div v-for="product in lowStockProducts" :key="product.id"
                 class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
              <div>
                <p class="font-medium text-gray-900">{{ product.name }}</p>
                <p class="text-sm text-gray-600">Min: {{ product.min_stock_quantity }}</p>
              </div>
              <span class="px-3 py-1 bg-red-600 text-white rounded-lg font-bold">{{ product.stock_quantity }}</span>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p>Tüm ürünler yeterli stokta</p>
          </div>
        </div>
      </div>

      <!-- Recent Payments -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Son Ödemeler</h2>
        <div class="max-h-64 overflow-y-auto">
          <div v-if="recentPayments.length > 0" class="space-y-3">
            <div v-for="payment in recentPayments" :key="payment.id"
                 class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div>
                <p class="font-medium text-gray-900">{{ payment.customer?.name || 'Bilinmiyor' }}</p>
                <p class="text-sm text-gray-600">{{ getPaymentMethodLabel(payment.payment_method) }}</p>
              </div>
              <span class="font-bold text-green-600">{{ formatCurrency(payment.amount) }}</span>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            <p>Ödeme kaydı yok</p>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Hızlı İşlemler</h2>
        <div class="space-y-3">
          <router-link to="/appointments"
                       class="flex items-center gap-3 px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-lg text-blue-700 font-medium transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Yeni Randevu
          </router-link>
          <router-link to="/sales"
                       class="flex items-center gap-3 px-4 py-3 bg-green-50 hover:bg-green-100 rounded-lg text-green-700 font-medium transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            Yeni Satış
          </router-link>
          <router-link to="/customers"
                       class="flex items-center gap-3 px-4 py-3 bg-purple-50 hover:bg-purple-100 rounded-lg text-purple-700 font-medium transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            Yeni Müşteri
          </router-link>
          <router-link to="/products"
                       class="flex items-center gap-3 px-4 py-3 bg-orange-50 hover:bg-orange-100 rounded-lg text-orange-700 font-medium transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            Ürün Yönetimi
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, computed, ref } from 'vue';
import { useCustomerStore } from '@/stores/customer';
import { useAppointmentStore } from '@/stores/appointment';
import { useProductStore } from '@/stores/product';
import { useServiceStore } from '@/stores/service';
import { usePaymentStore } from '@/stores/payment';
import { useExpenseStore } from '@/stores/expense';
import { useSaleStore } from '@/stores/sale';

const customerStore = useCustomerStore();
const appointmentStore = useAppointmentStore();
const productStore = useProductStore();
const serviceStore = useServiceStore();
const paymentStore = usePaymentStore();
const expenseStore = useExpenseStore();
const saleStore = useSaleStore();

const selectedPeriod = ref('today');

const periods = [
  { value: 'today', label: 'Bugün' },
  { value: 'week', label: 'Bu Hafta' },
  { value: 'month', label: 'Bu Ay' },
  { value: 'year', label: 'Bu Yıl' },
];

const selectedPeriodLabel = computed(() => {
  return periods.find(p => p.value === selectedPeriod.value)?.label || '';
});

const getDateRange = () => {
  const now = new Date();
  let startDate = new Date();

  switch (selectedPeriod.value) {
    case 'today':
      startDate.setHours(0, 0, 0, 0);
      break;
    case 'week':
      startDate.setDate(now.getDate() - 7);
      break;
    case 'month':
      startDate.setMonth(now.getMonth() - 1);
      break;
    case 'year':
      startDate.setFullYear(now.getFullYear() - 1);
      break;
  }

  return { startDate, endDate: now };
};

const isInDateRange = (dateString: string) => {
  const { startDate, endDate } = getDateRange();
  const date = new Date(dateString);
  return date >= startDate && date <= endDate;
};

const filteredPayments = computed(() => {
  return paymentStore.payments.filter((p: any) =>
    p.status === 'completed' && p.payment_date && isInDateRange(p.payment_date)
  );
});

const filteredExpenses = computed(() => {
  return expenseStore.expenses.filter((e: any) => e.expense_date && isInDateRange(e.expense_date));
});

const filteredSales = computed(() => {
  return saleStore.sales.filter((s: any) => s.sale_date && isInDateRange(s.sale_date));
});

const filteredAppointments = computed(() => {
  return appointmentStore.appointments.filter((a: any) => a.appointment_date && isInDateRange(a.appointment_date));
});

const totalIncome = computed(() => {
  const paymentsTotal = filteredPayments.value.reduce((sum: number, p: any) => sum + parseFloat(p.amount || 0), 0);
  const salesTotal = filteredSales.value.reduce((sum: number, s: any) => sum + parseFloat(s.total || 0), 0);
  return paymentsTotal + salesTotal;
});

const totalExpense = computed(() => {
  return filteredExpenses.value.reduce((sum: number, e: any) => sum + parseFloat(e.amount || 0), 0);
});

const netProfit = computed(() => {
  return totalIncome.value - totalExpense.value;
});

const profitMargin = computed(() => {
  if (totalIncome.value === 0) return 0;
  return ((netProfit.value / totalIncome.value) * 100).toFixed(1);
});

const averageSale = computed(() => {
  if (filteredSales.value.length === 0) return 0;
  return totalIncome.value / filteredSales.value.length;
});

const pendingAppointments = computed(() => {
  return filteredAppointments.value.filter((a: any) => a.status === 'pending').length;
});

const activeServices = computed(() => {
  return serviceStore.services.filter((s: any) => s.is_active).length;
});

const todayAppointments = computed(() => {
  const today = new Date().toISOString().split('T')[0];
  return appointmentStore.appointments.filter((apt: any) =>
    apt.appointment_date?.startsWith(today)
  );
});

const lowStockProducts = computed(() => {
  return productStore.products.filter((product: any) =>
    product.stock_quantity <= product.min_stock_quantity
  ).slice(0, 5);
});

const recentPayments = computed(() => {
  return [...paymentStore.payments]
    .filter((p: any) => p.created_at)
    .sort((a: any, b: any) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
    .slice(0, 5);
});

// Chart Heights
const maxValue = computed(() => {
  return Math.max(totalIncome.value, totalExpense.value, Math.abs(netProfit.value));
});

const incomeBarHeight = computed(() => {
  if (maxValue.value === 0) return '0%';
  return `${(totalIncome.value / maxValue.value) * 100}%`;
});

const expenseBarHeight = computed(() => {
  if (maxValue.value === 0) return '0%';
  return `${(totalExpense.value / maxValue.value) * 100}%`;
});

const profitBarHeight = computed(() => {
  if (maxValue.value === 0) return '0%';
  return `${(Math.abs(netProfit.value) / maxValue.value) * 100}%`;
});

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY'
  }).format(amount);
};

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    pending: 'Bekliyor',
    confirmed: 'Onaylandı',
    completed: 'Tamamlandı',
    cancelled: 'İptal',
  };
  return labels[status] || status;
};

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getPaymentMethodLabel = (method: string) => {
  const labels: Record<string, string> = {
    cash: 'Nakit',
    credit_card: 'Kredi Kartı',
    debit_card: 'Banka Kartı',
    bank_transfer: 'Havale',
  };
  return labels[method] || method;
};

onMounted(async () => {
  try {
    await Promise.all([
      customerStore.fetchCustomers(),
      appointmentStore.fetchAppointments(),
      productStore.fetchProducts(),
      serviceStore.fetchServices(),
      paymentStore.fetchPayments(),
      expenseStore.fetchExpenses(),
      saleStore.fetchSales(),
    ]);
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error);
  }
});
</script>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
