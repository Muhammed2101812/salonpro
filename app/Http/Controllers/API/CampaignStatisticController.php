<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCampaignStatisticRequest;
use App\Http\Requests\UpdateCampaignStatisticRequest;
use App\Http\Resources\CampaignStatisticResource;
use App\Services\CampaignStatisticService;
use App\Models\CampaignStatistic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CampaignStatisticController extends BaseController
{
    public function __construct(
        protected CampaignStatisticService $campaignStatisticService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', CampaignStatistic::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $campaignStatistics = $this->campaignStatisticService->getPaginated($perPage);

            return $this->sendPaginated(
                CampaignStatisticResource::collection($campaignStatistics),
                'CampaignStatistics başarıyla getirildi'
            );
        }

        $campaignStatistics = $this->campaignStatisticService->getAll();

        return CampaignStatisticResource::collection($campaignStatistics);
    }

    public function store(StoreCampaignStatisticRequest $request): JsonResponse
    {
        $this->authorize('create', CampaignStatistic::class);

        $campaignStatistic = $this->campaignStatisticService->create($request->validated());

        return $this->sendSuccess(
            new CampaignStatisticResource($campaignStatistic),
            'CampaignStatistic başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $campaignStatistic = $this->campaignStatisticService->findByIdOrFail($id);

        return $this->sendSuccess(
            new CampaignStatisticResource($campaignStatistic),
            'CampaignStatistic başarıyla getirildi'
        );
    }

    public function update(UpdateCampaignStatisticRequest $request, string $id): JsonResponse
    {
        $campaignStatistic = $this->campaignStatisticService->update($id, $request->validated());

        return $this->sendSuccess(
            new CampaignStatisticResource($campaignStatistic),
            'CampaignStatistic başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->campaignStatisticService->delete($id);

        return $this->sendSuccess(
            null,
            'CampaignStatistic başarıyla silindi'
        );
    }
}
