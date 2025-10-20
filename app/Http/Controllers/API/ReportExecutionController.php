<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreReportExecutionRequest;
use App\Http\Requests\UpdateReportExecutionRequest;
use App\Http\Resources\ReportExecutionResource;
use App\Services\ReportExecutionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReportExecutionController extends BaseController
{
    public function __construct(
        protected ReportExecutionService $reportExecutionService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $reportExecutions = $this->reportExecutionService->getPaginated($perPage);

            return $this->sendPaginated(
                ReportExecutionResource::collection($reportExecutions),
                'ReportExecutions başarıyla getirildi'
            );
        }

        $reportExecutions = $this->reportExecutionService->getAll();

        return ReportExecutionResource::collection($reportExecutions);
    }

    public function store(StoreReportExecutionRequest $request): JsonResponse
    {
        $reportExecution = $this->reportExecutionService->create($request->validated());

        return $this->sendSuccess(
            new ReportExecutionResource($reportExecution),
            'ReportExecution başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $reportExecution = $this->reportExecutionService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ReportExecutionResource($reportExecution),
            'ReportExecution başarıyla getirildi'
        );
    }

    public function update(UpdateReportExecutionRequest $request, string $id): JsonResponse
    {
        $reportExecution = $this->reportExecutionService->update($id, $request->validated());

        return $this->sendSuccess(
            new ReportExecutionResource($reportExecution),
            'ReportExecution başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->reportExecutionService->delete($id);

        return $this->sendSuccess(
            null,
            'ReportExecution başarıyla silindi'
        );
    }
}
