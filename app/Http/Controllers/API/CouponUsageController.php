<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCouponUsageRequest;
use App\Http\Requests\UpdateCouponUsageRequest;
use App\Http\Resources\CouponUsageResource;
use App\Services\CouponUsageService;
use App\Models\CouponUsage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CouponUsageController extends BaseController
{
    public function __construct(
        protected CouponUsageService $couponUsageService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', CouponUsage::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $couponUsages = $this->couponUsageService->getPaginated($perPage);

            return $this->sendPaginated(
                CouponUsageResource::collection($couponUsages),
                'CouponUsages başarıyla getirildi'
            );
        }

        $couponUsages = $this->couponUsageService->getAll();

        return CouponUsageResource::collection($couponUsages);
    }

    public function store(StoreCouponUsageRequest $request): JsonResponse
    {
        $this->authorize('create', CouponUsage::class);

        $couponUsage = $this->couponUsageService->create($request->validated());

        return $this->sendSuccess(
            new CouponUsageResource($couponUsage),
            'CouponUsage başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $couponUsage = $this->couponUsageService->findByIdOrFail($id);

        return $this->sendSuccess(
            new CouponUsageResource($couponUsage),
            'CouponUsage başarıyla getirildi'
        );
    }

    public function update(UpdateCouponUsageRequest $request, string $id): JsonResponse
    {
        $couponUsage = $this->couponUsageService->update($id, $request->validated());

        return $this->sendSuccess(
            new CouponUsageResource($couponUsage),
            'CouponUsage başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->couponUsageService->delete($id);

        return $this->sendSuccess(
            null,
            'CouponUsage başarıyla silindi'
        );
    }
}
