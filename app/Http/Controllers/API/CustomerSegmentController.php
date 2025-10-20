<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCustomerSegmentRequest;
use App\Http\Requests\UpdateCustomerSegmentRequest;
use App\Http\Resources\CustomerSegmentResource;
use App\Services\CustomerSegmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerSegmentController extends BaseController
{
    public function __construct(
        protected CustomerSegmentService $customerSegmentService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $customerSegments = $this->customerSegmentService->getPaginated($perPage);

            return $this->sendPaginated(
                CustomerSegmentResource::collection($customerSegments),
                'CustomerSegments başarıyla getirildi'
            );
        }

        $customerSegments = $this->customerSegmentService->getAll();

        return CustomerSegmentResource::collection($customerSegments);
    }

    public function store(StoreCustomerSegmentRequest $request): JsonResponse
    {
        $customerSegment = $this->customerSegmentService->create($request->validated());

        return $this->sendSuccess(
            new CustomerSegmentResource($customerSegment),
            'CustomerSegment başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $customerSegment = $this->customerSegmentService->findByIdOrFail($id);

        return $this->sendSuccess(
            new CustomerSegmentResource($customerSegment),
            'CustomerSegment başarıyla getirildi'
        );
    }

    public function update(UpdateCustomerSegmentRequest $request, string $id): JsonResponse
    {
        $customerSegment = $this->customerSegmentService->update($id, $request->validated());

        return $this->sendSuccess(
            new CustomerSegmentResource($customerSegment),
            'CustomerSegment başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->customerSegmentService->delete($id);

        return $this->sendSuccess(
            null,
            'CustomerSegment başarıyla silindi'
        );
    }
}
