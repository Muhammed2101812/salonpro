<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreExchangeRateRequest;
use App\Http\Requests\UpdateExchangeRateRequest;
use App\Http\Resources\ExchangeRateResource;
use App\Services\ExchangeRateService;
use App\Models\ExchangeRate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExchangeRateController extends BaseController
{
    public function __construct(
        protected ExchangeRateService $exchangeRateService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', ExchangeRate::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $exchangeRates = $this->exchangeRateService->getPaginated($perPage);

            return $this->sendPaginated(
                ExchangeRateResource::collection($exchangeRates),
                'ExchangeRates başarıyla getirildi'
            );
        }

        $exchangeRates = $this->exchangeRateService->getAll();

        return ExchangeRateResource::collection($exchangeRates);
    }

    public function store(StoreExchangeRateRequest $request): JsonResponse
    {
        $this->authorize('create', ExchangeRate::class);

        $exchangeRate = $this->exchangeRateService->create($request->validated());

        return $this->sendSuccess(
            new ExchangeRateResource($exchangeRate),
            'ExchangeRate başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $exchangeRate = $this->exchangeRateService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ExchangeRateResource($exchangeRate),
            'ExchangeRate başarıyla getirildi'
        );
    }

    public function update(UpdateExchangeRateRequest $request, string $id): JsonResponse
    {
        $exchangeRate = $this->exchangeRateService->update($id, $request->validated());

        return $this->sendSuccess(
            new ExchangeRateResource($exchangeRate),
            'ExchangeRate başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->exchangeRateService->delete($id);

        return $this->sendSuccess(
            null,
            'ExchangeRate başarıyla silindi'
        );
    }
}
