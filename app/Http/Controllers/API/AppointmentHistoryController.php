<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentHistoryResource;
use App\Services\Contracts\AppointmentHistoryServiceInterface;
use App\Models\AppointmentHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentHistoryController extends Controller
{
    public function __construct(
        private AppointmentHistoryServiceInterface $appointmentHistoryService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', \App\Models\AppointmentHistory::class);

        if ($request->has('appointment_id')) {
            $appointmentId = $request->input('appointment_id');
            // Ensure appointment_id is a string before passing to service
            if (is_string($appointmentId)) {
                $history = $this->appointmentHistoryService->getAppointmentHistory($appointmentId);
            } else {
                 $history = collect(); // Or handle error
            }
        } elseif ($request->has('user_id')) {
             $userId = $request->input('user_id');
             if (is_string($userId)) {
                $history = $this->appointmentHistoryService->getChangesByUser(
                    $userId,
                    (int) $request->input('per_page', 15)
                );
             } else {
                 $history = collect();
             }

        } else {
            $history = $this->appointmentHistoryService->getRecentChanges(
                (int) $request->input('limit', 50)
            );
        }

        return AppointmentHistoryResource::collection($history);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', \App\Models\AppointmentHistory::class);

        $validated = $request->validate([
            'appointment_id' => 'required|uuid|exists:appointments,id',
            'action' => 'required|string|max:100',
            'old_values' => 'nullable|json',
            'new_values' => 'nullable|json',
        ]);

        $history = $this->appointmentHistoryService->logChange(
            $validated['appointment_id'],
            $validated['action'],
            [
                'old' => $validated['old_values'] ?? null,
                'new' => $validated['new_values'] ?? null,
            ]
        );

        return AppointmentHistoryResource::make($history)->response()->setStatusCode(201);
    }

    public function show(string $id): AppointmentHistoryResource
    {
        $history = \App\Models\AppointmentHistory::with(['appointment', 'user'])->findOrFail($id);
        $this->authorize('view', $history);

        return AppointmentHistoryResource::make($history);
    }

    public function recentChanges(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', \App\Models\AppointmentHistory::class);

        $limit = (int) $request->input('limit', 50);
        $changes = $this->appointmentHistoryService->getRecentChanges($limit);

        return AppointmentHistoryResource::collection($changes);
    }

    public function appointmentHistory(string $appointmentId): AnonymousResourceCollection
    {
        $this->authorize('viewAny', \App\Models\AppointmentHistory::class);

        $history = $this->appointmentHistoryService->getAppointmentHistory($appointmentId);

        return AppointmentHistoryResource::collection($history);
    }

    public function userChanges(Request $request, string $userId): AnonymousResourceCollection
    {
        $this->authorize('viewAny', \App\Models\AppointmentHistory::class);

        $changes = $this->appointmentHistoryService->getChangesByUser(
            $userId,
            (int) $request->input('per_page', 15)
        );

        return AppointmentHistoryResource::collection($changes);
    }
}
