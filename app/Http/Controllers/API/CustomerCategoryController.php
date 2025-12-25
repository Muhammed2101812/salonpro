<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCustomerCategoryRequest;
use App\Http\Requests\UpdateCustomerCategoryRequest;
use App\Http\Resources\CustomerCategoryResource;
use App\Services\CustomerCategoryService;
use App\Models\CustomerCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class CustomerCategoryController extends BaseController
{
    public function __construct(
        protected CustomerCategoryService $customerCategoryService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', CustomerCategory::class);

        $branchId = Auth::user()->branch_id;
        $customerCategories = $this->customerCategoryService->getAllCategories($branchId);

        return CustomerCategoryResource::collection($customerCategories);
    }

    public function store(StoreCustomerCategoryRequest $request): JsonResponse
    {
        $this->authorize('create', CustomerCategory::class);

        $data = $request->validated();
        $data['branch_id'] = Auth::user()->branch_id;

        $customerCategory = $this->customerCategoryService->createCategory($data);

        return $this->sendSuccess(
            new CustomerCategoryResource($customerCategory),
            'CustomerCategory başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $customerCategory = CustomerCategory::findOrFail($id);
        $this->authorize('view', $customerCategory);

        return $this->sendSuccess(
            new CustomerCategoryResource($customerCategory),
            'CustomerCategory başarıyla getirildi'
        );
    }

    public function update(UpdateCustomerCategoryRequest $request, string $id): JsonResponse
    {
        $customerCategory = CustomerCategory::findOrFail($id);
        $this->authorize('update', $customerCategory);

        $this->customerCategoryService->updateCategory($id, $request->validated());
        $customerCategory->refresh();

        return $this->sendSuccess(
            new CustomerCategoryResource($customerCategory),
            'CustomerCategory başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $customerCategory = CustomerCategory::findOrFail($id);
        $this->authorize('delete', $customerCategory);

        $this->customerCategoryService->deleteCategory($id);

        return $this->sendSuccess(
            null,
            'CustomerCategory başarıyla silindi'
        );
    }
}

