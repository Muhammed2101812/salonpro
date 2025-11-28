<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCustomFieldRequest;
use App\Http\Requests\UpdateCustomFieldRequest;
use App\Http\Resources\CustomFieldResource;
use App\Services\CustomFieldService;
use App\Models\CustomField;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomFieldController extends BaseController
{
    public function __construct(
        protected CustomFieldService $customFieldService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', CustomField::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $customFields = $this->customFieldService->getPaginated($perPage);

            return $this->sendPaginated(
                CustomFieldResource::collection($customFields),
                'CustomFields başarıyla getirildi'
            );
        }

        $customFields = $this->customFieldService->getAll();

        return CustomFieldResource::collection($customFields);
    }

    public function store(StoreCustomFieldRequest $request): JsonResponse
    {
        $this->authorize('create', CustomField::class);

        $customField = $this->customFieldService->create($request->validated());

        return $this->sendSuccess(
            new CustomFieldResource($customField),
            'CustomField başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $customField = $this->customFieldService->findByIdOrFail($id);

        return $this->sendSuccess(
            new CustomFieldResource($customField),
            'CustomField başarıyla getirildi'
        );
    }

    public function update(UpdateCustomFieldRequest $request, string $id): JsonResponse
    {
        $customField = $this->customFieldService->update($id, $request->validated());

        return $this->sendSuccess(
            new CustomFieldResource($customField),
            'CustomField başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->customFieldService->delete($id);

        return $this->sendSuccess(
            null,
            'CustomField başarıyla silindi'
        );
    }
}
