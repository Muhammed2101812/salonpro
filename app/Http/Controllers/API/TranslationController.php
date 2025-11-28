<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreTranslationRequest;
use App\Http\Requests\UpdateTranslationRequest;
use App\Http\Resources\TranslationResource;
use App\Services\TranslationService;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TranslationController extends BaseController
{
    public function __construct(
        protected TranslationService $translationService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', Translation::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $translations = $this->translationService->getPaginated($perPage);

            return $this->sendPaginated(
                TranslationResource::collection($translations),
                'Translations başarıyla getirildi'
            );
        }

        $translations = $this->translationService->getAll();

        return TranslationResource::collection($translations);
    }

    public function store(StoreTranslationRequest $request): JsonResponse
    {
        $this->authorize('create', Translation::class);

        $translation = $this->translationService->create($request->validated());

        $this->authorize('view', $translation);


        return $this->sendSuccess(
            new TranslationResource($translation),
            'Translation başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $translation = $this->translationService->findByIdOrFail($id);

        return $this->sendSuccess(
            new TranslationResource($translation),
            'Translation başarıyla getirildi'
        );
    }

    public function update(UpdateTranslationRequest $request, string $id): JsonResponse
    {
        $translation = $this->translationService->update($id, $request->validated());

        $this->authorize('update', $translation);


        return $this->sendSuccess(
            new TranslationResource($translation),
            'Translation başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $translation = $this->translationService->findByIdOrFail($id);

        $this->authorize('delete', $translation);

        $this->translationService->delete($id);

        return $this->sendSuccess(
            null,
            'Translation başarıyla silindi'
        );
    }
}
