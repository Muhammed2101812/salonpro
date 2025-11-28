<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockAuditRequest;
use App\Http\Requests\UpdateStockAuditRequest;
use App\Services\StockAuditService;
use App\Models\StockAudit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockAuditController extends Controller
{
    public function __construct(
        protected StockAuditService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', StockAudit::class);

        try {
            $perPage = $request->input('per_page', 15);
            $filters = $request->only(['branch_id', 'status', 'start_date', 'end_date', 'search']);
            
            $audits = $this->service->getPaginatedAudits($perPage, $filters);
            
            return response()->json([
                'success' => true,
                'data' => $audits,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stok sayımları listelenemedi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreStockAuditRequest $request): JsonResponse
    {
        $this->authorize('create', StockAudit::class);

        try {
            $audit = $this->service->createAudit($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Stok sayımı başarıyla oluşturuldu.',
                'data' => $audit,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stok sayımı oluşturulamadı.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $audit = $this->service->getAuditById($id);
            
            if (!$audit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok sayımı bulunamadı.',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $audit,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stok sayımı getirilemedi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateStockAuditRequest $request, int $id): JsonResponse
    {
        try {
            $audit = $this->service->updateAudit($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Stok sayımı başarıyla güncellendi.',
                'data' => $audit,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stok sayımı güncellenemedi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->deleteAudit($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Stok sayımı başarıyla silindi.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stok sayımı silinemedi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function pending(): JsonResponse
    {
        try {
            $audits = $this->service->getPendingAudits();
            
            return response()->json([
                'success' => true,
                'data' => $audits,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bekleyen sayımlar listelenemedi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function complete(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
            'apply_adjustments' => ['boolean'],
        ]);

        try {
            $audit = $this->service->completeAudit($id, $request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Stok sayımı başarıyla tamamlandı.',
                'data' => $audit,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stok sayımı tamamlanamadı.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'İptal nedeni zorunludur.',
            'reason.max' => 'İptal nedeni en fazla 500 karakter olabilir.',
        ]);

        try {
            $audit = $this->service->cancelAudit($id, $request->input('reason'));
            
            return response()->json([
                'success' => true,
                'message' => 'Stok sayımı başarıyla iptal edildi.',
                'data' => $audit,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stok sayımı iptal edilemedi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function variances(int $id): JsonResponse
    {
        try {
            $variances = $this->service->calculateVariances($id);
            
            return response()->json([
                'success' => true,
                'data' => $variances,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Farklar hesaplanamadı.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function byBranch(int $branchId): JsonResponse
    {
        try {
            $audits = $this->service->getAuditsByBranch($branchId);
            
            return response()->json([
                'success' => true,
                'data' => $audits,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Şube sayımları listelenemedi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
