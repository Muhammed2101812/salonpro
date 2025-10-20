<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreLeadActivityRequest;
use App\Http\Requests\UpdateLeadActivityRequest;
use App\Http\Resources\LeadActivityResource;
use App\Services\LeadActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LeadActivityController extends BaseController
{
    public function __construct(
        protected LeadActivityService $leadActivityService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $leadActivities = $this->leadActivityService->getPaginated($perPage);

            return $this->sendPaginated(
                LeadActivityResource::collection($leadActivities),
                'LeadActivities başarıyla getirildi'
            );
        }

        $leadActivities = $this->leadActivityService->getAll();

        return LeadActivityResource::collection($leadActivities);
    }

    public function store(StoreLeadActivityRequest $request): JsonResponse
    {
        $leadActivity = $this->leadActivityService->create($request->validated());

        return $this->sendSuccess(
            new LeadActivityResource($leadActivity),
            'LeadActivity başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $leadActivity = $this->leadActivityService->findByIdOrFail($id);

        return $this->sendSuccess(
            new LeadActivityResource($leadActivity),
            'LeadActivity başarıyla getirildi'
        );
    }

    public function update(UpdateLeadActivityRequest $request, string $id): JsonResponse
    {
        $leadActivity = $this->leadActivityService->update($id, $request->validated());

        return $this->sendSuccess(
            new LeadActivityResource($leadActivity),
            'LeadActivity başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->leadActivityService->delete($id);

        return $this->sendSuccess(
            null,
            'LeadActivity başarıyla silindi'
        );
    }
}
