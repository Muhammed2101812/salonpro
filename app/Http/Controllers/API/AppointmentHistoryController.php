<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreAppointmentHistoryRequest;
use App\Http\Requests\UpdateAppointmentHistoryRequest;
use App\Http\Resources\AppointmentHistoryResource;
use App\Services\AppointmentHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentHistoryController extends BaseController
{
    public function __construct(
        protected AppointmentHistoryService $appointmentHistoryService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $appointmentHistories = $this->appointmentHistoryService->getPaginated($perPage);

            return $this->sendPaginated(
                AppointmentHistoryResource::collection($appointmentHistories),
                'AppointmentHistories başarıyla getirildi'
            );
        }

        $appointmentHistories = $this->appointmentHistoryService->getAll();

        return AppointmentHistoryResource::collection($appointmentHistories);
    }

    public function store(StoreAppointmentHistoryRequest $request): JsonResponse
    {
        $appointmentHistory = $this->appointmentHistoryService->create($request->validated());

        return $this->sendSuccess(
            new AppointmentHistoryResource($appointmentHistory),
            'AppointmentHistory başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $appointmentHistory = $this->appointmentHistoryService->findByIdOrFail($id);

        return $this->sendSuccess(
            new AppointmentHistoryResource($appointmentHistory),
            'AppointmentHistory başarıyla getirildi'
        );
    }

    public function update(UpdateAppointmentHistoryRequest $request, string $id): JsonResponse
    {
        $appointmentHistory = $this->appointmentHistoryService->update($id, $request->validated());

        return $this->sendSuccess(
            new AppointmentHistoryResource($appointmentHistory),
            'AppointmentHistory başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->appointmentHistoryService->delete($id);

        return $this->sendSuccess(
            null,
            'AppointmentHistory başarıyla silindi'
        );
    }
}
