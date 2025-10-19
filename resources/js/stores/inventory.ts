import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';

interface InventoryMovement {
  id: string;
  product_id: string;
  product?: any;
  user_id?: string;
  user?: any;
  type: 'in' | 'out' | 'adjustment';
  quantity: number;
  quantity_before: number;
  quantity_after: number;
  reason?: string;
  reference_type?: string;
  reference_id?: string;
  movement_date: string;
  created_at?: string;
}

export const useInventoryStore = defineStore('inventory', () => {
  const movements = ref<InventoryMovement[]>([]);
  const currentMovement = ref<InventoryMovement | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const fetchMovements = async (params?: any) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get('/inventory-movements', params);
      movements.value = response.data;
      return response;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Stok hareketleri yüklenirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchMovement = async (id: string) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get(`/inventory-movements/${id}`);
      currentMovement.value = response.data;
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Stok hareketi yüklenirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const createMovement = async (data: Partial<InventoryMovement>) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.post('/inventory-movements', data);
      movements.value.unshift(response.data.data || response.data);
      return response.data.data || response.data;
    } catch (err: any) {
      // Handle validation errors
      if (err.response?.data?.errors) {
        const validationErrors = Object.values(err.response.data.errors).flat();
        error.value = (validationErrors as string[]).join(', ');
      } else {
        error.value = err.response?.data?.message || 'Stok hareketi oluşturulurken hata oluştu';
      }
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const updateMovement = async (id: string, data: Partial<InventoryMovement>) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.put(`/inventory-movements/${id}`, data);
      const index = movements.value.findIndex(m => m.id === id);
      if (index !== -1) movements.value[index] = response.data;
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Stok hareketi güncellenirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const deleteMovement = async (id: string) => {
    loading.value = true;
    error.value = null;
    try {
      await api.delete(`/inventory-movements/${id}`);
      movements.value = movements.value.filter(m => m.id !== id);
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Stok hareketi silinirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    movements,
    currentMovement,
    loading,
    error,
    fetchMovements,
    fetchMovement,
    createMovement,
    updateMovement,
    deleteMovement
  };
});
