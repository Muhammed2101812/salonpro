import { describe, it, expect, vi, beforeEach } from 'vitest';
import { useToast, type Toast } from './useToast';

describe('useToast', () => {
    beforeEach(() => {
        vi.useFakeTimers();
    });

    it('should add a success toast', () => {
        const { toasts, success } = useToast();

        success('İşlem başarılı!');

        expect(toasts.value.length).toBe(1);
        expect(toasts.value[0].type).toBe('success');
        expect(toasts.value[0].message).toBe('İşlem başarılı!');
    });

    it('should add an error toast', () => {
        const { toasts, error } = useToast();

        error('Bir hata oluştu');

        expect(toasts.value.length).toBeGreaterThan(0);
        const errorToast = toasts.value.find((t: Toast) => t.type === 'error');
        expect(errorToast).toBeDefined();
        expect(errorToast?.message).toBe('Bir hata oluştu');
    });

    it('should dismiss a toast', () => {
        const { toasts, success, dismiss } = useToast();

        const id = success('Test mesajı');
        expect(toasts.value.length).toBeGreaterThan(0);

        dismiss(id);

        const found = toasts.value.find((t: Toast) => t.id === id);
        expect(found).toBeUndefined();
    });

    it('should auto-dismiss toast after duration', () => {
        const { toasts, info } = useToast();

        info('Otomatik kapanacak');
        const initialLength = toasts.value.length;

        // Fast forward time
        vi.advanceTimersByTime(6000);

        expect(toasts.value.length).toBeLessThan(initialLength);
    });
});
