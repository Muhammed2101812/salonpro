import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';

const CACHE_TTL = 5 * 60 * 1000; // 5 minutes cache

export const useCustomerStore = defineStore('customer', () => {
  const customers = ref<any[]>([]);
  const currentCustomer = ref<any>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);
  const lastFetched = ref<number>(0);

  const fetchCustomers = async (params?: any, forceRefresh = false) => {
    // Return cached data if still valid
    const now = Date.now();
    if (!forceRefresh && customers.value.length > 0 && (now - lastFetched.value) < CACHE_TTL) {
      return { data: customers.value };
    }

    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get('/customers', params);
      customers.value = response.data;
      lastFetched.value = Date.now();
      return response;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Müşteriler yüklenirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchCustomer = async (id: string) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get(`/customers/${id}`);
      currentCustomer.value = response.data;
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Müşteri yüklenirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const createCustomer = async (data: any) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.post('/customers', data);
      customers.value.push(response.data);
      return response.data;
    } catch (err: any) {
      // Handle validation errors
      if (err.response?.data?.errors) {
        const validationErrors = Object.values(err.response.data.errors).flat();
        error.value = (validationErrors as string[]).join(', ');
      } else {
        error.value = err.response?.data?.message || 'Müşteri oluşturulurken hata oluştu';
      }
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const updateCustomer = async (id: string, data: any) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.put(`/customers/${id}`, data);
      const index = customers.value.findIndex(c => c.id === id);
      if (index !== -1) customers.value[index] = response.data;
      return response.data;
    } catch (err: any) {
      // Handle validation errors
      if (err.response?.data?.errors) {
        const validationErrors = Object.values(err.response.data.errors).flat();
        error.value = (validationErrors as string[]).join(', ');
      } else {
        error.value = err.response?.data?.message || 'Müşteri güncellenirken hata oluştu';
      }
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const deleteCustomer = async (id: string) => {
    loading.value = true;
    error.value = null;
    try {
      await api.delete(`/customers/${id}`);
      customers.value = customers.value.filter(c => c.id !== id);
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Müşteri silinirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return { customers, currentCustomer, loading, error, fetchCustomers, fetchCustomer, createCustomer, updateCustomer, deleteCustomer };
});
