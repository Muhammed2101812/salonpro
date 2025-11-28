<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreChartOfAccountRequest;
use App\Http\Requests\UpdateChartOfAccountRequest;
use App\Http\Resources\ChartOfAccountResource;
use App\Services\ChartOfAccountService;
use App\Models\ChartOfAccount;
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
        $this->authorize('viewAny', ChartOfAccount::class);

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
        $this->authorize('create', ChartOfAccount::class);

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

        $this->authorize('view', $chartOfAccount);

        return $this->sendSuccess(
            new ChartOfAccountResource($chartOfAccount),
            'ChartOfAccount başarıyla getirildi'
        );
    }

    public function update(UpdateChartOfAccountRequest $request, string $id): JsonResponse
    {
        $chartOfAccount = $this->chartOfAccountService->findByIdOrFail($id);

        $this->authorize('update', $chartOfAccount);

        $chartOfAccount = $this->chartOfAccountService->update($id, $request->validated());

        return $this->sendSuccess(
            new ChartOfAccountResource($chartOfAccount),
            'ChartOfAccount başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $chartOfAccount = $this->chartOfAccountService->findByIdOrFail($id);

        $this->authorize('delete', $chartOfAccount);

        $this->chartOfAccountService->delete($id);

        return $this->sendSuccess(
            null,
            'ChartOfAccount başarıyla silindi'
        );
    }
}
