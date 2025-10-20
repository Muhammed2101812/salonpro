<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCustomerFeedbackRequest;
use App\Http\Requests\UpdateCustomerFeedbackRequest;
use App\Http\Resources\CustomerFeedbackResource;
use App\Services\CustomerFeedbackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerFeedbackController extends BaseController
{
    public function __construct(
        protected CustomerFeedbackService $customerFeedbackService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $customerFeedbacks = $this->customerFeedbackService->getPaginated($perPage);

            return $this->sendPaginated(
                CustomerFeedbackResource::collection($customerFeedbacks),
                'CustomerFeedbacks başarıyla getirildi'
            );
        }

        $customerFeedbacks = $this->customerFeedbackService->getAll();

        return CustomerFeedbackResource::collection($customerFeedbacks);
    }

    public function store(StoreCustomerFeedbackRequest $request): JsonResponse
    {
        $customerFeedback = $this->customerFeedbackService->create($request->validated());

        return $this->sendSuccess(
            new CustomerFeedbackResource($customerFeedback),
            'CustomerFeedback başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $customerFeedback = $this->customerFeedbackService->findByIdOrFail($id);

        return $this->sendSuccess(
            new CustomerFeedbackResource($customerFeedback),
            'CustomerFeedback başarıyla getirildi'
        );
    }

    public function update(UpdateCustomerFeedbackRequest $request, string $id): JsonResponse
    {
        $customerFeedback = $this->customerFeedbackService->update($id, $request->validated());

        return $this->sendSuccess(
            new CustomerFeedbackResource($customerFeedback),
            'CustomerFeedback başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->customerFeedbackService->delete($id);

        return $this->sendSuccess(
            null,
            'CustomerFeedback başarıyla silindi'
        );
    }
}
