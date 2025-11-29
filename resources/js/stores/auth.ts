import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '@/services/api';

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem('auth_token'));
  const user = ref<any>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const isAuthenticated = computed(() => !!token.value);

  const login = async (email: string, password: string) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.post('/login', { email, password });
      // response = { success, message, data: { user, token } }
      token.value = response.data.token;
      user.value = response.data.user;
      console.log('Login response:', response);
      console.log('Token:', token.value);
      if (token.value) {
        localStorage.setItem('auth_token', token.value);
      }
      return true;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Login failed';
      return false;
    } finally {
      loading.value = false;
    }
  };

  const register = async (data: any) => {
    loading.value = true;
    error.value = null;
    try {
      const response: any = await api.post('/register', data);
      token.value = response.data.token;
      user.value = response.data.user;
      localStorage.setItem('auth_token', token.value!);
      return true;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Registration failed';
      return false;
    } finally {
      loading.value = false;
    }
  };

  const logout = async () => {
    try {
      await api.post('/logout');
    } catch (err) {
      console.error('Logout error:', err);
    }
    token.value = null;
    user.value = null;
    localStorage.removeItem('auth_token');
  };

  const fetchProfile = async () => {
    if (!token.value) return;
    try {
      const response: any = await api.get('/profile');
      user.value = response.data;
    } catch (err) {
      console.error('Fetch profile error:', err);
    }
  };

  return { token, user, loading, error, isAuthenticated, login, register, logout, fetchProfile };
});
