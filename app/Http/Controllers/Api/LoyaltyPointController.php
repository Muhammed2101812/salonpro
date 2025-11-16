<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Contracts\LoyaltyPointServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoyaltyPointController extends Controller
{
    public function __construct(
        private LoyaltyPointServiceInterface $loyaltyPointService
    ) {}

    public function balance(string $customerId): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\LoyaltyPoint::class);

        $balance = $this->loyaltyPointService->getCustomerBalance($customerId);

        return response()->json([
            'customer_id' => $customerId,
            'balance' => $balance,
        ]);
    }

    public function history(Request $request, string $customerId): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\LoyaltyPoint::class);

        $history = $this->loyaltyPointService->getPointsHistory(
            $customerId,
            $request->input('per_page', 15)
        );

        return response()->json($history);
    }

    public function award(Request $request, string $customerId): JsonResponse
    {
        $this->authorize('create', \App\Models\LoyaltyPoint::class);

        $validated = $request->validate([
            'points' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|uuid',
            'expires_at' => 'nullable|date',
        ]);

        $transaction = $this->loyaltyPointService->awardPoints(
            $customerId,
            $validated['points'],
            $validated['reason'],
            [
                'reference_type' => $validated['reference_type'] ?? null,
                'reference_id' => $validated['reference_id'] ?? null,
                'expires_at' => $validated['expires_at'] ?? null,
            ]
        );

        return response()->json($transaction, 201);
    }

    public function redeem(Request $request, string $customerId): JsonResponse
    {
        $this->authorize('create', \App\Models\LoyaltyPoint::class);

        $validated = $request->validate([
            'points' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|uuid',
        ]);

        try {
            $transaction = $this->loyaltyPointService->redeemPoints(
                $customerId,
                $validated['points'],
                $validated['reason'],
                [
                    'reference_type' => $validated['reference_type'] ?? null,
                    'reference_id' => $validated['reference_id'] ?? null,
                ]
            );

            return response()->json($transaction, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function expiringPoints(Request $request, string $customerId): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\LoyaltyPoint::class);

        $days = $request->input('days', 30);
        $expiringPoints = $this->loyaltyPointService->getExpiringPoints($customerId, $days);

        return response()->json($expiringPoints);
    }

    public function calculatePoints(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'customer_id' => 'nullable|uuid|exists:customers,id',
        ]);

        $points = $this->loyaltyPointService->calculatePointsForPurchase(
            $validated['amount'],
            $validated['customer_id'] ?? null
        );

        return response()->json([
            'amount' => $validated['amount'],
            'points' => $points,
        ]);
    }
}
