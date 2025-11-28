<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreTaxRateRequest;
use App\Http\Requests\UpdateTaxRateRequest;
use App\Http\Resources\TaxRateResource;
use App\Services\TaxRateService;
use App\Models\TaxRate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaxRateController extends BaseController
{
    public function __construct(
        protected TaxRateService $taxRateService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', TaxRate::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $taxRates = $this->taxRateService->getPaginated($perPage);

            return $this->sendPaginated(
                TaxRateResource::collection($taxRates),
                'TaxRates başarıyla getirildi'
            );
        }

        $taxRates = $this->taxRateService->getAll();

        return TaxRateResource::collection($taxRates);
    }

    public function store(StoreTaxRateRequest $request): JsonResponse
    {
        $this->authorize('create', TaxRate::class);

        $taxRate = $this->taxRateService->create($request->validated());

        return $this->sendSuccess(
            new TaxRateResource($taxRate),
            'TaxRate başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $taxRate = $this->taxRateService->findByIdOrFail($id);

        return $this->sendSuccess(
            new TaxRateResource($taxRate),
            'TaxRate başarıyla getirildi'
        );
    }

    public function update(UpdateTaxRateRequest $request, string $id): JsonResponse
    {
        $taxRate = $this->taxRateService->update($id, $request->validated());

        return $this->sendSuccess(
            new TaxRateResource($taxRate),
            'TaxRate başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->taxRateService->delete($id);

        return $this->sendSuccess(
            null,
            'TaxRate başarıyla silindi'
        );
    }
}
