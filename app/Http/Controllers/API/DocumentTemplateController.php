<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreDocumentTemplateRequest;
use App\Http\Requests\UpdateDocumentTemplateRequest;
use App\Http\Resources\DocumentTemplateResource;
use App\Services\DocumentTemplateService;
use App\Models\DocumentTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DocumentTemplateController extends BaseController
{
    public function __construct(
        protected DocumentTemplateService $documentTemplateService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', DocumentTemplate::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $documentTemplates = $this->documentTemplateService->getPaginated($perPage);

            return $this->sendPaginated(
                DocumentTemplateResource::collection($documentTemplates),
                'DocumentTemplates başarıyla getirildi'
            );
        }

        $documentTemplates = $this->documentTemplateService->getAll();

        return DocumentTemplateResource::collection($documentTemplates);
    }

    public function store(StoreDocumentTemplateRequest $request): JsonResponse
    {
        $this->authorize('create', DocumentTemplate::class);

        $documentTemplate = $this->documentTemplateService->create($request->validated());

        return $this->sendSuccess(
            new DocumentTemplateResource($documentTemplate),
            'DocumentTemplate başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $documentTemplate = $this->documentTemplateService->findByIdOrFail($id);

        return $this->sendSuccess(
            new DocumentTemplateResource($documentTemplate),
            'DocumentTemplate başarıyla getirildi'
        );
    }

    public function update(UpdateDocumentTemplateRequest $request, string $id): JsonResponse
    {
        $documentTemplate = $this->documentTemplateService->update($id, $request->validated());

        return $this->sendSuccess(
            new DocumentTemplateResource($documentTemplate),
            'DocumentTemplate başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->documentTemplateService->delete($id);

        return $this->sendSuccess(
            null,
            'DocumentTemplate başarıyla silindi'
        );
    }
}
