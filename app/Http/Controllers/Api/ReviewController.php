<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\ReviewService;
use App\Http\Resources\ReviewResource;
use App\Http\Requests\SaveReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function __construct(private ReviewService $reviewService)
    {
    }

    public function index(): JsonResponse
    {
        $reviews = $this->reviewService->getReviews();
        return response()->json(ReviewResource::collection($reviews), 200);
    }

    public function store(SaveReviewRequest $req): JsonResponse
    {
        $validatedData = $req->validated();

        $data = array_merge($validatedData, [
            'user_id' => auth()->id()
        ]);

        $review = $this->reviewService->createReview($data);
        return response()->json(new ReviewResource($review), 201);
    }

    public function show(Review $review): JsonResponse
    {
        $review->load(['book', 'user']);
        return response()->json(new ReviewResource($review));
    }

    public function update(UpdateReviewRequest $req, Review $review)
    {
        $updatedReview = $this->reviewService->updateReview($review, $req->validated());
        return response()->json(new ReviewResource($updatedReview));
    }

    public function destroy(Review $review)
    {
        $this->reviewService->deleteReview($review);
        return response([], 204);
    }
}
