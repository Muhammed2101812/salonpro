<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentService;
use App\Models\Payment;
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
        $this->authorize('viewAny', Payment::class);

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
        $this->authorize('create', Payment::class);

        try {
            $payment = $this->paymentService->create($request->validated());

        $this->authorize('view', $payment);


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

        $this->authorize('update', $payment);


        return $this->sendSuccess(
            new PaymentResource($payment->load(['customer', 'appointment', 'sale'])),
            'Payment updated successfully'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $payment = $this->paymentService->findByIdOrFail($id);

        $this->authorize('delete', $payment);

        $this->paymentService->delete($id);

        return $this->sendSuccess(
            null,
            'Payment deleted successfully'
        );
    }

    public function restore(string $id): JsonResponse
    {
        $payment = $this->paymentService->findByIdOrFail($id);

        $this->authorize('restore', $payment);

        $this->paymentService->restore($id);

        return $this->sendSuccess(
            null,
            'Payment restored successfully'
        );
    }

    public function forceDestroy(string $id): JsonResponse
    {
        $payment = $this->paymentService->findByIdOrFail($id);

        $this->authorize('forceDelete', $payment);

        $this->paymentService->forceDelete($id);

        return $this->sendSuccess(
            null,
            'Payment permanently deleted'
        );
    }
}
