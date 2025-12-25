import { onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';

interface KeyboardShortcut {
    key: string;
    ctrl?: boolean;
    alt?: boolean;
    shift?: boolean;
    description: string;
    handler: () => void;
}

const registeredShortcuts: KeyboardShortcut[] = [];

/**
 * Global keyboard shortcuts composable
 * 
 * @example
 * // In a component
 * useKeyboardShortcuts([
 *   { key: 'n', ctrl: true, description: 'Yeni kayıt', handler: () => openNewModal() },
 *   { key: 's', ctrl: true, description: 'Kaydet', handler: () => save() },
 * ]);
 */
export function useKeyboardShortcuts(shortcuts?: KeyboardShortcut[]) {
    const router = useRouter();

    // Default navigation shortcuts
    const defaultShortcuts: KeyboardShortcut[] = [
        { key: 'd', alt: true, description: 'Dashboard', handler: () => router.push('/') },
        { key: 'c', alt: true, description: 'Müşteriler', handler: () => router.push('/customers') },
        { key: 'a', alt: true, description: 'Randevular', handler: () => router.push('/appointments') },
        { key: 's', alt: true, description: 'Satışlar', handler: () => router.push('/sales') },
        { key: 'p', alt: true, description: 'Ürünler', handler: () => router.push('/products') },
    ];

    const allShortcuts = [...defaultShortcuts, ...(shortcuts || [])];

    const handleKeyDown = (event: KeyboardEvent) => {
        // Ignore if typing in input/textarea
        const target = event.target as HTMLElement;
        if (['INPUT', 'TEXTAREA', 'SELECT'].includes(target.tagName)) {
            // Allow Escape in inputs
            if (event.key !== 'Escape') return;
        }

        for (const shortcut of allShortcuts) {
            const keyMatch = event.key.toLowerCase() === shortcut.key.toLowerCase();
            const ctrlMatch = shortcut.ctrl ? (event.ctrlKey || event.metaKey) : true;
            const altMatch = shortcut.alt ? event.altKey : !event.altKey;
            const shiftMatch = shortcut.shift ? event.shiftKey : !event.shiftKey;

            if (keyMatch && ctrlMatch && altMatch && shiftMatch) {
                event.preventDefault();
                shortcut.handler();
                return;
            }
        }
    };

    onMounted(() => {
        window.addEventListener('keydown', handleKeyDown);
        registeredShortcuts.push(...allShortcuts);
    });

    onUnmounted(() => {
        window.removeEventListener('keydown', handleKeyDown);
        // Clean up registered shortcuts
        allShortcuts.forEach(s => {
            const index = registeredShortcuts.findIndex(r => r.key === s.key && r.description === s.description);
            if (index > -1) registeredShortcuts.splice(index, 1);
        });
    });

    return {
        shortcuts: allShortcuts,
        registeredShortcuts,
    };
}

/**
 * Get all registered shortcuts for help display
 */
export function getRegisteredShortcuts() {
    return registeredShortcuts;
}

export default useKeyboardShortcuts;
