<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends BaseController
{
    public function __construct(
        protected CustomerService $customerService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $customers = $this->customerService->getPaginated($perPage);

            return $this->sendPaginated(
                CustomerResource::collection($customers),
                'Customers retrieved successfully'
            );
        }

        $customers = $this->customerService->getAll();

        return CustomerResource::collection($customers);
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = $this->customerService->create($request->validated());

        return $this->sendSuccess(
            new CustomerResource($customer),
            'Customer created successfully',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $customer = $this->customerService->findByIdOrFail($id);

        return $this->sendSuccess(
            new CustomerResource($customer),
            'Customer retrieved successfully'
        );
    }

    public function update(UpdateCustomerRequest $request, string $id): JsonResponse
    {
        $customer = $this->customerService->update($id, $request->validated());

        return $this->sendSuccess(
            new CustomerResource($customer),
            'Customer updated successfully'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->customerService->delete($id);

        return $this->sendSuccess(
            null,
            'Customer deleted successfully'
        );
    }

    public function restore(string $id): JsonResponse
    {
        $this->customerService->restore($id);

        return $this->sendSuccess(
            null,
            'Customer restored successfully'
        );
    }

    public function forceDestroy(string $id): JsonResponse
    {
        $this->customerService->forceDelete($id);

        return $this->sendSuccess(
            null,
            'Customer permanently deleted'
        );
    }
}
