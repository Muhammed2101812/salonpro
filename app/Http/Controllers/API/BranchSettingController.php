<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBranchSettingsRequest;
use App\Services\BranchSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchSettingController extends Controller
{
    public function __construct(
        private BranchSettingService $service
    ) {}

    /**
     * Get all settings for a branch
     */
    public function index(Request $request): JsonResponse
    {
        $branchId = $request->header('X-Branch-ID') ?? $request->user()->branch_id;
        $group = $request->query('group');

        $settings = $this->service->getAllForBranch($branchId, $group);

        return response()->json([
            'data' => $settings,
        ]);
    }

    /**
     * Get a specific setting
     */
    public function show(Request $request, string $key): JsonResponse
    {
        $branchId = $request->header('X-Branch-ID') ?? $request->user()->branch_id;

        $value = $this->service->get($branchId, $key);

        return response()->json([
            'data' => [
                'key' => $key,
                'value' => $value,
            ],
        ]);
    }

    /**
     * Update settings in bulk
     */
    public function update(UpdateBranchSettingsRequest $request): JsonResponse
    {
        $branchId = $request->header('X-Branch-ID') ?? $request->user()->branch_id;
        $settings = $request->validated()['settings'];

        $formattedSettings = [];
        foreach ($settings as $setting) {
            $formattedSettings[$setting['key']] = [
                'value' => $setting['value'],
                'type' => $setting['type'] ?? 'string',
                'group' => $setting['group'] ?? null,
                'is_encrypted' => $setting['is_encrypted'] ?? false,
            ];
        }

        $result = $this->service->bulkUpdate($branchId, $formattedSettings);

        if ($result) {
            return response()->json([
                'message' => 'Ayarlar başarıyla güncellendi.',
                'data' => $this->service->getAllForBranch($branchId),
            ]);
        }

        return response()->json([
            'message' => 'Ayarlar güncellenirken bir hata oluştu.',
        ], 500);
    }

    /**
     * Delete a setting
     */
    public function destroy(Request $request, string $key): JsonResponse
    {
        $branchId = $request->header('X-Branch-ID') ?? $request->user()->branch_id;

        $result = $this->service->remove($branchId, $key);

        if ($result) {
            return response()->json([
                'message' => 'Ayar başarıyla silindi.',
            ]);
        }

        return response()->json([
            'message' => 'Ayar silinirken bir hata oluştu.',
        ], 500);
    }

    /**
     * Get business settings
     */
    public function business(Request $request): JsonResponse
    {
        $branchId = $request->header('X-Branch-ID') ?? $request->user()->branch_id;

        $settings = $this->service->getBusinessSettings($branchId);

        return response()->json([
            'data' => $settings,
        ]);
    }

    /**
     * Get appointment settings
     */
    public function appointments(Request $request): JsonResponse
    {
        $branchId = $request->header('X-Branch-ID') ?? $request->user()->branch_id;

        $settings = $this->service->getAppointmentSettings($branchId);

        return response()->json([
            'data' => $settings,
        ]);
    }

    /**
     * Get notification settings
     */
    public function notifications(Request $request): JsonResponse
    {
        $branchId = $request->header('X-Branch-ID') ?? $request->user()->branch_id;

        $settings = $this->service->getNotificationSettings($branchId);

        return response()->json([
            'data' => $settings,
        ]);
    }

    /**
     * Get financial settings
     */
    public function financial(Request $request): JsonResponse
    {
        $branchId = $request->header('X-Branch-ID') ?? $request->user()->branch_id;

        $settings = $this->service->getFinancialSettings($branchId);

        return response()->json([
            'data' => $settings,
        ]);
    }
}
