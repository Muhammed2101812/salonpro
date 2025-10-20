<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreChartOfAccountRequest;
use App\Http\Requests\UpdateChartOfAccountRequest;
use App\Http\Resources\ChartOfAccountResource;
use App\Services\ChartOfAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ChartOfAccountController extends BaseController
{
    public function __construct(
        protected ChartOfAccountService $chartOfAccountService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $chartOfAccounts = $this->chartOfAccountService->getPaginated($perPage);

            return $this->sendPaginated(
                ChartOfAccountResource::collection($chartOfAccounts),
                'ChartOfAccounts başarıyla getirildi'
            );
        }

        $chartOfAccounts = $this->chartOfAccountService->getAll();

        return ChartOfAccountResource::collection($chartOfAccounts);
    }

    public function store(StoreChartOfAccountRequest $request): JsonResponse
    {
        $chartOfAccount = $this->chartOfAccountService->create($request->validated());

        return $this->sendSuccess(
            new ChartOfAccountResource($chartOfAccount),
            'ChartOfAccount başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $chartOfAccount = $this->chartOfAccountService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ChartOfAccountResource($chartOfAccount),
            'ChartOfAccount başarıyla getirildi'
        );
    }

    public function update(UpdateChartOfAccountRequest $request, string $id): JsonResponse
    {
        $chartOfAccount = $this->chartOfAccountService->update($id, $request->validated());

        return $this->sendSuccess(
            new ChartOfAccountResource($chartOfAccount),
            'ChartOfAccount başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->chartOfAccountService->delete($id);

        return $this->sendSuccess(
            null,
            'ChartOfAccount başarıyla silindi'
        );
    }
}
