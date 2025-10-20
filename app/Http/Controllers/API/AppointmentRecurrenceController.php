<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreAppointmentRecurrenceRequest;
use App\Http\Requests\UpdateAppointmentRecurrenceRequest;
use App\Http\Resources\AppointmentRecurrenceResource;
use App\Services\AppointmentRecurrenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentRecurrenceController extends BaseController
{
    public function __construct(
        protected AppointmentRecurrenceService $appointmentRecurrenceService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $appointmentRecurrences = $this->appointmentRecurrenceService->getPaginated($perPage);

            return $this->sendPaginated(
                AppointmentRecurrenceResource::collection($appointmentRecurrences),
                'AppointmentRecurrences başarıyla getirildi'
            );
        }

        $appointmentRecurrences = $this->appointmentRecurrenceService->getAll();

        return AppointmentRecurrenceResource::collection($appointmentRecurrences);
    }

    public function store(StoreAppointmentRecurrenceRequest $request): JsonResponse
    {
        $appointmentRecurrence = $this->appointmentRecurrenceService->create($request->validated());

        return $this->sendSuccess(
            new AppointmentRecurrenceResource($appointmentRecurrence),
            'AppointmentRecurrence başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $appointmentRecurrence = $this->appointmentRecurrenceService->findByIdOrFail($id);

        return $this->sendSuccess(
            new AppointmentRecurrenceResource($appointmentRecurrence),
            'AppointmentRecurrence başarıyla getirildi'
        );
    }

    public function update(UpdateAppointmentRecurrenceRequest $request, string $id): JsonResponse
    {
        $appointmentRecurrence = $this->appointmentRecurrenceService->update($id, $request->validated());

        return $this->sendSuccess(
            new AppointmentRecurrenceResource($appointmentRecurrence),
            'AppointmentRecurrence başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->appointmentRecurrenceService->delete($id);

        return $this->sendSuccess(
            null,
            'AppointmentRecurrence başarıyla silindi'
        );
    }
}
