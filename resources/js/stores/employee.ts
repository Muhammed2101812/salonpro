import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';

export const useEmployeeStore = defineStore('employee', () => {
  const employees = ref<any[]>([]);
  const currentEmployee = ref<any>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const fetchEmployees = async (params?: any) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get('/employees', params);
      employees.value = response.data;
      return response;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch employees';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchEmployee = async (id: string) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get(`/employees/${id}`);
      currentEmployee.value = response.data;
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch employee';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const createEmployee = async (data: any) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.post('/employees', data);
      employees.value.push(response.data);
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create employee';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const updateEmployee = async (id: string, data: any) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.put(`/employees/${id}`, data);
      const index = employees.value.findIndex(e => e.id === id);
      if (index !== -1) employees.value[index] = response.data;
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to update employee';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const deleteEmployee = async (id: string) => {
    loading.value = true;
    error.value = null;
    try {
      await api.delete(`/employees/${id}`);
      employees.value = employees.value.filter(e => e.id !== id);
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete employee';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return { employees, currentEmployee, loading, error, fetchEmployees, fetchEmployee, createEmployee, updateEmployee, deleteEmployee };
});
