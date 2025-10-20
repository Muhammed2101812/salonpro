<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Http\Resources\LeadResource;
use App\Services\LeadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LeadController extends BaseController
{
    public function __construct(
        protected LeadService $leadService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $leads = $this->leadService->getPaginated($perPage);

            return $this->sendPaginated(
                LeadResource::collection($leads),
                'Leads başarıyla getirildi'
            );
        }

        $leads = $this->leadService->getAll();

        return LeadResource::collection($leads);
    }

    public function store(StoreLeadRequest $request): JsonResponse
    {
        $lead = $this->leadService->create($request->validated());

        return $this->sendSuccess(
            new LeadResource($lead),
            'Lead başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $lead = $this->leadService->findByIdOrFail($id);

        return $this->sendSuccess(
            new LeadResource($lead),
            'Lead başarıyla getirildi'
        );
    }

    public function update(UpdateLeadRequest $request, string $id): JsonResponse
    {
        $lead = $this->leadService->update($id, $request->validated());

        return $this->sendSuccess(
            new LeadResource($lead),
            'Lead başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->leadService->delete($id);

        return $this->sendSuccess(
            null,
            'Lead başarıyla silindi'
        );
    }
}
