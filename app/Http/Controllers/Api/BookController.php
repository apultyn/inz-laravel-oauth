<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Services\BookService;
use App\Http\Resources\BookResource;
use App\Http\Requests\SaveBookRequest;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    public function __construct(private BookService $bookService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $searchString = $request->query('searchString') ?? "";
        $books = $this->bookService->getBooks($searchString);
        return response()->json(BookResource::collection($books), 200);
    }

    public function store(SaveBookRequest $req): JsonResponse
    {
        $book = $this->bookService->createBook($req->validated());
        return response()->json(new BookResource($book), 201);
    }

    public function show(Book $book): JsonResponse
    {
        $book->load(['reviews.user', 'reviews.book']);
        return response()->json(new BookResource($book));
    }

    public function update(UpdateBookRequest $req, Book $book)
    {
        $updatedBook = $this->bookService->updateBook($book, $req->validated());
        return response()->json(new BookResource($updatedBook));
    }

    public function destroy(Book $book)
    {
        $this->bookService->deleteBook($book);
        return response([], 204);
    }
}
