import { ref, computed } from 'vue'
import { useBranchStore } from '@/stores/branch'
import { useCustomerStore } from '@/stores/customer'
import { useEmployeeStore } from '@/stores/employee'
import { useProductStore } from '@/stores/product'
import { useServiceStore } from '@/stores/service'

/**
 * Composable for managing relationship selects in forms
 * Provides formatted options for foreign key dropdowns
 */

// Branch Relationship
export function useBranchOptions() {
  const store = useBranchStore()
  const loading = ref(false)
  const error = ref<string | null>(null)

  const options = computed(() =>
    (store.branches || []).map(branch => ({
      value: branch.id,
      label: branch.name
    }))
  )

  const fetchBranches = async () => {
    loading.value = true
    error.value = null
    try {
      await store.fetchBranches()
    } catch (e: any) {
      error.value = e.message || 'Şubeler yüklenirken hata oluştu'
    } finally {
      loading.value = false
    }
  }

  return {
    options,
    loading,
    error,
    fetch: fetchBranches
  }
}

// Customer Relationship
export function useCustomerOptions() {
  const store = useCustomerStore()
  const loading = ref(false)
  const error = ref<string | null>(null)

  const options = computed(() =>
    (store.customers || []).map(customer => ({
      value: customer.id,
      label: `${customer.first_name} ${customer.last_name}` + (customer.phone ? ` (${customer.phone})` : '')
    }))
  )

  const fetchCustomers = async () => {
    loading.value = true
    error.value = null
    try {
      await store.fetchCustomers()
    } catch (e: any) {
      error.value = e.message || 'Müşteriler yüklenirken hata oluştu'
    } finally {
      loading.value = false
    }
  }

  return {
    options,
    loading,
    error,
    fetch: fetchCustomers
  }
}

// Employee Relationship
export function useEmployeeOptions() {
  const store = useEmployeeStore()
  const loading = ref(false)
  const error = ref<string | null>(null)

  const options = computed(() =>
    (store.employees || []).map(employee => ({
      value: employee.id,
      label: `${employee.first_name} ${employee.last_name}` + (employee.title ? ` - ${employee.title}` : '')
    }))
  )

  const fetchEmployees = async () => {
    loading.value = true
    error.value = null
    try {
      await store.fetchEmployees()
    } catch (e: any) {
      error.value = e.message || 'Çalışanlar yüklenirken hata oluştu'
    } finally {
      loading.value = false
    }
  }

  return {
    options,
    loading,
    error,
    fetch: fetchEmployees
  }
}

// Product Relationship
export function useProductOptions() {
  const store = useProductStore()
  const loading = ref(false)
  const error = ref<string | null>(null)

  const options = computed(() =>
    (store.products || []).map(product => ({
      value: product.id,
      label: product.name + (product.sku ? ` (${product.sku})` : '')
    }))
  )

  const fetchProducts = async () => {
    loading.value = true
    error.value = null
    try {
      await store.fetchProducts()
    } catch (e: any) {
      error.value = e.message || 'Ürünler yüklenirken hata oluştu'
    } finally {
      loading.value = false
    }
  }

  return {
    options,
    loading,
    error,
    fetch: fetchProducts
  }
}

// Service Relationship
export function useServiceOptions() {
  const store = useServiceStore()
  const loading = ref(false)
  const error = ref<string | null>(null)

  const options = computed(() =>
    (store.services || []).map(service => ({
      value: service.id,
      label: service.name + (service.duration_minutes ? ` (${service.duration_minutes} dk)` : '')
    }))
  )

  const fetchServices = async () => {
    loading.value = true
    error.value = null
    try {
      await store.fetchServices()
    } catch (e: any) {
      error.value = e.message || 'Hizmetler yüklenirken hata oluştu'
    } finally {
      loading.value = false
    }
  }

  return {
    options,
    loading,
    error,
    fetch: fetchServices
  }
}

// Active Employees (for assignments, appointments)
export function useActiveEmployeeOptions() {
  const store = useEmployeeStore()
  const loading = ref(false)
  const error = ref<string | null>(null)

  const options = computed(() =>
    (store.employees || [])
      .filter(employee => employee.is_active)
      .map(employee => ({
        value: employee.id,
        label: `${employee.first_name} ${employee.last_name}` + (employee.title ? ` - ${employee.title}` : '')
      }))
  )

  const fetchEmployees = async () => {
    loading.value = true
    error.value = null
    try {
      await store.fetchEmployees()
    } catch (e: any) {
      error.value = e.message || 'Çalışanlar yüklenirken hata oluştu'
    } finally {
      loading.value = false
    }
  }

  return {
    options,
    loading,
    error,
    fetch: fetchEmployees
  }
}

// Active Services (for appointments, packages)
export function useActiveServiceOptions() {
  const store = useServiceStore()
  const loading = ref(false)
  const error = ref<string | null>(null)

  const options = computed(() =>
    (store.services || [])
      .filter(service => service.is_active)
      .map(service => ({
        value: service.id,
        label: service.name + (service.price ? ` - ${service.price} TL` : '')
      }))
  )

  const fetchServices = async () => {
    loading.value = true
    error.value = null
    try {
      await store.fetchServices()
    } catch (e: any) {
      error.value = e.message || 'Hizmetler yüklenirken hata oluştu'
    } finally {
      loading.value = false
    }
  }

  return {
    options,
    loading,
    error,
    fetch: fetchServices
  }
}

// In-Stock Products (for sales, appointments)
export function useInStockProductOptions() {
  const store = useProductStore()
  const loading = ref(false)
  const error = ref<string | null>(null)

  const options = computed(() =>
    (store.products || [])
      .filter(product => product.is_active && product.stock_quantity > 0)
      .map(product => ({
        value: product.id,
        label: `${product.name} (Stok: ${product.stock_quantity})`
      }))
  )

  const fetchProducts = async () => {
    loading.value = true
    error.value = null
    try {
      await store.fetchProducts()
    } catch (e: any) {
      error.value = e.message || 'Ürünler yüklenirken hata oluştu'
    } finally {
      loading.value = false
    }
  }

  return {
    options,
    loading,
    error,
    fetch: fetchProducts
  }
}

// Generic relationship hook for custom use cases
export function useGenericOptions<T>(
  items: T[],
  valueKey: keyof T,
  labelKey: keyof T,
  filterFn?: (item: T) => boolean
) {
  return computed(() =>
    (filterFn ? items.filter(filterFn) : items).map(item => ({
      value: item[valueKey],
      label: item[labelKey]
    }))
  )
}
