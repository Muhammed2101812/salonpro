<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreLoyaltyProgramRequest;
use App\Http\Requests\UpdateLoyaltyProgramRequest;
use App\Http\Resources\LoyaltyProgramResource;
use App\Services\LoyaltyProgramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LoyaltyProgramController extends BaseController
{
    public function __construct(
        protected LoyaltyProgramService $loyaltyProgramService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $loyaltyPrograms = $this->loyaltyProgramService->getPaginated($perPage);

            return $this->sendPaginated(
                LoyaltyProgramResource::collection($loyaltyPrograms),
                'LoyaltyPrograms başarıyla getirildi'
            );
        }

        $loyaltyPrograms = $this->loyaltyProgramService->getAll();

        return LoyaltyProgramResource::collection($loyaltyPrograms);
    }

    public function store(StoreLoyaltyProgramRequest $request): JsonResponse
    {
        $loyaltyProgram = $this->loyaltyProgramService->create($request->validated());

        return $this->sendSuccess(
            new LoyaltyProgramResource($loyaltyProgram),
            'LoyaltyProgram başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $loyaltyProgram = $this->loyaltyProgramService->findByIdOrFail($id);

        return $this->sendSuccess(
            new LoyaltyProgramResource($loyaltyProgram),
            'LoyaltyProgram başarıyla getirildi'
        );
    }

    public function update(UpdateLoyaltyProgramRequest $request, string $id): JsonResponse
    {
        $loyaltyProgram = $this->loyaltyProgramService->update($id, $request->validated());

        return $this->sendSuccess(
            new LoyaltyProgramResource($loyaltyProgram),
            'LoyaltyProgram başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->loyaltyProgramService->delete($id);

        return $this->sendSuccess(
            null,
            'LoyaltyProgram başarıyla silindi'
        );
    }
}
