import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';

const CACHE_TTL = 5 * 60 * 1000; // 5 minutes cache

export const useExpenseStore = defineStore('expense', () => {
  const expenses = ref<any[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);
  const lastFetched = ref<number>(0);

  const fetchExpenses = async (forceRefresh = false) => {
    const now = Date.now();
    if (!forceRefresh && expenses.value.length > 0 && (now - lastFetched.value) < CACHE_TTL) {
      return { data: expenses.value };
    }

    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get('/expenses');
      expenses.value = response.data;
      lastFetched.value = Date.now();
      return response;
    } catch (err: any) {
      error.value = 'Giderler yüklenirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const createExpense = async (data: any) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.post('/expenses', data);
      expenses.value.unshift(response.data);
      return response.data;
    } catch (err: any) {
      // Handle validation errors
      if (err.response?.data?.errors) {
        const validationErrors = Object.values(err.response.data.errors).flat();
        error.value = (validationErrors as string[]).join(', ');
      } else {
        error.value = err.response?.data?.message || 'Gider eklenirken hata oluştu';
      }
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const updateExpense = async (id: string, data: any) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.put(`/expenses/${id}`, data);
      const index = expenses.value.findIndex(e => e.id === id);
      if (index !== -1) expenses.value[index] = response.data;
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Gider güncellenirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const deleteExpense = async (id: string) => {
    loading.value = true;
    error.value = null;
    try {
      await api.delete(`/expenses/${id}`);
      expenses.value = expenses.value.filter(e => e.id !== id);
    } catch (err: any) {
      error.value = 'Gider silinirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return { expenses, loading, error, fetchExpenses, createExpense, updateExpense, deleteExpense };
});
