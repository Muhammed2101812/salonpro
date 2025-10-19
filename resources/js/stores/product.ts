import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';

interface Product {
  id: string;
  name: string;
  description?: string;
  barcode?: string;
  sku?: string;
  price: string | number;
  cost_price?: string | number;
  stock_quantity: number;
  min_stock_quantity: number;
  unit: string;
  category?: string;
  is_active: boolean;
  is_low_stock?: boolean;
  is_out_of_stock?: boolean;
  created_at?: string;
  updated_at?: string;
}

export const useProductStore = defineStore('product', () => {
  const products = ref<Product[]>([]);
  const currentProduct = ref<Product | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const fetchProducts = async (params?: any) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get('/products', params);
      products.value = response.data;
      return response;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Ürünler yüklenirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchProduct = async (id: string) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get(`/products/${id}`);
      currentProduct.value = response.data;
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Ürün yüklenirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const createProduct = async (data: Partial<Product>) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.post('/products', data);
      products.value.push(response.data);
      return response.data;
    } catch (err: any) {
      // Handle validation errors
      if (err.response?.data?.errors) {
        const validationErrors = Object.values(err.response.data.errors).flat();
        error.value = (validationErrors as string[]).join(', ');
      } else {
        error.value = err.response?.data?.message || 'Ürün oluşturulurken hata oluştu';
      }
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const updateProduct = async (id: string, data: Partial<Product>) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.put(`/products/${id}`, data);
      const index = products.value.findIndex(p => p.id === id);
      if (index !== -1) products.value[index] = response.data;
      return response.data;
    } catch (err: any) {
      // Handle validation errors
      if (err.response?.data?.errors) {
        const validationErrors = Object.values(err.response.data.errors).flat();
        error.value = (validationErrors as string[]).join(', ');
      } else {
        error.value = err.response?.data?.message || 'Ürün güncellenirken hata oluştu';
      }
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const deleteProduct = async (id: string) => {
    loading.value = true;
    error.value = null;
    try {
      await api.delete(`/products/${id}`);
      products.value = products.value.filter(p => p.id !== id);
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Ürün silinirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    products,
    currentProduct,
    loading,
    error,
    fetchProducts,
    fetchProduct,
    createProduct,
    updateProduct,
    deleteProduct
  };
});
