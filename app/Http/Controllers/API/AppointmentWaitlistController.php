<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreAppointmentWaitlistRequest;
use App\Http\Requests\UpdateAppointmentWaitlistRequest;
use App\Http\Resources\AppointmentWaitlistResource;
use App\Services\AppointmentWaitlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentWaitlistController extends BaseController
{
    public function __construct(
        protected AppointmentWaitlistService $appointmentWaitlistService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $appointmentWaitlists = $this->appointmentWaitlistService->getPaginated($perPage);

            return $this->sendPaginated(
                AppointmentWaitlistResource::collection($appointmentWaitlists),
                'AppointmentWaitlists başarıyla getirildi'
            );
        }

        $appointmentWaitlists = $this->appointmentWaitlistService->getAll();

        return AppointmentWaitlistResource::collection($appointmentWaitlists);
    }

    public function store(StoreAppointmentWaitlistRequest $request): JsonResponse
    {
        $appointmentWaitlist = $this->appointmentWaitlistService->create($request->validated());

        return $this->sendSuccess(
            new AppointmentWaitlistResource($appointmentWaitlist),
            'AppointmentWaitlist başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $appointmentWaitlist = $this->appointmentWaitlistService->findByIdOrFail($id);

        return $this->sendSuccess(
            new AppointmentWaitlistResource($appointmentWaitlist),
            'AppointmentWaitlist başarıyla getirildi'
        );
    }

    public function update(UpdateAppointmentWaitlistRequest $request, string $id): JsonResponse
    {
        $appointmentWaitlist = $this->appointmentWaitlistService->update($id, $request->validated());

        return $this->sendSuccess(
            new AppointmentWaitlistResource($appointmentWaitlist),
            'AppointmentWaitlist başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->appointmentWaitlistService->delete($id);

        return $this->sendSuccess(
            null,
            'AppointmentWaitlist başarıyla silindi'
        );
    }
}
