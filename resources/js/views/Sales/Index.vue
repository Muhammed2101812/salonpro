<template>
  <div class="container">
    <div class="header">
      <h1>Satışlar</h1>
      <button @click="showModal = true" class="btn-primary">+ Yeni Satış</button>
    </div>

    <div v-if="saleStore.loading" class="loading">Yükleniyor...</div>
    <div v-else-if="saleStore.error" class="error">{{ saleStore.error }}</div>

    <div v-else class="table-container">
      <table>
        <thead>
          <tr>
            <th>Müşteri</th>
            <th>Çalışan</th>
            <th>Tarih</th>
            <th>Ara Toplam</th>
            <th>İndirim</th>
            <th>Vergi</th>
            <th>Toplam</th>
            <th>İşlemler</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="sale in saleStore.sales" :key="sale.id">
            <td>{{ sale.customer?.name || 'Bilinmiyor' }}</td>
            <td>{{ sale.employee?.name || 'Bilinmiyor' }}</td>
            <td>{{ formatDate(sale.sale_date) }}</td>
            <td>{{ formatCurrency(sale.subtotal) }}</td>
            <td>{{ formatCurrency(sale.discount) }}</td>
            <td>{{ formatCurrency(sale.tax) }}</td>
            <td><strong>{{ formatCurrency(sale.total) }}</strong></td>
            <td>
              <button @click="viewSale(sale)" class="btn-view">Görüntüle</button>
              <button @click="confirmDelete(sale.id)" class="btn-delete">Sil</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal-backdrop" @click.self="closeModal">
      <div class="modal-container modal-large">
        <div class="modal-header">
          <h2>Yeni Satış</h2>
          <button type="button" @click="closeModal" class="modal-close-btn">&times;</button>
        </div>
        <form @submit.prevent="handleSubmit">
          <div class="form-row">
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
              <label>Çalışan *</label>
              <select v-model="form.employee_id" required>
                <option value="">Çalışan seçin</option>
                <option v-for="employee in employeeStore.employees" :key="employee.id" :value="employee.id">
                  {{ employee.name }}
                </option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label>Satış Tarihi *</label>
            <input type="date" v-model="form.sale_date" required>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Ara Toplam *</label>
              <input type="number" v-model="form.subtotal" step="0.01" min="0" required @input="calculateTotal">
            </div>

            <div class="form-group">
              <label>İndirim</label>
              <input type="number" v-model="form.discount" step="0.01" min="0" @input="calculateTotal">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Vergi (KDV)</label>
              <input type="number" v-model="form.tax" step="0.01" min="0" @input="calculateTotal">
            </div>

            <div class="form-group">
              <label>Toplam</label>
              <input type="number" v-model="form.total" step="0.01" readonly>
            </div>
          </div>

          <div class="form-group">
            <label>Notlar</label>
            <textarea v-model="form.notes" rows="3"></textarea>
          </div>

          <div class="form-actions">
            <button type="button" @click="closeModal" class="btn-secondary">İptal</button>
            <button type="submit" class="btn-primary">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useSaleStore, type Sale } from '../../stores/sale';
import { useCustomerStore } from '../../stores/customer';
import { useEmployeeStore } from '../../stores/employee';

const saleStore = useSaleStore();
const customerStore = useCustomerStore();
const employeeStore = useEmployeeStore();
const showModal = ref(false);

const form = ref({
  customer_id: '',
  employee_id: '',
  sale_date: new Date().toISOString().split('T')[0],
  subtotal: 0,
  discount: 0,
  tax: 0,
  total: 0,
  notes: '',
});

onMounted(async () => {
  await saleStore.fetchSales();
  await customerStore.fetchCustomers();
  await employeeStore.fetchEmployees();
});

const calculateTotal = () => {
  const subtotal = parseFloat(String(form.value.subtotal)) || 0;
  const discount = parseFloat(String(form.value.discount)) || 0;
  const tax = parseFloat(String(form.value.tax)) || 0;
  form.value.total = subtotal - discount + tax;
};

const handleSubmit = async () => {
  try {
    await saleStore.createSale(form.value);
    closeModal();
  } catch (error) {
    console.error('Satış kaydedilemedi:', error);
  }
};

const viewSale = (sale: Sale) => {
  alert(`Satış Detayları:\n\nMüşteri: ${sale.customer?.name}\nÇalışan: ${sale.employee?.name}\nToplam: ${formatCurrency(sale.total)}`);
};

const confirmDelete = async (id: string) => {
  if (confirm('Bu satışı silmek istediğinizden emin misiniz?')) {
    await saleStore.deleteSale(id);
  }
};

const closeModal = () => {
  showModal.value = false;
  form.value = {
    customer_id: '',
    employee_id: '',
    sale_date: new Date().toISOString().split('T')[0],
    subtotal: 0,
    discount: 0,
    tax: 0,
    total: 0,
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

.btn-primary, .btn-secondary, .btn-view, .btn-delete {
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

.btn-view {
  background-color: #42a5f5;
  color: white;
  margin-right: 8px;
}

.btn-delete {
  background-color: #ef5350;
  color: white;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
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
