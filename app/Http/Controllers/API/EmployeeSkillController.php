<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreEmployeeSkillRequest;
use App\Http\Requests\UpdateEmployeeSkillRequest;
use App\Http\Resources\EmployeeSkillResource;
use App\Services\EmployeeSkillService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeSkillController extends BaseController
{
    public function __construct(
        protected EmployeeSkillService $employeeSkillService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $employeeSkills = $this->employeeSkillService->getPaginated($perPage);

            return $this->sendPaginated(
                EmployeeSkillResource::collection($employeeSkills),
                'EmployeeSkills başarıyla getirildi'
            );
        }

        $employeeSkills = $this->employeeSkillService->getAll();

        return EmployeeSkillResource::collection($employeeSkills);
    }

    public function store(StoreEmployeeSkillRequest $request): JsonResponse
    {
        $employeeSkill = $this->employeeSkillService->create($request->validated());

        return $this->sendSuccess(
            new EmployeeSkillResource($employeeSkill),
            'EmployeeSkill başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $employeeSkill = $this->employeeSkillService->findByIdOrFail($id);

        return $this->sendSuccess(
            new EmployeeSkillResource($employeeSkill),
            'EmployeeSkill başarıyla getirildi'
        );
    }

    public function update(UpdateEmployeeSkillRequest $request, string $id): JsonResponse
    {
        $employeeSkill = $this->employeeSkillService->update($id, $request->validated());

        return $this->sendSuccess(
            new EmployeeSkillResource($employeeSkill),
            'EmployeeSkill başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->employeeSkillService->delete($id);

        return $this->sendSuccess(
            null,
            'EmployeeSkill başarıyla silindi'
        );
    }
}
