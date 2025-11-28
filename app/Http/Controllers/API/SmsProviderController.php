<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreSmsProviderRequest;
use App\Http\Requests\UpdateSmsProviderRequest;
use App\Http\Resources\SmsProviderResource;
use App\Services\SmsProviderService;
use App\Models\SmsProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SmsProviderController extends BaseController
{
    public function __construct(
        protected SmsProviderService $smsProviderService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', SmsProvider::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $smsProviders = $this->smsProviderService->getPaginated($perPage);

            return $this->sendPaginated(
                SmsProviderResource::collection($smsProviders),
                'SmsProviders başarıyla getirildi'
            );
        }

        $smsProviders = $this->smsProviderService->getAll();

        return SmsProviderResource::collection($smsProviders);
    }

    public function store(StoreSmsProviderRequest $request): JsonResponse
    {
        $this->authorize('create', SmsProvider::class);

        $smsProvider = $this->smsProviderService->create($request->validated());

        return $this->sendSuccess(
            new SmsProviderResource($smsProvider),
            'SmsProvider başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $smsProvider = $this->smsProviderService->findByIdOrFail($id);

        return $this->sendSuccess(
            new SmsProviderResource($smsProvider),
            'SmsProvider başarıyla getirildi'
        );
    }

    public function update(UpdateSmsProviderRequest $request, string $id): JsonResponse
    {
        $smsProvider = $this->smsProviderService->update($id, $request->validated());

        return $this->sendSuccess(
            new SmsProviderResource($smsProvider),
            'SmsProvider başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->smsProviderService->delete($id);

        return $this->sendSuccess(
            null,
            'SmsProvider başarıyla silindi'
        );
    }
}
