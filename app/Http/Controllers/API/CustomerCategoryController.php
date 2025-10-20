<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCustomerCategoryRequest;
use App\Http\Requests\UpdateCustomerCategoryRequest;
use App\Http\Resources\CustomerCategoryResource;
use App\Services\CustomerCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerCategoryController extends BaseController
{
    public function __construct(
        protected CustomerCategoryService $customerCategoryService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $customerCategories = $this->customerCategoryService->getPaginated($perPage);

            return $this->sendPaginated(
                CustomerCategoryResource::collection($customerCategories),
                'CustomerCategories başarıyla getirildi'
            );
        }

        $customerCategories = $this->customerCategoryService->getAll();

        return CustomerCategoryResource::collection($customerCategories);
    }

    public function store(StoreCustomerCategoryRequest $request): JsonResponse
    {
        $customerCategory = $this->customerCategoryService->create($request->validated());

        return $this->sendSuccess(
            new CustomerCategoryResource($customerCategory),
            'CustomerCategory başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $customerCategory = $this->customerCategoryService->findByIdOrFail($id);

        return $this->sendSuccess(
            new CustomerCategoryResource($customerCategory),
            'CustomerCategory başarıyla getirildi'
        );
    }

    public function update(UpdateCustomerCategoryRequest $request, string $id): JsonResponse
    {
        $customerCategory = $this->customerCategoryService->update($id, $request->validated());

        return $this->sendSuccess(
            new CustomerCategoryResource($customerCategory),
            'CustomerCategory başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->customerCategoryService->delete($id);

        return $this->sendSuccess(
            null,
            'CustomerCategory başarıyla silindi'
        );
    }
}
