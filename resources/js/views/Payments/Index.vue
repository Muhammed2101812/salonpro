<template>
  <div class="container">
    <div class="header">
      <h1>Ödemeler</h1>
      <button @click="showModal = true" class="btn-primary">+ Yeni Ödeme</button>
    </div>

    <div v-if="paymentStore.loading" class="loading">Yükleniyor...</div>
    <div v-else-if="paymentStore.error" class="error">{{ paymentStore.error }}</div>

    <div v-else class="table-container">
      <table>
        <thead>
          <tr>
            <th>Müşteri</th>
            <th>Tutar</th>
            <th>Ödeme Yöntemi</th>
            <th>Tarih</th>
            <th>Durum</th>
            <th>İşlemler</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="payment in paymentStore.payments" :key="payment.id">
            <td>{{ payment.customer?.name || 'Bilinmiyor' }}</td>
            <td>{{ formatCurrency(payment.amount) }}</td>
            <td>{{ getPaymentMethodLabel(payment.payment_method) }}</td>
            <td>{{ formatDate(payment.payment_date) }}</td>
            <td>
              <span :class="['status-badge', `status-${payment.status}`]">
                {{ getStatusLabel(payment.status) }}
              </span>
            </td>
            <td>
              <button @click="editPayment(payment)" class="btn-edit">Düzenle</button>
              <button @click="confirmDelete(payment.id)" class="btn-delete">Sil</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal" @click.self="closeModal">
      <div class="modal-content">
        <h2>{{ editingPayment ? 'Ödeme Düzenle' : 'Yeni Ödeme' }}</h2>
        <form @submit.prevent="handleSubmit">
          <div class="form-group">
            <label>Müşteri *</label>
            <select v-model="form.customer_id" required>
              <option value="">Müşteri seçin</option>
              <option v-for="customer in customerStore.customers" :key="customer.id" :value="customer.id">
                {{ customer.name }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label>Tutar *</label>
            <input type="number" v-model="form.amount" step="0.01" min="0" required>
          </div>

          <div class="form-group">
            <label>Ödeme Yöntemi *</label>
            <select v-model="form.payment_method" required>
              <option value="cash">Nakit</option>
              <option value="credit_card">Kredi Kartı</option>
              <option value="debit_card">Banka Kartı</option>
              <option value="bank_transfer">Havale</option>
            </select>
          </div>

          <div class="form-group">
            <label>Ödeme Tarihi *</label>
            <input type="date" v-model="form.payment_date" required>
          </div>

          <div class="form-group">
            <label>Durum</label>
            <select v-model="form.status">
              <option value="pending">Beklemede</option>
              <option value="completed">Tamamlandı</option>
              <option value="failed">Başarısız</option>
              <option value="refunded">İade</option>
            </select>
          </div>

          <div class="form-group">
            <label>Notlar</label>
            <textarea v-model="form.notes" rows="3"></textarea>
          </div>

          <div class="form-actions">
            <button type="button" @click="closeModal" class="btn-secondary">İptal</button>
            <button type="submit" class="btn-primary">{{ editingPayment ? 'Güncelle' : 'Ekle' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { usePaymentStore, type Payment } from '../../stores/payment';
import { useCustomerStore } from '../../stores/customer';

const paymentStore = usePaymentStore();
const customerStore = useCustomerStore();
const showModal = ref(false);
const editingPayment = ref<Payment | null>(null);

const form = ref({
  customer_id: '',
  amount: 0,
  payment_method: 'cash',
  payment_date: new Date().toISOString().split('T')[0],
  status: 'completed',
  notes: '',
});

onMounted(async () => {
  await paymentStore.fetchPayments();
  await customerStore.fetchCustomers();
});

const handleSubmit = async () => {
  try {
    if (editingPayment.value) {
      await paymentStore.updatePayment(editingPayment.value.id, form.value);
    } else {
      await paymentStore.createPayment(form.value);
    }
    closeModal();
  } catch (error) {
    console.error('Ödeme kaydedilemedi:', error);
  }
};

const editPayment = (payment: Payment) => {
  editingPayment.value = payment;
  form.value = {
    customer_id: payment.customer_id,
    amount: payment.amount,
    payment_method: payment.payment_method,
    payment_date: payment.payment_date,
    status: payment.status,
    notes: payment.notes || '',
  };
  showModal.value = true;
};

const confirmDelete = async (id: string) => {
  if (confirm('Bu ödemeyi silmek istediğinizden emin misiniz?')) {
    await paymentStore.deletePayment(id);
  }
};

const closeModal = () => {
  showModal.value = false;
  editingPayment.value = null;
  form.value = {
    customer_id: '',
    amount: 0,
    payment_method: 'cash',
    payment_date: new Date().toISOString().split('T')[0],
    status: 'completed',
    notes: '',
  };
};

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY'
  }).format(amount);
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR');
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

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    pending: 'Beklemede',
    completed: 'Tamamlandı',
    failed: 'Başarısız',
    refunded: 'İade',
  };
  return labels[status] || status;
};
</script>

<style scoped>
.container {
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.loading, .error {
  text-align: center;
  padding: 20px;
}

.error {
  color: #d32f2f;
}

.table-container {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  background-color: #f5f5f5;
  font-weight: 600;
}

.status-badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.status-pending {
  background-color: #fff3cd;
  color: #856404;
}

.status-completed {
  background-color: #d4edda;
  color: #155724;
}

.status-failed {
  background-color: #f8d7da;
  color: #721c24;
}

.status-refunded {
  background-color: #d1ecf1;
  color: #0c5460;
}

.btn-primary, .btn-secondary, .btn-edit, .btn-delete {
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn-primary {
  background-color: #1976d2;
  color: white;
}

.btn-secondary {
  background-color: #757575;
  color: white;
}

.btn-edit {
  background-color: #ffa726;
  color: white;
  margin-right: 8px;
}

.btn-delete {
  background-color: #ef5350;
  color: white;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 24px;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  margin-bottom: 4px;
  font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  margin-top: 20px;
}
</style>
