<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreReportTemplateRequest;
use App\Http\Requests\UpdateReportTemplateRequest;
use App\Http\Resources\ReportTemplateResource;
use App\Services\ReportTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReportTemplateController extends BaseController
{
    public function __construct(
        protected ReportTemplateService $reportTemplateService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $reportTemplates = $this->reportTemplateService->getPaginated($perPage);

            return $this->sendPaginated(
                ReportTemplateResource::collection($reportTemplates),
                'ReportTemplates başarıyla getirildi'
            );
        }

        $reportTemplates = $this->reportTemplateService->getAll();

        return ReportTemplateResource::collection($reportTemplates);
    }

    public function store(StoreReportTemplateRequest $request): JsonResponse
    {
        $reportTemplate = $this->reportTemplateService->create($request->validated());

        return $this->sendSuccess(
            new ReportTemplateResource($reportTemplate),
            'ReportTemplate başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $reportTemplate = $this->reportTemplateService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ReportTemplateResource($reportTemplate),
            'ReportTemplate başarıyla getirildi'
        );
    }

    public function update(UpdateReportTemplateRequest $request, string $id): JsonResponse
    {
        $reportTemplate = $this->reportTemplateService->update($id, $request->validated());

        return $this->sendSuccess(
            new ReportTemplateResource($reportTemplate),
            'ReportTemplate başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->reportTemplateService->delete($id);

        return $this->sendSuccess(
            null,
            'ReportTemplate başarıyla silindi'
        );
    }
}
