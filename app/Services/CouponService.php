<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CouponUsageRepositoryInterface;
use App\Services\Contracts\CouponServiceInterface;
use Illuminate\Support\Facades\DB;

class CouponService implements CouponServiceInterface
{
    public function __construct(
        private CouponUsageRepositoryInterface $couponUsageRepository
    ) {}

    public function validateCoupon(string $code, ?string $customerId = null, ?float $amount = null): array
    {
        $coupon = DB::table('coupons')->where('code', $code)->first();

        if (!$coupon) {
            return [
                'valid' => false,
                'message' => 'Coupon code not found',
            ];
        }

        // Check if active
        if (!$coupon->is_active) {
            return [
                'valid' => false,
                'message' => 'This coupon is no longer active',
            ];
        }

        // Check dates
        $now = now();
        if ($coupon->start_date && $now->lt($coupon->start_date)) {
            return [
                'valid' => false,
                'message' => 'This coupon is not yet valid',
            ];
        }

        if ($coupon->end_date && $now->gt($coupon->end_date)) {
            return [
                'valid' => false,
                'message' => 'This coupon has expired',
            ];
        }

        // Check minimum purchase amount
        if ($amount && $coupon->minimum_purchase && $amount < $coupon->minimum_purchase) {
            return [
                'valid' => false,
                'message' => "Minimum purchase amount of {$coupon->minimum_purchase} required",
            ];
        }

        // Check usage limits
        if ($coupon->usage_limit) {
            $totalUsage = $this->couponUsageRepository->getUsageCount($coupon->id);
            if ($totalUsage >= $coupon->usage_limit) {
                return [
                    'valid' => false,
                    'message' => 'This coupon has reached its usage limit',
                ];
            }
        }

        // Check per-customer usage limit
        if ($customerId && $coupon->usage_limit_per_customer) {
            $customerUsage = $this->couponUsageRepository->getUsageCount($coupon->id, $customerId);
            if ($customerUsage >= $coupon->usage_limit_per_customer) {
                return [
                    'valid' => false,
                    'message' => 'You have already used this coupon the maximum number of times',
                ];
            }
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'message' => 'Coupon is valid',
        ];
    }

    public function applyCoupon(string $code, string $customerId, array $metadata)
    {
        $validation = $this->validateCoupon($code, $customerId, $metadata['amount'] ?? null);

        if (!$validation['valid']) {
            throw new \Exception($validation['message']);
        }

        $coupon = $validation['coupon'];
        $discount = $this->calculateDiscount($code, $metadata['amount']);

        return $this->couponUsageRepository->create([
            'coupon_id' => $coupon->id,
            'customer_id' => $customerId,
            'appointment_id' => $metadata['appointment_id'] ?? null,
            'sale_id' => $metadata['sale_id'] ?? null,
            'discount_amount' => $discount,
            'used_at' => now(),
        ]);
    }

    public function getCouponUsage(string $couponId)
    {
        return $this->couponUsageRepository->findByCoupon($couponId);
    }

    public function getCustomerCouponUsage(string $customerId)
    {
        return $this->couponUsageRepository->findByCustomer($customerId);
    }

    public function canCustomerUseCoupon(string $code, string $customerId): bool
    {
        $validation = $this->validateCoupon($code, $customerId);

        return $validation['valid'];
    }

    public function calculateDiscount(string $code, float $amount): float
    {
        $coupon = DB::table('coupons')->where('code', $code)->first();

        if (!$coupon) {
            return 0;
        }

        $discount = match($coupon->discount_type) {
            'percentage' => ($amount * $coupon->discount_value) / 100,
            'fixed' => $coupon->discount_value,
            default => 0,
        };

        // Apply maximum discount limit if set
        if ($coupon->max_discount_amount && $discount > $coupon->max_discount_amount) {
            $discount = $coupon->max_discount_amount;
        }

        return round($discount, 2);
    }
}
