import { defineStore } from 'pinia';
import api from '../services/api';

export interface SaleItem {
  id?: string;
  product_id?: string;
  service_id?: string;
  product?: any;
  service?: any;
  quantity: number;
  price: number;
  total: number;
}

export interface Sale {
  id: string;
  customer_id: string;
  customer?: any;
  employee_id: string;
  employee?: any;
  sale_date: string;
  subtotal: number;
  discount: number;
  tax: number;
  total: number;
  notes?: string;
  items?: SaleItem[];
  created_at: string;
  updated_at: string;
}

interface SaleState {
  sales: Sale[];
  loading: boolean;
  error: string | null;
}

export const useSaleStore = defineStore('sale', {
  state: (): SaleState => ({
    sales: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchSales() {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.get('/sales');
        this.sales = response.data;
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Satışlar yüklenemedi';
        console.error('Satış listesi hatası:', error);
      } finally {
        this.loading = false;
      }
    },

    async createSale(saleData: Partial<Sale>) {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.post('/sales', saleData);
        this.sales.unshift(response.data.data);
        return response.data.data;
      } catch (error: any) {
        // Handle validation errors
        if (error.response?.data?.errors) {
          const validationErrors = Object.values(error.response.data.errors).flat();
          this.error = (validationErrors as string[]).join(', ');
        } else {
          this.error = error.response?.data?.message || 'Satış eklenemedi';
        }
        console.error('Satış ekleme hatası:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateSale(id: string, saleData: Partial<Sale>) {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.put(`/sales/${id}`, saleData);
        const index = this.sales.findIndex(s => s.id === id);
        if (index !== -1) {
          this.sales[index] = response.data.data;
        }
        return response.data.data;
      } catch (error: any) {
        // Handle validation errors
        if (error.response?.data?.errors) {
          const validationErrors = Object.values(error.response.data.errors).flat();
          this.error = (validationErrors as string[]).join(', ');
        } else {
          this.error = error.response?.data?.message || 'Satış güncellenemedi';
        }
        console.error('Satış güncelleme hatası:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async deleteSale(id: string) {
      this.loading = true;
      this.error = null;
      try {
        await api.delete(`/sales/${id}`);
        this.sales = this.sales.filter(s => s.id !== id);
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Satış silinemedi';
        console.error('Satış silme hatası:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },
  },
});
