<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreReportScheduleRequest;
use App\Http\Requests\UpdateReportScheduleRequest;
use App\Http\Resources\ReportScheduleResource;
use App\Services\ReportScheduleService;
use App\Models\ReportSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReportScheduleController extends BaseController
{
    public function __construct(
        protected ReportScheduleService $reportScheduleService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', ReportSchedule::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $reportSchedules = $this->reportScheduleService->getPaginated($perPage);

            return $this->sendPaginated(
                ReportScheduleResource::collection($reportSchedules),
                'ReportSchedules başarıyla getirildi'
            );
        }

        $reportSchedules = $this->reportScheduleService->getAll();

        return ReportScheduleResource::collection($reportSchedules);
    }

    public function store(StoreReportScheduleRequest $request): JsonResponse
    {
        $this->authorize('create', ReportSchedule::class);

        $reportSchedule = $this->reportScheduleService->create($request->validated());

        return $this->sendSuccess(
            new ReportScheduleResource($reportSchedule),
            'ReportSchedule başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $reportSchedule = $this->reportScheduleService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ReportScheduleResource($reportSchedule),
            'ReportSchedule başarıyla getirildi'
        );
    }

    public function update(UpdateReportScheduleRequest $request, string $id): JsonResponse
    {
        $reportSchedule = $this->reportScheduleService->update($id, $request->validated());

        return $this->sendSuccess(
            new ReportScheduleResource($reportSchedule),
            'ReportSchedule başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->reportScheduleService->delete($id);

        return $this->sendSuccess(
            null,
            'ReportSchedule başarıyla silindi'
        );
    }
}
