<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Book;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory(50)->create();
        $books = Book::factory(20)->create();

        $books->each(function ($book) use ($users) {
            $randomUsers = $users->random(rand(0, 5));
            $randomUsers->each(function ($user) use ($book) {
                Review::factory()->create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                ]);
            });
        });
    }
}
