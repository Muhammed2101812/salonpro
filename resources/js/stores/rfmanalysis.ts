/**
 * RFM Analysis Store - Alias for CustomerRfmAnalysis
 * Bu dosya customerrfmanalysis.ts'yi yeniden export eder
 */
import { defineStore } from 'pinia'
import api from '@/services/api'

export const useRfmAnalysisStore = defineStore('rfmanalysis', {
    state: () => ({
        items: [] as any[],
        loading: false,
        error: null as string | null,
    }),

    actions: {
        async fetchAll(params = {}) {
            this.loading = true
            try {
                const response = await api.get('/customer-rfm-analyses', { params })
                this.items = response.data?.data || response.data || []
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
                const response = await api.get(`/customer-rfm-analyses/${id}`)
                return response.data?.data || response.data
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
                const response = await api.post('/customer-rfm-analyses', data)
                return response.data?.data || response.data
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
                const response = await api.put(`/customer-rfm-analyses/${id}`, data)
                return response.data?.data || response.data
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
                await api.delete(`/customer-rfm-analyses/${id}`)
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
