import { defineStore } from 'pinia';
import api from '../services/api';

export interface Payment {
  id: string;
  appointment_id?: string;
  sale_id?: string;
  customer_id: string;
  customer?: any;
  amount: number;
  payment_method: 'cash' | 'credit_card' | 'debit_card' | 'bank_transfer';
  payment_date: string;
  status: 'pending' | 'completed' | 'failed' | 'refunded';
  notes?: string;
  created_at: string;
  updated_at: string;
}

interface PaymentState {
  payments: Payment[];
  loading: boolean;
  error: string | null;
}

export const usePaymentStore = defineStore('payment', {
  state: (): PaymentState => ({
    payments: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchPayments() {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.get('/payments');
        this.payments = response.data;
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Ödemeler yüklenemedi';
        console.error('Ödeme listesi hatası:', error);
      } finally {
        this.loading = false;
      }
    },

    async createPayment(paymentData: Partial<Payment>) {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.post('/payments', paymentData);
        this.payments.unshift(response.data.data);
        return response.data.data;
      } catch (error: any) {
        // Handle validation errors
        if (error.response?.data?.errors) {
          const validationErrors = Object.values(error.response.data.errors).flat();
          this.error = (validationErrors as string[]).join(', ');
        } else {
          this.error = error.response?.data?.message || 'Ödeme eklenemedi';
        }
        console.error('Ödeme ekleme hatası:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updatePayment(id: string, paymentData: Partial<Payment>) {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.put(`/payments/${id}`, paymentData);
        const index = this.payments.findIndex(p => p.id === id);
        if (index !== -1) {
          this.payments[index] = response.data.data;
        }
        return response.data.data;
      } catch (error: any) {
        // Handle validation errors
        if (error.response?.data?.errors) {
          const validationErrors = Object.values(error.response.data.errors).flat();
          this.error = (validationErrors as string[]).join(', ');
        } else {
          this.error = error.response?.data?.message || 'Ödeme güncellenemedi';
        }
        console.error('Ödeme güncelleme hatası:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async deletePayment(id: string) {
      this.loading = true;
      this.error = null;
      try {
        await api.delete(`/payments/${id}`);
        this.payments = this.payments.filter(p => p.id !== id);
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Ödeme silinemedi';
        console.error('Ödeme silme hatası:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },
  },
});
