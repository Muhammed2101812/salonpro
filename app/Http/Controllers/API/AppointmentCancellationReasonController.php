<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreAppointmentCancellationReasonRequest;
use App\Http\Requests\UpdateAppointmentCancellationReasonRequest;
use App\Http\Resources\AppointmentCancellationReasonResource;
use App\Services\AppointmentCancellationReasonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentCancellationReasonController extends BaseController
{
    public function __construct(
        protected AppointmentCancellationReasonService $appointmentCancellationReasonService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $appointmentCancellationReasons = $this->appointmentCancellationReasonService->getPaginated($perPage);

            return $this->sendPaginated(
                AppointmentCancellationReasonResource::collection($appointmentCancellationReasons),
                'AppointmentCancellationReasons başarıyla getirildi'
            );
        }

        $appointmentCancellationReasons = $this->appointmentCancellationReasonService->getAll();

        return AppointmentCancellationReasonResource::collection($appointmentCancellationReasons);
    }

    public function store(StoreAppointmentCancellationReasonRequest $request): JsonResponse
    {
        $appointmentCancellationReason = $this->appointmentCancellationReasonService->create($request->validated());

        return $this->sendSuccess(
            new AppointmentCancellationReasonResource($appointmentCancellationReason),
            'AppointmentCancellationReason başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $appointmentCancellationReason = $this->appointmentCancellationReasonService->findByIdOrFail($id);

        return $this->sendSuccess(
            new AppointmentCancellationReasonResource($appointmentCancellationReason),
            'AppointmentCancellationReason başarıyla getirildi'
        );
    }

    public function update(UpdateAppointmentCancellationReasonRequest $request, string $id): JsonResponse
    {
        $appointmentCancellationReason = $this->appointmentCancellationReasonService->update($id, $request->validated());

        return $this->sendSuccess(
            new AppointmentCancellationReasonResource($appointmentCancellationReason),
            'AppointmentCancellationReason başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->appointmentCancellationReasonService->delete($id);

        return $this->sendSuccess(
            null,
            'AppointmentCancellationReason başarıyla silindi'
        );
    }
}
