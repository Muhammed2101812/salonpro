<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreUserPreferenceRequest;
use App\Http\Requests\UpdateUserPreferenceRequest;
use App\Http\Resources\UserPreferenceResource;
use App\Services\UserPreferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserPreferenceController extends BaseController
{
    public function __construct(
        protected UserPreferenceService $userPreferenceService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $userPreferences = $this->userPreferenceService->getPaginated($perPage);

            return $this->sendPaginated(
                UserPreferenceResource::collection($userPreferences),
                'UserPreferences başarıyla getirildi'
            );
        }

        $userPreferences = $this->userPreferenceService->getAll();

        return UserPreferenceResource::collection($userPreferences);
    }

    public function store(StoreUserPreferenceRequest $request): JsonResponse
    {
        $userPreference = $this->userPreferenceService->create($request->validated());

        return $this->sendSuccess(
            new UserPreferenceResource($userPreference),
            'UserPreference başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $userPreference = $this->userPreferenceService->findByIdOrFail($id);

        return $this->sendSuccess(
            new UserPreferenceResource($userPreference),
            'UserPreference başarıyla getirildi'
        );
    }

    public function update(UpdateUserPreferenceRequest $request, string $id): JsonResponse
    {
        $userPreference = $this->userPreferenceService->update($id, $request->validated());

        return $this->sendSuccess(
            new UserPreferenceResource($userPreference),
            'UserPreference başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->userPreferenceService->delete($id);

        return $this->sendSuccess(
            null,
            'UserPreference başarıyla silindi'
        );
    }
}
