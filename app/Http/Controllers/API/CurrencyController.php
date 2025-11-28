<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Http\Resources\CurrencyResource;
use App\Services\CurrencyService;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CurrencyController extends BaseController
{
    public function __construct(
        protected CurrencyService $currencyService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', Currency::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $currencies = $this->currencyService->getPaginated($perPage);

            return $this->sendPaginated(
                CurrencyResource::collection($currencies),
                'Currencies başarıyla getirildi'
            );
        }

        $currencies = $this->currencyService->getAll();

        return CurrencyResource::collection($currencies);
    }

    public function store(StoreCurrencyRequest $request): JsonResponse
    {
        $this->authorize('create', Currency::class);

        $currency = $this->currencyService->create($request->validated());

        $this->authorize('view', $currency);


        return $this->sendSuccess(
            new CurrencyResource($currency),
            'Currency başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $currency = $this->currencyService->findByIdOrFail($id);

        return $this->sendSuccess(
            new CurrencyResource($currency),
            'Currency başarıyla getirildi'
        );
    }

    public function update(UpdateCurrencyRequest $request, string $id): JsonResponse
    {
        $currency = $this->currencyService->update($id, $request->validated());

        $this->authorize('update', $currency);


        return $this->sendSuccess(
            new CurrencyResource($currency),
            'Currency başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $currency = $this->currencyService->findByIdOrFail($id);

        $this->authorize('delete', $currency);

        $this->currencyService->delete($id);

        return $this->sendSuccess(
            null,
            'Currency başarıyla silindi'
        );
    }
}
