<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\CouponUsage;
use App\Repositories\Contracts\CouponUsageRepositoryInterface;

class CouponUsageRepository extends BaseRepository implements CouponUsageRepositoryInterface
{
    public function __construct(CouponUsage $model)
    {
        parent::__construct($model);
    }

    public function findByCoupon(string $couponId)
    {
        return $this->model->where('coupon_id', $couponId)
            ->with(['customer', 'appointment', 'sale'])
            ->orderBy('used_at', 'desc')
            ->get();
    }

    public function findByCustomer(string $customerId)
    {
        return $this->model->where('customer_id', $customerId)
            ->with(['coupon', 'appointment', 'sale'])
            ->orderBy('used_at', 'desc')
            ->get();
    }

    public function getUsageCount(string $couponId, ?string $customerId = null)
    {
        $query = $this->model->where('coupon_id', $couponId);
        
        if ($customerId) {
            $query->where('customer_id', $customerId);
        }
        
        return $query->count();
    }

    public function getTotalDiscount(string $couponId, ?string $startDate = null, ?string $endDate = null)
    {
        $query = $this->model->where('coupon_id', $couponId);
        
        if ($startDate && $endDate) {
            $query->whereBetween('used_at', [$startDate, $endDate]);
        }
        
        return $query->sum('discount_amount');
    }
}
