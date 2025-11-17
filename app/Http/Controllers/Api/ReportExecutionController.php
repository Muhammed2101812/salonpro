<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportExecution\StoreReportExecutionRequest;
use App\Http\Requests\ReportExecution\UpdateReportExecutionRequest;
use App\Http\Resources\ReportExecutionResource;
use App\Services\Contracts\ReportExecutionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReportExecutionController extends Controller
{
    public function __construct(
        protected ReportExecutionServiceInterface $reportExecutionService
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $executions = $this->reportExecutionService->getAll($perPage);

        return ReportExecutionResource::collection($executions);
    }

    public function store(StoreReportExecutionRequest $request): JsonResponse
    {
        $execution = $this->reportExecutionService->executeReport($request->validated());

        return response()->json([
            'message' => 'Report execution started successfully',
            'data' => ReportExecutionResource::make($execution),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $execution = $this->reportExecutionService->findById($id);

        return response()->json([
            'data' => ReportExecutionResource::make($execution),
        ]);
    }

    public function update(UpdateReportExecutionRequest $request, string $id): JsonResponse
    {
        $execution = $this->reportExecutionService->update($id, $request->validated());

        return response()->json([
            'message' => 'Report execution updated successfully',
            'data' => ReportExecutionResource::make($execution),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->reportExecutionService->delete($id);

        return response()->json([
            'message' => 'Report execution deleted successfully',
        ]);
    }

    public function pending(): AnonymousResourceCollection
    {
        $executions = $this->reportExecutionService->getPending();

        return ReportExecutionResource::collection($executions);
    }

    public function completed(Request $request): AnonymousResourceCollection
    {
        $branchId = $request->query('branch_id');
        $executions = $this->reportExecutionService->getCompleted($branchId);

        return ReportExecutionResource::collection($executions);
    }

    public function failed(Request $request): AnonymousResourceCollection
    {
        $branchId = $request->query('branch_id');
        $executions = $this->reportExecutionService->getFailed($branchId);

        return ReportExecutionResource::collection($executions);
    }

    public function markAsCompleted(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'output_file' => ['nullable', 'string'],
            'output_format' => ['nullable', 'string'],
            'row_count' => ['nullable', 'integer'],
            'file_size' => ['nullable', 'integer'],
        ]);

        $execution = $this->reportExecutionService->markAsCompleted($id, $request->all());

        return response()->json([
            'message' => 'Report execution marked as completed',
            'data' => ReportExecutionResource::make($execution),
        ]);
    }

    public function markAsFailed(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'error_message' => ['required', 'string'],
        ]);

        $execution = $this->reportExecutionService->markAsFailed($id, $request->input('error_message'));

        return response()->json([
            'message' => 'Report execution marked as failed',
            'data' => ReportExecutionResource::make($execution),
        ]);
    }
}
