<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreEmailProviderRequest;
use App\Http\Requests\UpdateEmailProviderRequest;
use App\Http\Resources\EmailProviderResource;
use App\Services\EmailProviderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmailProviderController extends BaseController
{
    public function __construct(
        protected EmailProviderService $emailProviderService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $emailProviders = $this->emailProviderService->getPaginated($perPage);

            return $this->sendPaginated(
                EmailProviderResource::collection($emailProviders),
                'EmailProviders başarıyla getirildi'
            );
        }

        $emailProviders = $this->emailProviderService->getAll();

        return EmailProviderResource::collection($emailProviders);
    }

    public function store(StoreEmailProviderRequest $request): JsonResponse
    {
        $emailProvider = $this->emailProviderService->create($request->validated());

        return $this->sendSuccess(
            new EmailProviderResource($emailProvider),
            'EmailProvider başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $emailProvider = $this->emailProviderService->findByIdOrFail($id);

        return $this->sendSuccess(
            new EmailProviderResource($emailProvider),
            'EmailProvider başarıyla getirildi'
        );
    }

    public function update(UpdateEmailProviderRequest $request, string $id): JsonResponse
    {
        $emailProvider = $this->emailProviderService->update($id, $request->validated());

        return $this->sendSuccess(
            new EmailProviderResource($emailProvider),
            'EmailProvider başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->emailProviderService->delete($id);

        return $this->sendSuccess(
            null,
            'EmailProvider başarıyla silindi'
        );
    }
}
