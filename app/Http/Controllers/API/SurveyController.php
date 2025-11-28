<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Http\Resources\SurveyResource;
use App\Services\SurveyService;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SurveyController extends BaseController
{
    public function __construct(
        protected SurveyService $surveyService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', Survey::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $surveys = $this->surveyService->getPaginated($perPage);

            return $this->sendPaginated(
                SurveyResource::collection($surveys),
                'Surveys başarıyla getirildi'
            );
        }

        $surveys = $this->surveyService->getAll();

        return SurveyResource::collection($surveys);
    }

    public function store(StoreSurveyRequest $request): JsonResponse
    {
        $this->authorize('create', Survey::class);

        $survey = $this->surveyService->create($request->validated());

        $this->authorize('view', $survey);


        return $this->sendSuccess(
            new SurveyResource($survey),
            'Survey başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $survey = $this->surveyService->findByIdOrFail($id);

        return $this->sendSuccess(
            new SurveyResource($survey),
            'Survey başarıyla getirildi'
        );
    }

    public function update(UpdateSurveyRequest $request, string $id): JsonResponse
    {
        $survey = $this->surveyService->update($id, $request->validated());

        $this->authorize('update', $survey);


        return $this->sendSuccess(
            new SurveyResource($survey),
            'Survey başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $survey = $this->surveyService->findByIdOrFail($id);

        $this->authorize('delete', $survey);

        $this->surveyService->delete($id);

        return $this->sendSuccess(
            null,
            'Survey başarıyla silindi'
        );
    }
}
