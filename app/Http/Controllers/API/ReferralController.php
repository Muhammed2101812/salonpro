<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreReferralRequest;
use App\Http\Requests\UpdateReferralRequest;
use App\Http\Resources\ReferralResource;
use App\Services\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReferralController extends BaseController
{
    public function __construct(
        protected ReferralService $referralService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $referrals = $this->referralService->getPaginated($perPage);

            return $this->sendPaginated(
                ReferralResource::collection($referrals),
                'Referrals başarıyla getirildi'
            );
        }

        $referrals = $this->referralService->getAll();

        return ReferralResource::collection($referrals);
    }

    public function store(StoreReferralRequest $request): JsonResponse
    {
        $referral = $this->referralService->create($request->validated());

        return $this->sendSuccess(
            new ReferralResource($referral),
            'Referral başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $referral = $this->referralService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ReferralResource($referral),
            'Referral başarıyla getirildi'
        );
    }

    public function update(UpdateReferralRequest $request, string $id): JsonResponse
    {
        $referral = $this->referralService->update($id, $request->validated());

        return $this->sendSuccess(
            new ReferralResource($referral),
            'Referral başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->referralService->delete($id);

        return $this->sendSuccess(
            null,
            'Referral başarıyla silindi'
        );
    }
}
