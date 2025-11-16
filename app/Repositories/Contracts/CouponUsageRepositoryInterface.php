<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface CouponUsageRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCoupon(string $couponId);
    public function findByCustomer(string $customerId);
    public function getUsageCount(string $couponId, ?string $customerId = null);
    public function getTotalDiscount(string $couponId, ?string $startDate = null, ?string $endDate = null);
}
