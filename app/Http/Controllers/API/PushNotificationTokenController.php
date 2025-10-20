<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePushNotificationTokenRequest;
use App\Http\Requests\UpdatePushNotificationTokenRequest;
use App\Http\Resources\PushNotificationTokenResource;
use App\Services\PushNotificationTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PushNotificationTokenController extends BaseController
{
    public function __construct(
        protected PushNotificationTokenService $pushNotificationTokenService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $pushNotificationTokens = $this->pushNotificationTokenService->getPaginated($perPage);

            return $this->sendPaginated(
                PushNotificationTokenResource::collection($pushNotificationTokens),
                'PushNotificationTokens başarıyla getirildi'
            );
        }

        $pushNotificationTokens = $this->pushNotificationTokenService->getAll();

        return PushNotificationTokenResource::collection($pushNotificationTokens);
    }

    public function store(StorePushNotificationTokenRequest $request): JsonResponse
    {
        $pushNotificationToken = $this->pushNotificationTokenService->create($request->validated());

        return $this->sendSuccess(
            new PushNotificationTokenResource($pushNotificationToken),
            'PushNotificationToken başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $pushNotificationToken = $this->pushNotificationTokenService->findByIdOrFail($id);

        return $this->sendSuccess(
            new PushNotificationTokenResource($pushNotificationToken),
            'PushNotificationToken başarıyla getirildi'
        );
    }

    public function update(UpdatePushNotificationTokenRequest $request, string $id): JsonResponse
    {
        $pushNotificationToken = $this->pushNotificationTokenService->update($id, $request->validated());

        return $this->sendSuccess(
            new PushNotificationTokenResource($pushNotificationToken),
            'PushNotificationToken başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->pushNotificationTokenService->delete($id);

        return $this->sendSuccess(
            null,
            'PushNotificationToken başarıyla silindi'
        );
    }
}
