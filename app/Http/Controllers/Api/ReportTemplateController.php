<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportTemplate\StoreReportTemplateRequest;
use App\Http\Requests\ReportTemplate\UpdateReportTemplateRequest;
use App\Http\Resources\ReportTemplateResource;
use App\Services\Contracts\ReportTemplateServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReportTemplateController extends Controller
{
    public function __construct(
        protected ReportTemplateServiceInterface $reportTemplateService
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $templates = $this->reportTemplateService->getAll($perPage);

        return ReportTemplateResource::collection($templates);
    }

    public function store(StoreReportTemplateRequest $request): JsonResponse
    {
        $template = $this->reportTemplateService->create($request->validated());

        return response()->json([
            'message' => 'Report template created successfully',
            'data' => ReportTemplateResource::make($template),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $template = $this->reportTemplateService->findById($id);

        return response()->json([
            'data' => ReportTemplateResource::make($template),
        ]);
    }

    public function update(UpdateReportTemplateRequest $request, string $id): JsonResponse
    {
        $template = $this->reportTemplateService->update($id, $request->validated());

        return response()->json([
            'message' => 'Report template updated successfully',
            'data' => ReportTemplateResource::make($template),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->reportTemplateService->delete($id);

        return response()->json([
            'message' => 'Report template deleted successfully',
        ]);
    }

    public function active(): AnonymousResourceCollection
    {
        $templates = $this->reportTemplateService->getActive();

        return ReportTemplateResource::collection($templates);
    }

    public function system(): AnonymousResourceCollection
    {
        $templates = $this->reportTemplateService->getSystemTemplates();

        return ReportTemplateResource::collection($templates);
    }

    public function user(): AnonymousResourceCollection
    {
        $templates = $this->reportTemplateService->getUserTemplates();

        return ReportTemplateResource::collection($templates);
    }

    public function activate(string $id): JsonResponse
    {
        $template = $this->reportTemplateService->activate($id);

        return response()->json([
            'message' => 'Report template activated successfully',
            'data' => ReportTemplateResource::make($template),
        ]);
    }

    public function deactivate(string $id): JsonResponse
    {
        $template = $this->reportTemplateService->deactivate($id);

        return response()->json([
            'message' => 'Report template deactivated successfully',
            'data' => ReportTemplateResource::make($template),
        ]);
    }
}
