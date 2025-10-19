<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Resources\SettingResource;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends BaseController
{
    public function __construct(protected SettingService $settingService) {}

    public function index()
    {
        return SettingResource::collection($this->settingService->getAll());
    }

    public function store(Request $request)
    {
        return $this->sendSuccess(new SettingResource($this->settingService->create($request->all())), 'Setting created', 201);
    }

    public function update(Request $request, string $id)
    {
        return $this->sendSuccess(new SettingResource($this->settingService->update($id, $request->all())), 'Setting updated');
    }
}
