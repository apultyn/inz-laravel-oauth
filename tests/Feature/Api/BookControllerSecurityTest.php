<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use App\Models\Review;


class BookControllerSecurityTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_get_books(): void
    {
        Book::factory()->create([
            'title' => 'Tytuł 1',
            'author' => 'Ktoś'
        ]);
        Book::factory()->create([
            'title' => 'Inny tytus',
            'author' => 'Autor'
        ]);
        Book::factory()->create([
            'title' => 'Jeszcze inny',
            'author' => 'Ktoś inny'
        ]);

        $this->getJson('/api/books/')
            ->assertStatus(200)
            ->assertJsonCount(3, );

        $this->getJson('/api/books/?searchString=tyt')
            ->assertStatus(200)
            ->assertJsonCount(2, );

        $this->getJson('/api/books/?searchString=błąd')
            ->assertStatus(200)
            ->assertJsonCount(0, );
        ;
    }

    public function test_get_book()
    {
        $book = Book::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Review::factory()->create(['book_id' => $book->id, 'user_id' => $user1->id]);
        Review::factory()->create(['book_id' => $book->id, 'user_id' => $user2->id]);

        $this->getJson("/api/books/{$book->id}")
            ->assertStatus(200)
            ->assertJsonCount(2, 'reviews')
            ->assertJsonStructure([
                'id',
                'title',
                'author',
                'reviews' => [
                    '*' => [
                        'id',
                        'stars',
                        'comment',
                        'user_email',
                        'book_id'
                    ]
                ]
            ]);
    }

    public function test_create_book_unauth(): void
    {
        $bookData = ['title' => 'Nowa Książka', 'author' => 'Autor'];
        $this->postJson('/api/books', $bookData)
            ->assertStatus(401);
    }

    public function test_create_book_user(): void
    {
        $user = User::factory()->create();
        $bookData = ['title' => 'Nowa Książka', 'author' => 'Autor'];

        $this->actingAs($user, 'api')
            ->postJson('/api/books', $bookData)
            ->assertStatus(403);
    }

    public function test_create_book_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $bookData = ['title' => 'Nowa Książka od Admina', 'author' => 'Admin'];

        $this->actingAs($admin, 'api')
            ->postJson('/api/books', $bookData)
            ->assertStatus(201);
    }

    public function test_update_book_unauth(): void
    {
        $book = Book::factory()->create();

        $updateData = ['title' => 'Nowy tytuł'];

        $this->patchJson("/api/books/{$book->id}", $updateData)
            ->assertStatus(401);
    }

    public function test_update_book_user(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $updateData = ['title' => 'Nowy tytuł'];

        $this->actingAs($user, 'api')
            ->patchJson("/api/books/{$book->id}", $updateData)
            ->assertStatus(403);
    }

    public function test_update_book_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create();

        $updateData = ['title' => 'Nowy tytuł'];

        $this->actingAs($admin, 'api')
            ->patchJson("/api/books/{$book->id}", $updateData)
            ->assertStatus(200);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Nowy tytuł',
            'author' => $book->author
        ]);
    }

    public function test_delete_book_unauth(): void
    {
        $book = Book::factory()->create();

        $this->delete("/api/books/{$book->id}")
            ->assertStatus(401);
    }

    public function test_delete_book_user(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAs($user, 'api')
            ->delete("/api/books/{$book->id}")
            ->assertStatus(403);
    }

    public function test_delete_book_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create();


        $this->actingAs($admin, 'api')
            ->delete("/api/books/{$book->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->author
        ]);
    }
}
