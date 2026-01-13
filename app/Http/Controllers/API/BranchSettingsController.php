<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Models\BranchSetting;
use App\Services\BranchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchSettingsController extends BaseController
{
    public function __construct(
        protected BranchService $branchService
    ) {}

    /**
     * Get settings for the branch.
     */
    public function index(string $branchId): JsonResponse
    {
        $branch = $this->branchService->findByIdOrFail($branchId);

        $this->authorize('view', $branch);

        $settings = $branch->settings;

        $data = [
            'business' => [],
            'hours' => [],
            'appointments' => [],
            'notifications' => [],
            'financial' => [],
        ];

        foreach ($settings as $setting) {
            $group = $setting->group ?? 'general';
            if (!isset($data[$group])) {
                $data[$group] = [];
            }
            $data[$group][$setting->key] = $setting->value;
        }

        return $this->sendSuccess($data);
    }

    /**
     * Update settings for the branch.
     */
    public function update(Request $request, string $branchId): JsonResponse
    {
        $branch = $this->branchService->findByIdOrFail($branchId);

        $this->authorize('update', $branch);

        $data = $request->all();

        foreach ($data as $group => $settings) {
            if (!is_array($settings)) continue;

            foreach ($settings as $key => $value) {
                $type = 'string';
                if (is_bool($value)) $type = 'boolean';
                elseif (is_int($value)) $type = 'integer';
                elseif (is_float($value)) $type = 'float';
                elseif (is_array($value)) $type = 'json';

                BranchSetting::updateOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'key' => $key,
                    ],
                    [
                        'value' => $value,
                        'group' => $group,
                        'type' => $type,
                    ]
                );
            }
        }

        return $this->index($branchId);
    }
}
