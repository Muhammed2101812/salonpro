<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreAppointmentGroupRequest;
use App\Http\Requests\UpdateAppointmentGroupRequest;
use App\Http\Resources\AppointmentGroupResource;
use App\Services\AppointmentGroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentGroupController extends BaseController
{
    public function __construct(
        protected AppointmentGroupService $appointmentGroupService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $appointmentGroups = $this->appointmentGroupService->getPaginated($perPage);

            return $this->sendPaginated(
                AppointmentGroupResource::collection($appointmentGroups),
                'AppointmentGroups başarıyla getirildi'
            );
        }

        $appointmentGroups = $this->appointmentGroupService->getAll();

        return AppointmentGroupResource::collection($appointmentGroups);
    }

    public function store(StoreAppointmentGroupRequest $request): JsonResponse
    {
        $appointmentGroup = $this->appointmentGroupService->create($request->validated());

        return $this->sendSuccess(
            new AppointmentGroupResource($appointmentGroup),
            'AppointmentGroup başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $appointmentGroup = $this->appointmentGroupService->findByIdOrFail($id);

        return $this->sendSuccess(
            new AppointmentGroupResource($appointmentGroup),
            'AppointmentGroup başarıyla getirildi'
        );
    }

    public function update(UpdateAppointmentGroupRequest $request, string $id): JsonResponse
    {
        $appointmentGroup = $this->appointmentGroupService->update($id, $request->validated());

        return $this->sendSuccess(
            new AppointmentGroupResource($appointmentGroup),
            'AppointmentGroup başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->appointmentGroupService->delete($id);

        return $this->sendSuccess(
            null,
            'AppointmentGroup başarıyla silindi'
        );
    }
}
