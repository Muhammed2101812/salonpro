<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreAppointmentConflictRequest;
use App\Http\Requests\UpdateAppointmentConflictRequest;
use App\Http\Resources\AppointmentConflictResource;
use App\Services\AppointmentConflictService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentConflictController extends BaseController
{
    public function __construct(
        protected AppointmentConflictService $appointmentConflictService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $appointmentConflicts = $this->appointmentConflictService->getPaginated($perPage);

            return $this->sendPaginated(
                AppointmentConflictResource::collection($appointmentConflicts),
                'AppointmentConflicts başarıyla getirildi'
            );
        }

        $appointmentConflicts = $this->appointmentConflictService->getAll();

        return AppointmentConflictResource::collection($appointmentConflicts);
    }

    public function store(StoreAppointmentConflictRequest $request): JsonResponse
    {
        $appointmentConflict = $this->appointmentConflictService->create($request->validated());

        return $this->sendSuccess(
            new AppointmentConflictResource($appointmentConflict),
            'AppointmentConflict başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $appointmentConflict = $this->appointmentConflictService->findByIdOrFail($id);

        return $this->sendSuccess(
            new AppointmentConflictResource($appointmentConflict),
            'AppointmentConflict başarıyla getirildi'
        );
    }

    public function update(UpdateAppointmentConflictRequest $request, string $id): JsonResponse
    {
        $appointmentConflict = $this->appointmentConflictService->update($id, $request->validated());

        return $this->sendSuccess(
            new AppointmentConflictResource($appointmentConflict),
            'AppointmentConflict başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->appointmentConflictService->delete($id);

        return $this->sendSuccess(
            null,
            'AppointmentConflict başarıyla silindi'
        );
    }
}
