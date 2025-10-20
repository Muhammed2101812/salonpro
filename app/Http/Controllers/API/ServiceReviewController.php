<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreServiceReviewRequest;
use App\Http\Requests\UpdateServiceReviewRequest;
use App\Http\Resources\ServiceReviewResource;
use App\Services\ServiceReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceReviewController extends BaseController
{
    public function __construct(
        protected ServiceReviewService $serviceReviewService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $serviceReviews = $this->serviceReviewService->getPaginated($perPage);

            return $this->sendPaginated(
                ServiceReviewResource::collection($serviceReviews),
                'ServiceReviews başarıyla getirildi'
            );
        }

        $serviceReviews = $this->serviceReviewService->getAll();

        return ServiceReviewResource::collection($serviceReviews);
    }

    public function store(StoreServiceReviewRequest $request): JsonResponse
    {
        $serviceReview = $this->serviceReviewService->create($request->validated());

        return $this->sendSuccess(
            new ServiceReviewResource($serviceReview),
            'ServiceReview başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $serviceReview = $this->serviceReviewService->findByIdOrFail($id);

        return $this->sendSuccess(
            new ServiceReviewResource($serviceReview),
            'ServiceReview başarıyla getirildi'
        );
    }

    public function update(UpdateServiceReviewRequest $request, string $id): JsonResponse
    {
        $serviceReview = $this->serviceReviewService->update($id, $request->validated());

        return $this->sendSuccess(
            new ServiceReviewResource($serviceReview),
            'ServiceReview başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->serviceReviewService->delete($id);

        return $this->sendSuccess(
            null,
            'ServiceReview başarıyla silindi'
        );
    }
}
