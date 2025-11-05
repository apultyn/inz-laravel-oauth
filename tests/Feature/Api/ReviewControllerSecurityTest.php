<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use App\Models\Review;


class ReviewControllerSecurityTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_create_review_unauth(): void
    {
        $book = Book::factory()->create();

        $reviewData = [
            'book_id' => $book->id,
            'stars' => 5,
            'comment' => 'fine'
        ];
        $this->postJson('/api/reviews', $reviewData)
            ->assertStatus(401);
    }

    public function test_create_review_user(): void
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();

        $reviewData = [
            'book_id' => $book->id,
            'stars' => 5,
            'comment' => 'fine'
        ];
        $this->actingAs($user, 'api')
            ->postJson('/api/reviews', $reviewData)
            ->assertStatus(201);

        $this->assertDatabaseHas('reviews', [...$reviewData, 'user_id' => $user->id]);
    }

    public function test_create_review_admin(): void
    {
        $book = Book::factory()->create();
        $admin = User::factory()->admin()->create();

        $reviewData = [
            'book_id' => $book->id,
            'stars' => 5,
            'comment' => 'fine'
        ];
        $this->actingAs($admin, 'api')
            ->postJson('/api/reviews', $reviewData)
            ->assertStatus(201);

        $this->assertDatabaseHas('reviews', [...$reviewData, 'user_id' => $admin->id]);
    }

    public function test_update_review_unauth(): void
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create(['book_id' => $book->id, 'user_id' => $user->id]);

        $reviewData = [
            'comment' => 'fine'
        ];

        $this->patchJson("api/reviews/{$review->id}", $reviewData)
            ->assertStatus(401);
    }

    public function test_update_review_user(): void
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create(['book_id' => $book->id, 'user_id' => $user->id]);

        $reviewData = [
            'comment' => 'fine'
        ];

        $this->actingAs($user, 'api')
            ->patchJson("api/reviews/{$review->id}", $reviewData)
            ->assertStatus(403);
    }

    public function test_update_review_admin(): void
    {
        $book = Book::factory()->create();
        $admin = User::factory()->admin()->create();
        $review = Review::factory()->create(['book_id' => $book->id, 'user_id' => $admin->id]);

        $reviewData = [
            'comment' => 'fine',
            'stars' => 3
        ];

        $this->actingAs($admin, 'api')
            ->patchJson("api/reviews/{$review->id}", $reviewData)
            ->assertStatus(200);

        $this->assertDatabaseHas('reviews', array_merge(
            ['id' => $review->id, 'book_id' => $book->id, 'user_id' => $admin->id, 'stars' => 3, 'comment' => 'fine'],
        ));
    }

    public function test_delete_review_unauth(): void
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create(['book_id' => $book->id, 'user_id' => $user->id]);

        $this->deleteJson("/api/reviews/{$review->id}")
            ->assertStatus(401);
    }

    public function test_delete_review_user(): void
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create(['book_id' => $book->id, 'user_id' => $user->id]);

        $this->actingAs($user, 'api')
            ->deleteJson("/api/reviews/{$review->id}")
            ->assertStatus(403);
    }

    public function test_delete_review_admin(): void
    {
        $book = Book::factory()->create();
        $admin = User::factory()->admin()->create();
        $review = Review::factory()->create(['book_id' => $book->id, 'user_id' => $admin->id]);

        $this->actingAs($admin, 'api')
            ->deleteJson("/api/reviews/{$review->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('reviews', [$review]);
    }
}