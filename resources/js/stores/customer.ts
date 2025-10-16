import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';

export const useCustomerStore = defineStore('customer', () => {
  const customers = ref<any[]>([]);
  const currentCustomer = ref<any>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const fetchCustomers = async (params?: any) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get('/customers', params);
      customers.value = response.data;
      return response;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch customers';
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
      error.value = err.response?.data?.message || 'Failed to fetch customer';
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
      error.value = err.response?.data?.message || 'Failed to create customer';
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
      error.value = err.response?.data?.message || 'Failed to update customer';
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
      error.value = err.response?.data?.message || 'Failed to delete customer';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return { customers, currentCustomer, loading, error, fetchCustomers, fetchCustomer, createCustomer, updateCustomer, deleteCustomer };
});
