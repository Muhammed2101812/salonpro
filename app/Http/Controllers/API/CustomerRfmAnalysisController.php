<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCustomerRfmAnalysisRequest;
use App\Http\Requests\UpdateCustomerRfmAnalysisRequest;
use App\Http\Resources\CustomerRfmAnalysisResource;
use App\Services\CustomerRfmAnalysisService;
use App\Models\CustomerRfmAnalysis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerRfmAnalysisController extends BaseController
{
    public function __construct(
        protected CustomerRfmAnalysisService $customerRfmAnalysisService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', CustomerRfmAnalysis::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $customerRfmAnalyses = $this->customerRfmAnalysisService->getPaginated($perPage);

            return $this->sendPaginated(
                CustomerRfmAnalysisResource::collection($customerRfmAnalyses),
                'CustomerRfmAnalyses başarıyla getirildi'
            );
        }

        $customerRfmAnalyses = $this->customerRfmAnalysisService->getAll();

        return CustomerRfmAnalysisResource::collection($customerRfmAnalyses);
    }

    public function store(StoreCustomerRfmAnalysisRequest $request): JsonResponse
    {
        $this->authorize('create', CustomerRfmAnalysis::class);

        $customerRfmAnalysis = $this->customerRfmAnalysisService->create($request->validated());

        return $this->sendSuccess(
            new CustomerRfmAnalysisResource($customerRfmAnalysis),
            'CustomerRfmAnalysis başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $customerRfmAnalysis = $this->customerRfmAnalysisService->findByIdOrFail($id);

        return $this->sendSuccess(
            new CustomerRfmAnalysisResource($customerRfmAnalysis),
            'CustomerRfmAnalysis başarıyla getirildi'
        );
    }

    public function update(UpdateCustomerRfmAnalysisRequest $request, string $id): JsonResponse
    {
        $customerRfmAnalysis = $this->customerRfmAnalysisService->update($id, $request->validated());

        return $this->sendSuccess(
            new CustomerRfmAnalysisResource($customerRfmAnalysis),
            'CustomerRfmAnalysis başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->customerRfmAnalysisService->delete($id);

        return $this->sendSuccess(
            null,
            'CustomerRfmAnalysis başarıyla silindi'
        );
    }
}
