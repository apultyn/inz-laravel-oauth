<?php

namespace App\Services;

use App\Models\Review;
use Illuminate\Database\Eloquent\Collection;

class ReviewService
{
    public function getReviews(): Collection
    {
        return Review::all();
    }

    public function createReview(array $data): Review
    {
        return Review::create($data);
    }

    public function updateReview(Review $review, array $data): Review
    {
        $review->update($data);
        return $review;
    }

    public function deleteReview(Review $review): bool
    {
        $review->delete();
        return true;
    }
}