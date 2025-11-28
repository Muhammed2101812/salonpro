<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreAppointmentGroupParticipantRequest;
use App\Http\Requests\UpdateAppointmentGroupParticipantRequest;
use App\Http\Resources\AppointmentGroupParticipantResource;
use App\Services\AppointmentGroupParticipantService;
use App\Models\AppointmentGroupParticipant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentGroupParticipantController extends BaseController
{
    public function __construct(
        protected AppointmentGroupParticipantService $appointmentGroupParticipantService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', AppointmentGroupParticipant::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $appointmentGroupParticipants = $this->appointmentGroupParticipantService->getPaginated($perPage);

            return $this->sendPaginated(
                AppointmentGroupParticipantResource::collection($appointmentGroupParticipants),
                'AppointmentGroupParticipants başarıyla getirildi'
            );
        }

        $appointmentGroupParticipants = $this->appointmentGroupParticipantService->getAll();

        return AppointmentGroupParticipantResource::collection($appointmentGroupParticipants);
    }

    public function store(StoreAppointmentGroupParticipantRequest $request): JsonResponse
    {
        $this->authorize('create', AppointmentGroupParticipant::class);

        $appointmentGroupParticipant = $this->appointmentGroupParticipantService->create($request->validated());

        return $this->sendSuccess(
            new AppointmentGroupParticipantResource($appointmentGroupParticipant),
            'AppointmentGroupParticipant başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $appointmentGroupParticipant = $this->appointmentGroupParticipantService->findByIdOrFail($id);

        return $this->sendSuccess(
            new AppointmentGroupParticipantResource($appointmentGroupParticipant),
            'AppointmentGroupParticipant başarıyla getirildi'
        );
    }

    public function update(UpdateAppointmentGroupParticipantRequest $request, string $id): JsonResponse
    {
        $appointmentGroupParticipant = $this->appointmentGroupParticipantService->update($id, $request->validated());

        return $this->sendSuccess(
            new AppointmentGroupParticipantResource($appointmentGroupParticipant),
            'AppointmentGroupParticipant başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->appointmentGroupParticipantService->delete($id);

        return $this->sendSuccess(
            null,
            'AppointmentGroupParticipant başarıyla silindi'
        );
    }
}
