import { defineStore } from 'pinia'
import api from '@/services/api'

export const useReferralStore = defineStore('referral', {
  state: () => ({
    items: [] as any[],
    loading: false,
    error: null as string | null,
  }),

  actions: {
    async fetchAll(params = {}) {
      this.loading = true
      try {
        const response = await api.get('/eferrals', { params })
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
        const response = await api.get(`/eferrals/${id}`)
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
        const response = await api.post('/eferrals', data)
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
        const response = await api.put(`/eferrals/${id}`, data)
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
        await api.delete(`/eferrals/${id}`)
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