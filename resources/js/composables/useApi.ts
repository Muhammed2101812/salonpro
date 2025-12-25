import { ref, Ref, shallowRef } from 'vue';
import api from '@/services/api';

export interface UseApiOptions<T> {
    immediate?: boolean;
    initialData?: T;
    onSuccess?: (data: T) => void;
    onError?: (error: any) => void;
}

export interface UseApiReturn<T> {
    data: Ref<T | null>;
    loading: Ref<boolean>;
    error: Ref<string | null>;
    execute: (...args: any[]) => Promise<T | null>;
    reset: () => void;
}

/**
 * Reusable API composable for Vue components
 * Provides loading, error, and data states with automatic handling
 * 
 * @example
 * const { data, loading, error, execute } = useApi<Customer[]>(
 *   () => api.get('/customers'),
 *   { immediate: true }
 * );
 */
export function useApi<T>(
    requestFn: (...args: any[]) => Promise<T>,
    options: UseApiOptions<T> = {}
): UseApiReturn<T> {
    const { immediate = false, initialData = null, onSuccess, onError } = options;

    const data = shallowRef<T | null>(initialData as T | null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const execute = async (...args: any[]): Promise<T | null> => {
        loading.value = true;
        error.value = null;

        try {
            const result = await requestFn(...args);
            data.value = result;
            onSuccess?.(result);
            return result;
        } catch (err: any) {
            // Extract error message
            if (err.response?.data?.errors) {
                const validationErrors = Object.values(err.response.data.errors).flat();
                error.value = (validationErrors as string[]).join(', ');
            } else if (err.response?.data?.message) {
                error.value = err.response.data.message;
            } else if (err.message) {
                error.value = err.message;
            } else {
                error.value = 'Bir hata oluştu';
            }
            onError?.(err);
            return null;
        } finally {
            loading.value = false;
        }
    };

    const reset = () => {
        data.value = initialData as T | null;
        loading.value = false;
        error.value = null;
    };

    // Execute immediately if option is set
    if (immediate) {
        execute();
    }

    return {
        data: data as Ref<T | null>,
        loading,
        error,
        execute,
        reset,
    };
}

/**
 * Composable for CRUD operations on a resource
 * 
 * @example
 * const customers = useCrud<Customer>('/customers');
 * await customers.fetchAll();
 * await customers.create({ name: 'John' });
 */
export function useCrud<T extends { id?: string }>(resourceUrl: string) {
    const items = shallowRef<T[]>([]);
    const currentItem = shallowRef<T | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const handleError = (err: any) => {
        if (err.response?.data?.errors) {
            const validationErrors = Object.values(err.response.data.errors).flat();
            error.value = (validationErrors as string[]).join(', ');
        } else if (err.response?.data?.message) {
            error.value = err.response.data.message;
        } else {
            error.value = err.message || 'Bir hata oluştu';
        }
    };

    const fetchAll = async (params?: any) => {
        loading.value = true;
        error.value = null;
        try {
            const response: any = await api.get(resourceUrl, params);
            items.value = response.data || response;
            return response;
        } catch (err) {
            handleError(err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const fetchOne = async (id: string) => {
        loading.value = true;
        error.value = null;
        try {
            const response: any = await api.get(`${resourceUrl}/${id}`);
            currentItem.value = response.data || response;
            return currentItem.value;
        } catch (err) {
            handleError(err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const create = async (data: Partial<T>) => {
        loading.value = true;
        error.value = null;
        try {
            const response: any = await api.post(resourceUrl, data);
            const newItem = response.data || response;
            items.value = [...items.value, newItem];
            return newItem;
        } catch (err) {
            handleError(err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const update = async (id: string, data: Partial<T>) => {
        loading.value = true;
        error.value = null;
        try {
            const response: any = await api.put(`${resourceUrl}/${id}`, data);
            const updatedItem = response.data || response;
            const index = items.value.findIndex((item) => item.id === id);
            if (index !== -1) {
                items.value = [...items.value.slice(0, index), updatedItem, ...items.value.slice(index + 1)];
            }
            return updatedItem;
        } catch (err) {
            handleError(err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const remove = async (id: string) => {
        loading.value = true;
        error.value = null;
        try {
            await api.delete(`${resourceUrl}/${id}`);
            items.value = items.value.filter((item) => item.id !== id);
        } catch (err) {
            handleError(err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    return {
        items,
        currentItem,
        loading,
        error,
        fetchAll,
        fetchOne,
        create,
        update,
        remove,
    };
}

export default useApi;
