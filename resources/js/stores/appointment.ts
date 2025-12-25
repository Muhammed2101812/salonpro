import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';

const CACHE_TTL = 5 * 60 * 1000; // 5 minutes cache

interface Branch {
  id: string;
  name: string;
  code: string;
}

interface Customer {
  id: string;
  first_name: string;
  last_name: string;
  phone: string;
  email?: string;
}

interface Employee {
  id: string;
  first_name: string;
  last_name: string;
  phone: string;
}

interface Service {
  id: string;
  name: string;
  price: string | number;
  duration_minutes: number;
}

interface Appointment {
  id: string;
  branch_id: string;
  customer_id: string;
  employee_id: string;
  service_id: string;
  appointment_date: string;
  duration_minutes: number;
  price: string | number;
  status: 'pending' | 'confirmed' | 'cancelled' | 'completed';
  notes?: string;
  branch?: Branch;
  customer?: Customer;
  employee?: Employee;
  service?: Service;
  created_at?: string;
  updated_at?: string;
}

export const useAppointmentStore = defineStore('appointment', () => {
  const appointments = ref<Appointment[]>([]);
  const currentAppointment = ref<Appointment | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);
  const lastFetched = ref<number>(0);

  const fetchAppointments = async (params?: any, forceRefresh = false) => {
    // Return cached data if still valid
    const now = Date.now();
    if (!forceRefresh && appointments.value.length > 0 && (now - lastFetched.value) < CACHE_TTL) {
      return { data: appointments.value };
    }

    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get('/appointments', params);
      appointments.value = response.data;
      lastFetched.value = Date.now();
      return response;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Randevular yüklenirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchAppointment = async (id: string) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.get(`/appointments/${id}`);
      currentAppointment.value = response.data;
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Randevu yüklenirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const createAppointment = async (data: Partial<Appointment>) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.post('/appointments', data);
      appointments.value.push(response.data);
      return response.data;
    } catch (err: any) {
      // Handle validation errors
      if (err.response?.data?.errors) {
        const validationErrors = Object.values(err.response.data.errors).flat();
        error.value = (validationErrors as string[]).join(', ');
      } else {
        error.value = err.response?.data?.message || 'Randevu oluşturulurken hata oluştu';
      }
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const updateAppointment = async (id: string, data: Partial<Appointment>) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.put(`/appointments/${id}`, data);
      const index = appointments.value.findIndex(a => a.id === id);
      if (index !== -1) appointments.value[index] = response.data;
      return response.data;
    } catch (err: any) {
      // Handle validation errors
      if (err.response?.data?.errors) {
        const validationErrors = Object.values(err.response.data.errors).flat();
        error.value = (validationErrors as string[]).join(', ');
      } else {
        error.value = err.response?.data?.message || 'Randevu güncellenirken hata oluştu';
      }
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const deleteAppointment = async (id: string) => {
    loading.value = true;
    error.value = null;
    try {
      await api.delete(`/appointments/${id}`);
      appointments.value = appointments.value.filter(a => a.id !== id);
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Randevu silinirken hata oluştu';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const updateStatus = async (id: string, status: Appointment['status']) => {
    return updateAppointment(id, { status });
  };

  return {
    appointments,
    currentAppointment,
    loading,
    error,
    fetchAppointments,
    fetchAppointment,
    createAppointment,
    updateAppointment,
    deleteAppointment,
    updateStatus
  };
});
