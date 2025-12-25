import { describe, it, expect, vi, beforeEach } from 'vitest';
import { useApi, useCrud } from './useApi';
import api from '@/services/api';

// Mock the api module
vi.mock('@/services/api', () => ({
    default: {
        get: vi.fn(),
        post: vi.fn(),
        put: vi.fn(),
        delete: vi.fn(),
    },
}));

describe('useApi', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('should initialize with correct default values', () => {
        const { data, loading, error } = useApi(() => Promise.resolve('test'));

        expect(data.value).toBeNull();
        expect(loading.value).toBe(false);
        expect(error.value).toBeNull();
    });

    it('should execute request and update data', async () => {
        const mockData = { id: 1, name: 'Test' };
        const { data, loading, execute } = useApi(() => Promise.resolve(mockData));

        const result = await execute();

        expect(result).toEqual(mockData);
        expect(data.value).toEqual(mockData);
        expect(loading.value).toBe(false);
    });

    it('should handle errors', async () => {
        const errorMessage = 'Test error';
        const { error, execute } = useApi(() => Promise.reject({
            response: { data: { message: errorMessage } }
        }));

        await execute();

        expect(error.value).toBe(errorMessage);
    });

    it('should call onSuccess callback', async () => {
        const onSuccess = vi.fn();
        const mockData = { id: 1 };

        const { execute } = useApi(
            () => Promise.resolve(mockData),
            { onSuccess }
        );

        await execute();

        expect(onSuccess).toHaveBeenCalledWith(mockData);
    });

    it('should execute immediately when immediate option is true', async () => {
        const mockData = { id: 1 };
        const requestFn = vi.fn().mockResolvedValue(mockData);

        useApi(requestFn, { immediate: true });

        // Wait for the next tick
        await new Promise(resolve => setTimeout(resolve, 0));

        expect(requestFn).toHaveBeenCalled();
    });

    it('should reset state', () => {
        const { data, error, reset } = useApi(() => Promise.resolve({ id: 1 }));

        // Manually set values
        data.value = { id: 1 } as any;
        error.value = 'Some error';

        reset();

        expect(data.value).toBeNull();
        expect(error.value).toBeNull();
    });
});

describe('useCrud', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('should fetch all items', async () => {
        const mockItems = [{ id: '1' }, { id: '2' }];
        (api.get as any).mockResolvedValue({ data: mockItems });

        const { items, fetchAll } = useCrud('/test');

        await fetchAll();

        expect(api.get).toHaveBeenCalledWith('/test', undefined);
        expect(items.value).toEqual(mockItems);
    });

    it('should create an item', async () => {
        const newItem = { id: '3', name: 'New Item' };
        (api.post as any).mockResolvedValue({ data: newItem });

        const { items, create } = useCrud('/test');

        await create({ name: 'New Item' });

        expect(api.post).toHaveBeenCalledWith('/test', { name: 'New Item' });
        expect(items.value).toContainEqual(newItem);
    });

    it('should update an item', async () => {
        const updatedItem = { id: '1', name: 'Updated' };
        (api.put as any).mockResolvedValue({ data: updatedItem });

        const { items, update } = useCrud<{ id: string; name: string }>('/test');
        items.value = [{ id: '1', name: 'Original' }];

        await update('1', { name: 'Updated' });

        expect(api.put).toHaveBeenCalledWith('/test/1', { name: 'Updated' });
        expect(items.value[0].name).toBe('Updated');
    });

    it('should remove an item', async () => {
        (api.delete as any).mockResolvedValue({});

        const { items, remove } = useCrud<{ id: string }>('/test');
        items.value = [{ id: '1' }, { id: '2' }];

        await remove('1');

        expect(api.delete).toHaveBeenCalledWith('/test/1');
        expect(items.value.length).toBe(1);
        expect(items.value[0].id).toBe('2');
    });
});
