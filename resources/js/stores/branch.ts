import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';

export const useBranchStore = defineStore('branch', () => {
  const branches = ref<any[]>([]);
  const currentBranch = ref<any>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const fetchBranches = async (params?: any) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get('/branches', params);
      branches.value = response.data;
      return response;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch branches';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchBranch = async (id: string) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get(`/branches/${id}`);
      currentBranch.value = response.data;
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch branch';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const createBranch = async (data: any) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.post('/branches', data);
      branches.value.push(response.data);
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create branch';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const updateBranch = async (id: string, data: any) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.put(`/branches/${id}`, data);
      const index = branches.value.findIndex(b => b.id === id);
      if (index !== -1) branches.value[index] = response.data;
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to update branch';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const deleteBranch = async (id: string) => {
    loading.value = true;
    error.value = null;
    try {
      await api.delete(`/branches/${id}`);
      branches.value = branches.value.filter(b => b.id !== id);
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete branch';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return { branches, currentBranch, loading, error, fetchBranches, fetchBranch, createBranch, updateBranch, deleteBranch };
});
