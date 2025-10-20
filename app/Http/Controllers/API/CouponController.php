<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Http\Resources\CouponResource;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CouponController extends BaseController
{
    public function __construct(
        protected CouponService $couponService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $coupons = $this->couponService->getPaginated($perPage);

            return $this->sendPaginated(
                CouponResource::collection($coupons),
                'Coupons başarıyla getirildi'
            );
        }

        $coupons = $this->couponService->getAll();

        return CouponResource::collection($coupons);
    }

    public function store(StoreCouponRequest $request): JsonResponse
    {
        $coupon = $this->couponService->create($request->validated());

        return $this->sendSuccess(
            new CouponResource($coupon),
            'Coupon başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $coupon = $this->couponService->findByIdOrFail($id);

        return $this->sendSuccess(
            new CouponResource($coupon),
            'Coupon başarıyla getirildi'
        );
    }

    public function update(UpdateCouponRequest $request, string $id): JsonResponse
    {
        $coupon = $this->couponService->update($id, $request->validated());

        return $this->sendSuccess(
            new CouponResource($coupon),
            'Coupon başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->couponService->delete($id);

        return $this->sendSuccess(
            null,
            'Coupon başarıyla silindi'
        );
    }
}
