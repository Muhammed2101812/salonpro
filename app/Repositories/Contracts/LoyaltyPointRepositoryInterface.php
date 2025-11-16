<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface LoyaltyPointRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCustomer(string $customerId);
    public function getCustomerBalance(string $customerId);
    public function addPoints(string $customerId, int $points, string $transactionType, array $data = []);
    public function deductPoints(string $customerId, int $points, string $transactionType, array $data = []);
    public function getExpiringPoints(string $customerId, int $days = 30);
}
