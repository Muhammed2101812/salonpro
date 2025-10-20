<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCustomFieldValueRequest;
use App\Http\Requests\UpdateCustomFieldValueRequest;
use App\Http\Resources\CustomFieldValueResource;
use App\Services\CustomFieldValueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomFieldValueController extends BaseController
{
    public function __construct(
        protected CustomFieldValueService $customFieldValueService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $customFieldValues = $this->customFieldValueService->getPaginated($perPage);

            return $this->sendPaginated(
                CustomFieldValueResource::collection($customFieldValues),
                'CustomFieldValues başarıyla getirildi'
            );
        }

        $customFieldValues = $this->customFieldValueService->getAll();

        return CustomFieldValueResource::collection($customFieldValues);
    }

    public function store(StoreCustomFieldValueRequest $request): JsonResponse
    {
        $customFieldValue = $this->customFieldValueService->create($request->validated());

        return $this->sendSuccess(
            new CustomFieldValueResource($customFieldValue),
            'CustomFieldValue başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $customFieldValue = $this->customFieldValueService->findByIdOrFail($id);

        return $this->sendSuccess(
            new CustomFieldValueResource($customFieldValue),
            'CustomFieldValue başarıyla getirildi'
        );
    }

    public function update(UpdateCustomFieldValueRequest $request, string $id): JsonResponse
    {
        $customFieldValue = $this->customFieldValueService->update($id, $request->validated());

        return $this->sendSuccess(
            new CustomFieldValueResource($customFieldValue),
            'CustomFieldValue başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->customFieldValueService->delete($id);

        return $this->sendSuccess(
            null,
            'CustomFieldValue başarıyla silindi'
        );
    }
}
