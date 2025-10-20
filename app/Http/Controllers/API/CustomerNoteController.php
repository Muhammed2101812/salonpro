<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCustomerNoteRequest;
use App\Http\Requests\UpdateCustomerNoteRequest;
use App\Http\Resources\CustomerNoteResource;
use App\Services\CustomerNoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerNoteController extends BaseController
{
    public function __construct(
        protected CustomerNoteService $customerNoteService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $customerNotes = $this->customerNoteService->getPaginated($perPage);

            return $this->sendPaginated(
                CustomerNoteResource::collection($customerNotes),
                'CustomerNotes başarıyla getirildi'
            );
        }

        $customerNotes = $this->customerNoteService->getAll();

        return CustomerNoteResource::collection($customerNotes);
    }

    public function store(StoreCustomerNoteRequest $request): JsonResponse
    {
        $customerNote = $this->customerNoteService->create($request->validated());

        return $this->sendSuccess(
            new CustomerNoteResource($customerNote),
            'CustomerNote başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $customerNote = $this->customerNoteService->findByIdOrFail($id);

        return $this->sendSuccess(
            new CustomerNoteResource($customerNote),
            'CustomerNote başarıyla getirildi'
        );
    }

    public function update(UpdateCustomerNoteRequest $request, string $id): JsonResponse
    {
        $customerNote = $this->customerNoteService->update($id, $request->validated());

        return $this->sendSuccess(
            new CustomerNoteResource($customerNote),
            'CustomerNote başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->customerNoteService->delete($id);

        return $this->sendSuccess(
            null,
            'CustomerNote başarıyla silindi'
        );
    }
}
