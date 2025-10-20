<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreAppointmentReminderRequest;
use App\Http\Requests\UpdateAppointmentReminderRequest;
use App\Http\Resources\AppointmentReminderResource;
use App\Services\AppointmentReminderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentReminderController extends BaseController
{
    public function __construct(
        protected AppointmentReminderService $appointmentReminderService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $appointmentReminders = $this->appointmentReminderService->getPaginated($perPage);

            return $this->sendPaginated(
                AppointmentReminderResource::collection($appointmentReminders),
                'AppointmentReminders başarıyla getirildi'
            );
        }

        $appointmentReminders = $this->appointmentReminderService->getAll();

        return AppointmentReminderResource::collection($appointmentReminders);
    }

    public function store(StoreAppointmentReminderRequest $request): JsonResponse
    {
        $appointmentReminder = $this->appointmentReminderService->create($request->validated());

        return $this->sendSuccess(
            new AppointmentReminderResource($appointmentReminder),
            'AppointmentReminder başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $appointmentReminder = $this->appointmentReminderService->findByIdOrFail($id);

        return $this->sendSuccess(
            new AppointmentReminderResource($appointmentReminder),
            'AppointmentReminder başarıyla getirildi'
        );
    }

    public function update(UpdateAppointmentReminderRequest $request, string $id): JsonResponse
    {
        $appointmentReminder = $this->appointmentReminderService->update($id, $request->validated());

        return $this->sendSuccess(
            new AppointmentReminderResource($appointmentReminder),
            'AppointmentReminder başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->appointmentReminderService->delete($id);

        return $this->sendSuccess(
            null,
            'AppointmentReminder başarıyla silindi'
        );
    }
}
