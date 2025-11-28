<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreAppointmentCancellationRequest;
use App\Http\Requests\UpdateAppointmentCancellationRequest;
use App\Http\Resources\AppointmentCancellationResource;
use App\Services\AppointmentCancellationService;
use App\Models\AppointmentCancellation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentCancellationController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected AppointmentCancellationService $appointmentCancellationService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', AppointmentCancellation::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $cancellations = $this->appointmentCancellationService->getPaginated($perPage);

            return $this->sendPaginated(
                AppointmentCancellationResource::collection($cancellations),
                'Randevu iptalleri başarıyla getirildi'
            );
        }

        $cancellations = $this->appointmentCancellationService->getAll();

        return AppointmentCancellationResource::collection($cancellations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentCancellationRequest $request): JsonResponse
    {
        $this->authorize('create', AppointmentCancellation::class);

        $cancellation = $this->appointmentCancellationService->create($request->validated());

        return $this->sendSuccess(
            new AppointmentCancellationResource($cancellation),
            'Randevu iptali başarıyla oluşturuldu',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $cancellation = $this->appointmentCancellationService->findByIdOrFail($id);

        return $this->sendSuccess(
            new AppointmentCancellationResource($cancellation),
            'Randevu iptali başarıyla getirildi'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentCancellationRequest $request, string $id): JsonResponse
    {
        $cancellation = $this->appointmentCancellationService->update($id, $request->validated());

        return $this->sendSuccess(
            new AppointmentCancellationResource($cancellation),
            'Randevu iptali başarıyla güncellendi'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->appointmentCancellationService->delete($id);

        return $this->sendSuccess(
            null,
            'Randevu iptali başarıyla silindi'
        );
    }
}
