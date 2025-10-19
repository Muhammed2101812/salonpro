<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentController extends BaseController
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $payments = $this->paymentService->getPaginated($perPage);

            return $this->sendPaginated(
                PaymentResource::collection($payments),
                'Payments retrieved successfully'
            );
        }

        $payments = $this->paymentService->getAll();

        return PaymentResource::collection($payments);
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        try {
            $payment = $this->paymentService->create($request->validated());

            return $this->sendSuccess(
                new PaymentResource($payment->load(['customer', 'appointment', 'sale'])),
                'Payment created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->sendError('Failed to create payment: '.$e->getMessage(), [], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $payment = $this->paymentService->findByIdOrFail($id);

            return $this->sendSuccess(
                new PaymentResource($payment->load(['customer', 'appointment', 'sale'])),
                'Payment retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->sendError('Payment not found', [], 404);
        }
    }

    public function update(UpdatePaymentRequest $request, string $id): JsonResponse
    {
        $payment = $this->paymentService->update($id, $request->validated());

        return $this->sendSuccess(
            new PaymentResource($payment->load(['customer', 'appointment', 'sale'])),
            'Payment updated successfully'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->paymentService->delete($id);

        return $this->sendSuccess(
            null,
            'Payment deleted successfully'
        );
    }

    public function restore(string $id): JsonResponse
    {
        $this->paymentService->restore($id);

        return $this->sendSuccess(
            null,
            'Payment restored successfully'
        );
    }

    public function forceDestroy(string $id): JsonResponse
    {
        $this->paymentService->forceDelete($id);

        return $this->sendSuccess(
            null,
            'Payment permanently deleted'
        );
    }
}
