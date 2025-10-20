<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreLoyaltyPointRequest;
use App\Http\Requests\UpdateLoyaltyPointRequest;
use App\Http\Resources\LoyaltyPointResource;
use App\Services\LoyaltyPointService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LoyaltyPointController extends BaseController
{
    public function __construct(
        protected LoyaltyPointService $loyaltyPointService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $loyaltyPoints = $this->loyaltyPointService->getPaginated($perPage);

            return $this->sendPaginated(
                LoyaltyPointResource::collection($loyaltyPoints),
                'LoyaltyPoints başarıyla getirildi'
            );
        }

        $loyaltyPoints = $this->loyaltyPointService->getAll();

        return LoyaltyPointResource::collection($loyaltyPoints);
    }

    public function store(StoreLoyaltyPointRequest $request): JsonResponse
    {
        $loyaltyPoint = $this->loyaltyPointService->create($request->validated());

        return $this->sendSuccess(
            new LoyaltyPointResource($loyaltyPoint),
            'LoyaltyPoint başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $loyaltyPoint = $this->loyaltyPointService->findByIdOrFail($id);

        return $this->sendSuccess(
            new LoyaltyPointResource($loyaltyPoint),
            'LoyaltyPoint başarıyla getirildi'
        );
    }

    public function update(UpdateLoyaltyPointRequest $request, string $id): JsonResponse
    {
        $loyaltyPoint = $this->loyaltyPointService->update($id, $request->validated());

        return $this->sendSuccess(
            new LoyaltyPointResource($loyaltyPoint),
            'LoyaltyPoint başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->loyaltyPointService->delete($id);

        return $this->sendSuccess(
            null,
            'LoyaltyPoint başarıyla silindi'
        );
    }
}
