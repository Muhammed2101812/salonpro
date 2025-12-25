<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCustomerTagRequest;
use App\Http\Requests\UpdateCustomerTagRequest;
use App\Http\Resources\CustomerTagResource;
use App\Services\CustomerTagService;
use App\Models\CustomerTag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class CustomerTagController extends BaseController
{
    public function __construct(
        protected CustomerTagService $customerTagService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', CustomerTag::class);

        $branchId = Auth::user()->branch_id;
        $customerTags = $this->customerTagService->getAllTags($branchId);

        return CustomerTagResource::collection($customerTags);
    }

    public function store(StoreCustomerTagRequest $request): JsonResponse
    {
        $this->authorize('create', CustomerTag::class);

        $data = $request->validated();
        $data['branch_id'] = Auth::user()->branch_id;

        $customerTag = $this->customerTagService->createTag($data);

        return $this->sendSuccess(
            new CustomerTagResource($customerTag),
            'CustomerTag başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $customerTag = CustomerTag::findOrFail($id);
        $this->authorize('view', $customerTag);

        return $this->sendSuccess(
            new CustomerTagResource($customerTag),
            'CustomerTag başarıyla getirildi'
        );
    }

    public function update(UpdateCustomerTagRequest $request, string $id): JsonResponse
    {
        $customerTag = CustomerTag::findOrFail($id);
        $this->authorize('update', $customerTag);

        $this->customerTagService->updateTag($id, $request->validated());
        $customerTag->refresh();

        return $this->sendSuccess(
            new CustomerTagResource($customerTag),
            'CustomerTag başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $customerTag = CustomerTag::findOrFail($id);
        $this->authorize('delete', $customerTag);

        $this->customerTagService->deleteTag($id);

        return $this->sendSuccess(
            null,
            'CustomerTag başarıyla silindi'
        );
    }
}

