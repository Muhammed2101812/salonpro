<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreFeatureFlagRequest;
use App\Http\Requests\UpdateFeatureFlagRequest;
use App\Http\Resources\FeatureFlagResource;
use App\Services\FeatureFlagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeatureFlagController extends BaseController
{
    public function __construct(
        protected FeatureFlagService $featureFlagService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $featureFlags = $this->featureFlagService->getPaginated($perPage);

            return $this->sendPaginated(
                FeatureFlagResource::collection($featureFlags),
                'FeatureFlags başarıyla getirildi'
            );
        }

        $featureFlags = $this->featureFlagService->getAll();

        return FeatureFlagResource::collection($featureFlags);
    }

    public function store(StoreFeatureFlagRequest $request): JsonResponse
    {
        $featureFlag = $this->featureFlagService->create($request->validated());

        return $this->sendSuccess(
            new FeatureFlagResource($featureFlag),
            'FeatureFlag başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $featureFlag = $this->featureFlagService->findByIdOrFail($id);

        return $this->sendSuccess(
            new FeatureFlagResource($featureFlag),
            'FeatureFlag başarıyla getirildi'
        );
    }

    public function update(UpdateFeatureFlagRequest $request, string $id): JsonResponse
    {
        $featureFlag = $this->featureFlagService->update($id, $request->validated());

        return $this->sendSuccess(
            new FeatureFlagResource($featureFlag),
            'FeatureFlag başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->featureFlagService->delete($id);

        return $this->sendSuccess(
            null,
            'FeatureFlag başarıyla silindi'
        );
    }
}
