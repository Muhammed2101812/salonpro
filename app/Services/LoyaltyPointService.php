<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\LoyaltyPointRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Services\Contracts\LoyaltyPointServiceInterface;
use Illuminate\Support\Facades\DB;

class LoyaltyPointService implements LoyaltyPointServiceInterface
{
    public function __construct(
        private LoyaltyPointRepositoryInterface $loyaltyPointRepository,
        private CustomerRepositoryInterface $customerRepository
    ) {}

    public function getCustomerBalance(string $customerId): int
    {
        return $this->loyaltyPointRepository->getCustomerBalance($customerId);
    }

    public function awardPoints(string $customerId, int $points, string $reason, array $metadata = [])
    {
        $customer = $this->customerRepository->findOrFail($customerId);

        return $this->loyaltyPointRepository->addPoints(
            $customerId,
            $points,
            'earned',
            [
                'description' => $reason,
                'reference_type' => $metadata['reference_type'] ?? null,
                'reference_id' => $metadata['reference_id'] ?? null,
                'expires_at' => $metadata['expires_at'] ?? now()->addYear(),
            ]
        );
    }

    public function redeemPoints(string $customerId, int $points, string $reason, array $metadata = [])
    {
        $balance = $this->getCustomerBalance($customerId);

        if ($balance < $points) {
            throw new \Exception('Insufficient loyalty points balance');
        }

        return $this->loyaltyPointRepository->deductPoints(
            $customerId,
            $points,
            'redeemed',
            [
                'description' => $reason,
                'reference_type' => $metadata['reference_type'] ?? null,
                'reference_id' => $metadata['reference_id'] ?? null,
            ]
        );
    }

    public function getPointsHistory(string $customerId, int $perPage = 15)
    {
        return $this->loyaltyPointRepository->findByCustomer($customerId)
            ->paginate($perPage);
    }

    public function getExpiringPoints(string $customerId, int $days = 30)
    {
        return $this->loyaltyPointRepository->getExpiringPoints($customerId, $days);
    }

    public function expirePoints()
    {
        // Find all expired points that haven't been processed
        $expiredPoints = DB::table('loyalty_points')
            ->where('expires_at', '<=', now())
            ->where('transaction_type', 'earned')
            ->whereNull('expired_at')
            ->get();

        $count = 0;
        foreach ($expiredPoints as $point) {
            // Create expiration transaction
            $this->loyaltyPointRepository->create([
                'customer_id' => $point->customer_id,
                'points' => -abs($point->points),
                'transaction_type' => 'expired',
                'reference_type' => 'App\Models\LoyaltyPoint',
                'reference_id' => $point->id,
                'description' => 'Points expired',
            ]);

            // Mark original transaction as expired
            DB::table('loyalty_points')
                ->where('id', $point->id)
                ->update(['expired_at' => now()]);

            $count++;
        }

        return $count;
    }

    public function calculatePointsForPurchase(float $amount, ?string $customerId = null): int
    {
        // Base calculation: 1 point per currency unit spent
        $basePoints = (int) floor($amount);

        // Check if customer has VIP status for bonus multiplier
        if ($customerId) {
            $customer = $this->customerRepository->find($customerId);
            if ($customer && $customer->is_vip) {
                // VIP customers get 1.5x points
                $basePoints = (int) floor($basePoints * 1.5);
            }
        }

        return $basePoints;
    }
}
