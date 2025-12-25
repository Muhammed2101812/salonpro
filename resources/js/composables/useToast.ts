import { ref, Ref, shallowRef, computed } from 'vue';

export interface ToastOptions {
    type?: 'success' | 'error' | 'warning' | 'info';
    title?: string;
    message: string;
    duration?: number;
    dismissible?: boolean;
}

export interface Toast extends ToastOptions {
    id: string;
    createdAt: number;
}

const toasts = ref<Toast[]>([]);
let toastId = 0;

/**
 * Toast notification composable
 * Provides methods to show toast notifications
 * 
 * @example
 * const { success, error } = useToast();
 * success('Kayıt başarılı!');
 * error('Bir hata oluştu');
 */
export function useToast() {
    const add = (options: ToastOptions) => {
        const id = `toast-${++toastId}`;
        const toast: Toast = {
            id,
            type: options.type || 'info',
            title: options.title,
            message: options.message,
            duration: options.duration ?? 5000,
            dismissible: options.dismissible ?? true,
            createdAt: Date.now(),
        };

        toasts.value.push(toast);

        // Auto dismiss
        if (toast.duration > 0) {
            setTimeout(() => {
                dismiss(id);
            }, toast.duration);
        }

        return id;
    };

    const dismiss = (id: string) => {
        const index = toasts.value.findIndex(t => t.id === id);
        if (index !== -1) {
            toasts.value.splice(index, 1);
        }
    };

    const dismissAll = () => {
        toasts.value = [];
    };

    const success = (message: string, title?: string) => {
        return add({ type: 'success', message, title: title || 'Başarılı' });
    };

    const error = (message: string, title?: string) => {
        return add({ type: 'error', message, title: title || 'Hata', duration: 8000 });
    };

    const warning = (message: string, title?: string) => {
        return add({ type: 'warning', message, title: title || 'Uyarı' });
    };

    const info = (message: string, title?: string) => {
        return add({ type: 'info', message, title });
    };

    return {
        toasts: computed(() => toasts.value),
        add,
        dismiss,
        dismissAll,
        success,
        error,
        warning,
        info,
    };
}

export default useToast;
