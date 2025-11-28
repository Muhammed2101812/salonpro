<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreServicePricingRuleRequest;
use App\Http\Requests\UpdateServicePricingRuleRequest;
use App\Http\Resources\ServicePricingRuleResource;
use App\Services\ServicePricingRuleService;
use App\Models\ServicePricingRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServicePricingRuleController extends BaseController
{
    public function __construct(
        protected ServicePricingRuleService $servicePricingRuleService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', ServicePricingRule::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $servicePricingRules = $this->servicePricingRuleService->getPaginated($perPage);

            return $this->sendPaginated(
                ServicePricingRuleResource::collection($servicePricingRules),
                'ServicePricingRules başarıyla getirildi'
            );
        }

        $servicePricingRules = $this->servicePricingRuleService->getAll();

        return ServicePricingRuleResource::collection($servicePricingRules);
    }

    public function store(StoreServicePricingRuleRequest $request): JsonResponse
    {
        $this->authorize('create', ServicePricingRule::class);

        $servicePricingRule = $this->servicePricingRuleService->create($request->validated());

        return $this->sendSuccess(
            new ServicePricingRuleResource($servicePricingRule),
            'ServicePricingRule başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $servicePricingRule = $this->servicePricingRuleService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ServicePricingRuleResource($servicePricingRule),
            'ServicePricingRule başarıyla getirildi'
        );
    }

    public function update(UpdateServicePricingRuleRequest $request, string $id): JsonResponse
    {
        $servicePricingRule = $this->servicePricingRuleService->update($id, $request->validated());

        return $this->sendSuccess(
            new ServicePricingRuleResource($servicePricingRule),
            'ServicePricingRule başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->servicePricingRuleService->delete($id);

        return $this->sendSuccess(
            null,
            'ServicePricingRule başarıyla silindi'
        );
    }
}
