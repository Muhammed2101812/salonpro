<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCustomerTagRequest;
use App\Http\Requests\UpdateCustomerTagRequest;
use App\Http\Resources\CustomerTagResource;
use App\Services\CustomerTagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerTagController extends BaseController
{
    public function __construct(
        protected CustomerTagService $customerTagService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $customerTags = $this->customerTagService->getPaginated($perPage);

            return $this->sendPaginated(
                CustomerTagResource::collection($customerTags),
                'CustomerTags başarıyla getirildi'
            );
        }

        $customerTags = $this->customerTagService->getAll();

        return CustomerTagResource::collection($customerTags);
    }

    public function store(StoreCustomerTagRequest $request): JsonResponse
    {
        $customerTag = $this->customerTagService->create($request->validated());

        return $this->sendSuccess(
            new CustomerTagResource($customerTag),
            'CustomerTag başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $customerTag = $this->customerTagService->findByIdOrFail($id);

        return $this->sendSuccess(
            new CustomerTagResource($customerTag),
            'CustomerTag başarıyla getirildi'
        );
    }

    public function update(UpdateCustomerTagRequest $request, string $id): JsonResponse
    {
        $customerTag = $this->customerTagService->update($id, $request->validated());

        return $this->sendSuccess(
            new CustomerTagResource($customerTag),
            'CustomerTag başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->customerTagService->delete($id);

        return $this->sendSuccess(
            null,
            'CustomerTag başarıyla silindi'
        );
    }
}
