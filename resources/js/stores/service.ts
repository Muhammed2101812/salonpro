import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';

interface ServiceCategory {
  id: string;
  name: { tr: string; en?: string };
  description?: string;
  is_active: boolean;
  services_count?: number;
  created_at?: string;
  updated_at?: string;
}

interface Service {
  id: string;
  service_category_id: string;
  name: { tr: string; en?: string };
  description?: string;
  price: string | number;
  duration_minutes: number;
  is_active: boolean;
  service_category?: ServiceCategory;
  created_at?: string;
  updated_at?: string;
}

export const useServiceStore = defineStore('service', () => {
  const services = ref<Service[]>([]);
  const categories = ref<ServiceCategory[]>([]);
  const currentService = ref<Service | null>(null);
  const currentCategory = ref<ServiceCategory | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  // Service Category Methods
  const fetchCategories = async (params?: any) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get('/service-categories', params);
      categories.value = response.data;
      return response;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Kategoriler yüklenirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchCategory = async (id: string) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get(`/service-categories/${id}`);
      currentCategory.value = response.data;
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Kategori yüklenirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const createCategory = async (data: Partial<ServiceCategory>) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.post('/service-categories', data);
      categories.value.push(response.data);
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Kategori oluşturulurken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const updateCategory = async (id: string, data: Partial<ServiceCategory>) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.put(`/service-categories/${id}`, data);
      const index = categories.value.findIndex(c => c.id === id);
      if (index !== -1) categories.value[index] = response.data;
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Kategori güncellenirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const deleteCategory = async (id: string) => {
    loading.value = true;
    error.value = null;
    try {
      await api.delete(`/service-categories/${id}`);
      categories.value = categories.value.filter(c => c.id !== id);
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Kategori silinirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Service Methods
  const fetchServices = async (params?: any) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get('/services', params);
      services.value = response.data;
      return response;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Hizmetler yüklenirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchService = async (id: string) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get(`/services/${id}`);
      currentService.value = response.data;
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Hizmet yüklenirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const createService = async (data: Partial<Service>) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.post('/services', data);
      services.value.push(response.data);
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Hizmet oluşturulurken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const updateService = async (id: string, data: Partial<Service>) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.put(`/services/${id}`, data);
      const index = services.value.findIndex(s => s.id === id);
      if (index !== -1) services.value[index] = response.data;
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Hizmet güncellenirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const deleteService = async (id: string) => {
    loading.value = true;
    error.value = null;
    try {
      await api.delete(`/services/${id}`);
      services.value = services.value.filter(s => s.id !== id);
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Hizmet silinirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    services,
    categories,
    currentService,
    currentCategory,
    loading,
    error,
    fetchCategories,
    fetchCategory,
    createCategory,
    updateCategory,
    deleteCategory,
    fetchServices,
    fetchService,
    createService,
    updateService,
    deleteService
  };
});
