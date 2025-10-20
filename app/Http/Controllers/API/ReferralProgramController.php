<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreReferralProgramRequest;
use App\Http\Requests\UpdateReferralProgramRequest;
use App\Http\Resources\ReferralProgramResource;
use App\Services\ReferralProgramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReferralProgramController extends BaseController
{
    public function __construct(
        protected ReferralProgramService $referralProgramService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $referralPrograms = $this->referralProgramService->getPaginated($perPage);

            return $this->sendPaginated(
                ReferralProgramResource::collection($referralPrograms),
                'ReferralPrograms başarıyla getirildi'
            );
        }

        $referralPrograms = $this->referralProgramService->getAll();

        return ReferralProgramResource::collection($referralPrograms);
    }

    public function store(StoreReferralProgramRequest $request): JsonResponse
    {
        $referralProgram = $this->referralProgramService->create($request->validated());

        return $this->sendSuccess(
            new ReferralProgramResource($referralProgram),
            'ReferralProgram başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $referralProgram = $this->referralProgramService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ReferralProgramResource($referralProgram),
            'ReferralProgram başarıyla getirildi'
        );
    }

    public function update(UpdateReferralProgramRequest $request, string $id): JsonResponse
    {
        $referralProgram = $this->referralProgramService->update($id, $request->validated());

        return $this->sendSuccess(
            new ReferralProgramResource($referralProgram),
            'ReferralProgram başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->referralProgramService->delete($id);

        return $this->sendSuccess(
            null,
            'ReferralProgram başarıyla silindi'
        );
    }
}
