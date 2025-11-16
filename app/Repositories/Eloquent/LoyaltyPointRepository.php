<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\LoyaltyPoint;
use App\Repositories\Contracts\LoyaltyPointRepositoryInterface;

class LoyaltyPointRepository extends BaseRepository implements LoyaltyPointRepositoryInterface
{
    public function __construct(LoyaltyPoint $model)
    {
        parent::__construct($model);
    }

    public function findByCustomer(string $customerId)
    {
        return $this->model->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getCustomerBalance(string $customerId)
    {
        return $this->model->where('customer_id', $customerId)->sum('points');
    }

    public function addPoints(string $customerId, int $points, string $transactionType, array $data = [])
    {
        return $this->create([
            'customer_id' => $customerId,
            'points' => $points,
            'transaction_type' => $transactionType,
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'description' => $data['description'] ?? null,
            'expires_at' => $data['expires_at'] ?? now()->addYear(),
        ]);
    }

    public function deductPoints(string $customerId, int $points, string $transactionType, array $data = [])
    {
        return $this->addPoints($customerId, -$points, $transactionType, $data);
    }

    public function getExpiringPoints(string $customerId, int $days = 30)
    {
        return $this->model->where('customer_id', $customerId)
            ->where('points', '>', 0)
            ->whereBetween('expires_at', [now(), now()->addDays($days)])
            ->sum('points');
    }
}
