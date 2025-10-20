import { defineStore } from 'pinia'
import api from '@/services/api'

export const useServiceCategoryStore = defineStore('servicecategory', {
  state: () => ({
    items: [] as any[],
    loading: false,
    error: null as string | null,
  }),

  actions: {
    async fetchAll(params = {}) {
      this.loading = true
      try {
        const response = await api.get('/ervicecategories', { params })
        this.items = response.data.data
        return response.data
      } catch (error: any) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchOne(id: string) {
      this.loading = true
      try {
        const response = await api.get(`/ervicecategories/${id}`)
        return response.data.data
      } catch (error: any) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async create(data: any) {
      this.loading = true
      try {
        const response = await api.post('/ervicecategories', data)
        return response.data.data
      } catch (error: any) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async update(id: string, data: any) {
      this.loading = true
      try {
        const response = await api.put(`/ervicecategories/${id}`, data)
        return response.data.data
      } catch (error: any) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async delete(id: string) {
      this.loading = true
      try {
        await api.delete(`/ervicecategories/${id}`)
        this.items = this.items.filter(item => item.id !== id)
      } catch (error: any) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },
  },
})