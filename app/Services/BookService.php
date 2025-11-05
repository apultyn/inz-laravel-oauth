<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;

class BookService
{
    public function getBooks(string $searchString): Collection
    {
        return Book::where(function ($query) use ($searchString) {
            $query->where('title', 'like', '%' . $searchString . '%')
                ->orWhere('author', 'like', '%' . $searchString . '%');
        })->get();
    }

    public function createBook(array $data): Book
    {
        return Book::create($data);
    }

    public function updateBook(Book $book, array $data): Book
    {
        $book->update($data);
        return $book;
    }

    public function deleteBook(Book $book): bool
    {
        $book->delete();
        return true;
    }
}