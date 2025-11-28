<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreMarketingCampaignRequest;
use App\Http\Requests\UpdateMarketingCampaignRequest;
use App\Http\Resources\MarketingCampaignResource;
use App\Services\MarketingCampaignService;
use App\Models\MarketingCampaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MarketingCampaignController extends BaseController
{
    public function __construct(
        protected MarketingCampaignService $marketingCampaignService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', MarketingCampaign::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $marketingCampaigns = $this->marketingCampaignService->getPaginated($perPage);

            return $this->sendPaginated(
                MarketingCampaignResource::collection($marketingCampaigns),
                'MarketingCampaigns başarıyla getirildi'
            );
        }

        $marketingCampaigns = $this->marketingCampaignService->getAll();

        return MarketingCampaignResource::collection($marketingCampaigns);
    }

    public function store(StoreMarketingCampaignRequest $request): JsonResponse
    {
        $this->authorize('create', MarketingCampaign::class);

        $marketingCampaign = $this->marketingCampaignService->create($request->validated());

        return $this->sendSuccess(
            new MarketingCampaignResource($marketingCampaign),
            'MarketingCampaign başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $marketingCampaign = $this->marketingCampaignService->findByIdOrFail($id);

        return $this->sendSuccess(
            new MarketingCampaignResource($marketingCampaign),
            'MarketingCampaign başarıyla getirildi'
        );
    }

    public function update(UpdateMarketingCampaignRequest $request, string $id): JsonResponse
    {
        $marketingCampaign = $this->marketingCampaignService->update($id, $request->validated());

        return $this->sendSuccess(
            new MarketingCampaignResource($marketingCampaign),
            'MarketingCampaign başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->marketingCampaignService->delete($id);

        return $this->sendSuccess(
            null,
            'MarketingCampaign başarıyla silindi'
        );
    }
}
