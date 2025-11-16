<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface LoyaltyPointServiceInterface
{
    public function getCustomerBalance(string $customerId): int;
    public function awardPoints(string $customerId, int $points, string $reason, array $metadata = []);
    public function redeemPoints(string $customerId, int $points, string $reason, array $metadata = []);
    public function getPointsHistory(string $customerId, int $perPage = 15);
    public function getExpiringPoints(string $customerId, int $days = 30);
    public function expirePoints();
    public function calculatePointsForPurchase(float $amount, ?string $customerId = null): int;
}
