<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected AppointmentService $appointmentService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', Appointment::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $appointments = $this->appointmentService->getPaginated($perPage);

            return $this->sendPaginated(
                AppointmentResource::collection($appointments),
                'Randevular başarıyla getirildi'
            );
        }

        $appointments = $this->appointmentService->getAll();

        return AppointmentResource::collection($appointments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $this->authorize('create', Appointment::class);

        $appointment = $this->appointmentService->create($request->validated());

        return $this->sendSuccess(
            new AppointmentResource($appointment),
            'Randevu başarıyla oluşturuldu',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $appointment = $this->appointmentService->findByIdOrFail($id);

        $this->authorize('view', $appointment);

        return $this->sendSuccess(
            new AppointmentResource($appointment),
            'Randevu başarıyla getirildi'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, string $id): JsonResponse
    {
        $appointment = $this->appointmentService->findByIdOrFail($id);

        $this->authorize('update', $appointment);

        $appointment = $this->appointmentService->update($id, $request->validated());

        return $this->sendSuccess(
            new AppointmentResource($appointment),
            'Randevu başarıyla güncellendi'
        );
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        $appointment = $this->appointmentService->findByIdOrFail($id);

        $this->authorize('delete', $appointment);

        $this->appointmentService->delete($id);

        return $this->sendSuccess(
            null,
            'Randevu başarıyla silindi'
        );
    }

    /**
     * Restore a soft-deleted resource.
     */
    public function restore(string $id): JsonResponse
    {
        $appointment = $this->appointmentService->findByIdOrFail($id);

        $this->authorize('restore', $appointment);

        $this->appointmentService->restore($id);

        return $this->sendSuccess(
            null,
            'Randevu başarıyla geri yüklendi'
        );
    }

    /**
     * Permanently remove the specified resource from storage.
     */
    public function forceDestroy(string $id): JsonResponse
    {
        $appointment = $this->appointmentService->findByIdOrFail($id);

        $this->authorize('forceDelete', $appointment);

        $this->appointmentService->forceDelete($id);

        return $this->sendSuccess(
            null,
            'Randevu kalıcı olarak silindi'
        );
    }
}
