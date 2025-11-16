<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Contracts\AppointmentHistoryServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentHistoryController extends Controller
{
    public function __construct(
        private AppointmentHistoryServiceInterface $appointmentHistoryService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\AppointmentHistory::class);

        if ($request->has('appointment_id')) {
            $history = $this->appointmentHistoryService->getAppointmentHistory(
                $request->input('appointment_id')
            );
        } elseif ($request->has('user_id')) {
            $history = $this->appointmentHistoryService->getChangesByUser(
                $request->input('user_id'),
                $request->input('per_page', 15)
            );
        } else {
            $history = $this->appointmentHistoryService->getRecentChanges(
                $request->input('limit', 50)
            );
        }

        return response()->json($history);
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

        return response()->json($history, 201);
    }

    public function show(string $id): JsonResponse
    {
        $history = \App\Models\AppointmentHistory::with(['appointment', 'user'])->findOrFail($id);
        $this->authorize('view', $history);

        return response()->json($history);
    }

    public function recentChanges(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\AppointmentHistory::class);

        $limit = $request->input('limit', 50);
        $changes = $this->appointmentHistoryService->getRecentChanges($limit);

        return response()->json($changes);
    }

    public function appointmentHistory(string $appointmentId): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\AppointmentHistory::class);

        $history = $this->appointmentHistoryService->getAppointmentHistory($appointmentId);

        return response()->json($history);
    }

    public function userChanges(Request $request, string $userId): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\AppointmentHistory::class);

        $changes = $this->appointmentHistoryService->getChangesByUser(
            $userId,
            $request->input('per_page', 15)
        );

        return response()->json($changes);
    }
}
