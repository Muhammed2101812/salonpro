<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreSurveyResponseRequest;
use App\Http\Requests\UpdateSurveyResponseRequest;
use App\Http\Resources\SurveyResponseResource;
use App\Services\SurveyResponseService;
use App\Models\SurveyResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SurveyResponseController extends BaseController
{
    public function __construct(
        protected SurveyResponseService $surveyResponseService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', SurveyResponse::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $surveyResponses = $this->surveyResponseService->getPaginated($perPage);

            return $this->sendPaginated(
                SurveyResponseResource::collection($surveyResponses),
                'SurveyResponses başarıyla getirildi'
            );
        }

        $surveyResponses = $this->surveyResponseService->getAll();

        return SurveyResponseResource::collection($surveyResponses);
    }

    public function store(StoreSurveyResponseRequest $request): JsonResponse
    {
        $this->authorize('create', SurveyResponse::class);

        $surveyResponse = $this->surveyResponseService->create($request->validated());

        return $this->sendSuccess(
            new SurveyResponseResource($surveyResponse),
            'SurveyResponse başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $surveyResponse = $this->surveyResponseService->findByIdOrFail($id);

        return $this->sendSuccess(
            new SurveyResponseResource($surveyResponse),
            'SurveyResponse başarıyla getirildi'
        );
    }

    public function update(UpdateSurveyResponseRequest $request, string $id): JsonResponse
    {
        $surveyResponse = $this->surveyResponseService->update($id, $request->validated());

        return $this->sendSuccess(
            new SurveyResponseResource($surveyResponse),
            'SurveyResponse başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->surveyResponseService->delete($id);

        return $this->sendSuccess(
            null,
            'SurveyResponse başarıyla silindi'
        );
    }
}
