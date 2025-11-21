<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface CouponServiceInterface
{
    public function validateCoupon(string $code, ?string $customerId = null, ?float $amount = null): array;
    public function applyCoupon(string $code, string $customerId, array $metadata);
    public function getCouponUsage(string $couponId);
    public function getCustomerCouponUsage(string $customerId);
    public function canCustomerUseCoupon(string $code, string $customerId): bool;
    public function calculateDiscount(string $code, float $amount): float;
}
