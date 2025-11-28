<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CouponUsageResource;
use App\Services\Contracts\CouponServiceInterface;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CouponController extends Controller
{
    public function __construct(
        private CouponServiceInterface $couponService
    ) {}

    public function validate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'customer_id' => 'nullable|uuid|exists:customers,id',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $validation = $this->couponService->validateCoupon(
            $validated['code'],
            $validated['customer_id'] ?? null,
            $validated['amount'] ?? null
        );

        return response()->json($validation);
    }

    public function apply(Request $request): JsonResponse
    {
        $this->authorize('create', \App\Models\CouponUsage::class);

        $validated = $request->validate([
            'code' => 'required|string',
            'customer_id' => 'required|uuid|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'appointment_id' => 'nullable|uuid|exists:appointments,id',
            'sale_id' => 'nullable|uuid|exists:sales,id',
        ]);

        try {
            $usage = $this->couponService->applyCoupon(
                $validated['code'],
                $validated['customer_id'],
                [
                    'amount' => $validated['amount'],
                    'appointment_id' => $validated['appointment_id'] ?? null,
                    'sale_id' => $validated['sale_id'] ?? null,
                ]
            );

            return CouponUsageResource::make($usage)->response()->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function usage(string $couponId): AnonymousResourceCollection
    {
        $this->authorize('viewAny', \App\Models\CouponUsage::class);

        $usage = $this->couponService->getCouponUsage($couponId);

        return CouponUsageResource::collection($usage);
    }

    public function customerUsage(string $customerId): AnonymousResourceCollection
    {
        $this->authorize('viewAny', \App\Models\CouponUsage::class);

        $usage = $this->couponService->getCustomerCouponUsage($customerId);

        return CouponUsageResource::collection($usage);
    }

    public function calculateDiscount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $discount = $this->couponService->calculateDiscount(
            $validated['code'],
            $validated['amount']
        );

        return response()->json([
            'code' => $validated['code'],
            'amount' => $validated['amount'],
            'discount' => $discount,
            'final_amount' => $validated['amount'] - $discount,
        ]);
    }
}
