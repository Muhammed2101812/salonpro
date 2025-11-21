<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceReviewResource;
use App\Services\Contracts\ServiceReviewServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceReviewController extends Controller
{
    public function __construct(
        private ServiceReviewServiceInterface $serviceReviewService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', \App\Models\ServiceReview::class);

        if ($request->has('service_id')) {
            $reviews = $this->serviceReviewService->getServiceReviews(
                $request->input('service_id'),
                $request->input('per_page', 15)
            );
        } else {
            $reviews = \App\Models\ServiceReview::with(['service', 'customer', 'appointment'])
                ->paginate($request->input('per_page', 15));
        }

        return ServiceReviewResource::collection($reviews);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', \App\Models\ServiceReview::class);

        $validated = $request->validate([
            'service_id' => 'required|uuid|exists:services,id',
            'customer_id' => 'required|uuid|exists:customers,id',
            'appointment_id' => 'nullable|uuid|exists:appointments,id',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
            'is_published' => 'nullable|boolean',
        ]);

        $review = $this->serviceReviewService->createReview($validated);

        return ServiceReviewResource::make($review)->response()->setStatusCode(201);
    }

    public function show(string $id): ServiceReviewResource
    {
        $review = \App\Models\ServiceReview::with(['service', 'customer', 'appointment'])->findOrFail($id);
        $this->authorize('view', $review);

        return ServiceReviewResource::make($review);
    }

    public function update(Request $request, string $id): ServiceReviewResource
    {
        $review = \App\Models\ServiceReview::findOrFail($id);
        $this->authorize('update', $review);

        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'review_text' => 'sometimes|string|max:1000',
            'is_published' => 'sometimes|boolean',
        ]);

        $updated = $this->serviceReviewService->updateReview($id, $validated);

        return ServiceReviewResource::make($updated);
    }

    public function destroy(string $id): JsonResponse
    {
        $review = \App\Models\ServiceReview::findOrFail($id);
        $this->authorize('delete', $review);

        $this->serviceReviewService->deleteReview($id);

        return response()->json(['message' => 'Review deleted successfully']);
    }

    public function approve(string $id): ServiceReviewResource
    {
        $review = \App\Models\ServiceReview::findOrFail($id);
        $this->authorize('update', $review);

        $approved = $this->serviceReviewService->approveReview($id);

        return ServiceReviewResource::make($approved);
    }

    public function reject(Request $request, string $id): ServiceReviewResource
    {
        $review = \App\Models\ServiceReview::findOrFail($id);
        $this->authorize('update', $review);

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $rejected = $this->serviceReviewService->rejectReview($id, $validated['reason']);

        return ServiceReviewResource::make($rejected);
    }

    public function published(Request $request, string $serviceId): AnonymousResourceCollection
    {
        $reviews = $this->serviceReviewService->getPublishedReviews(
            $serviceId,
            $request->input('per_page', 15)
        );

        return ServiceReviewResource::collection($reviews);
    }

    public function averageRating(string $serviceId): JsonResponse
    {
        $rating = $this->serviceReviewService->getServiceAverageRating($serviceId);

        return response()->json([
            'service_id' => $serviceId,
            'average_rating' => $rating,
        ]);
    }
}
