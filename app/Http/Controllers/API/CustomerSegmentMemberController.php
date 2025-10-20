<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCustomerSegmentMemberRequest;
use App\Http\Requests\UpdateCustomerSegmentMemberRequest;
use App\Http\Resources\CustomerSegmentMemberResource;
use App\Services\CustomerSegmentMemberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerSegmentMemberController extends BaseController
{
    public function __construct(
        protected CustomerSegmentMemberService $customerSegmentMemberService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $customerSegmentMembers = $this->customerSegmentMemberService->getPaginated($perPage);

            return $this->sendPaginated(
                CustomerSegmentMemberResource::collection($customerSegmentMembers),
                'CustomerSegmentMembers başarıyla getirildi'
            );
        }

        $customerSegmentMembers = $this->customerSegmentMemberService->getAll();

        return CustomerSegmentMemberResource::collection($customerSegmentMembers);
    }

    public function store(StoreCustomerSegmentMemberRequest $request): JsonResponse
    {
        $customerSegmentMember = $this->customerSegmentMemberService->create($request->validated());

        return $this->sendSuccess(
            new CustomerSegmentMemberResource($customerSegmentMember),
            'CustomerSegmentMember başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $customerSegmentMember = $this->customerSegmentMemberService->findByIdOrFail($id);

        return $this->sendSuccess(
            new CustomerSegmentMemberResource($customerSegmentMember),
            'CustomerSegmentMember başarıyla getirildi'
        );
    }

    public function update(UpdateCustomerSegmentMemberRequest $request, string $id): JsonResponse
    {
        $customerSegmentMember = $this->customerSegmentMemberService->update($id, $request->validated());

        return $this->sendSuccess(
            new CustomerSegmentMemberResource($customerSegmentMember),
            'CustomerSegmentMember başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->customerSegmentMemberService->delete($id);

        return $this->sendSuccess(
            null,
            'CustomerSegmentMember başarıyla silindi'
        );
    }
}
