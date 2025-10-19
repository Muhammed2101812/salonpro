import { defineStore } from 'pinia';
import api from '../services/api';

export interface Setting {
  id: string;
  key: string;
  value: string;
  type: 'string' | 'number' | 'boolean' | 'json';
  description?: string;
}

interface SettingState {
  settings: Setting[];
  loading: boolean;
  error: string | null;
}

export const useSettingStore = defineStore('setting', {
  state: (): SettingState => ({
    settings: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchSettings() {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.get('/settings');
        this.settings = response.data;
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Ayarlar yüklenemedi';
        console.error('Ayarlar listesi hatası:', error);
      } finally {
        this.loading = false;
      }
    },

    async createSetting(settingData: Partial<Setting>) {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.post('/settings', settingData);
        this.settings.push(response.data.data);
        return response.data.data;
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Ayar eklenemedi';
        console.error('Ayar ekleme hatası:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateSetting(id: string, settingData: Partial<Setting>) {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.put(`/settings/${id}`, settingData);
        const index = this.settings.findIndex(s => s.id === id);
        if (index !== -1) {
          this.settings[index] = response.data.data;
        }
        return response.data.data;
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Ayar güncellenemedi';
        console.error('Ayar güncelleme hatası:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    getSetting(key: string): Setting | undefined {
      return this.settings.find(s => s.key === key);
    },

    getSettingValue(key: string, defaultValue: any = null): any {
      const setting = this.getSetting(key);
      if (!setting) return defaultValue;

      switch (setting.type) {
        case 'number':
          return parseFloat(setting.value) || defaultValue;
        case 'boolean':
          return setting.value === 'true' || setting.value === '1';
        case 'json':
          try {
            return JSON.parse(setting.value);
          } catch {
            return defaultValue;
          }
        default:
          return setting.value || defaultValue;
      }
    },
  },
});
