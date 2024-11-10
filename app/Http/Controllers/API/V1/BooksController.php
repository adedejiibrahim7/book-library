<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\BookRequest;
use App\Http\Resources\API\V1\BookResource;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BooksController extends Controller
{

    public function __construct(private readonly BookService $bookService)
    {
    }

    public function index(Request $request)
    {
        $books = Book::when($request->has('author'), function ($query) use($request){
            $query->where('author', $request->get('author'));
        })->when($request->has('availability'), function ($query) use ($request){
            $query->where('availability', $request->get('availability'));
        })->latest()->paginate(10);

        return \response()->json([
            'message' => 'Books fetched successfully',
            'data' => BookResource::collection($books)
        ]);
    }

    public function show(Book $book)
    {
        return \response()->json([
            'message' => 'Book fetched successfully',
            'data' => BookResource::make($book)
        ]);
    }


    public function store(BookRequest $request): JsonResponse
    {
        $data = $request->validated();

        $book = $this->bookService->store($data);

        return response()->json([
           'message' => 'Book created successfully',
           'data' =>  BookResource::make($book)
        ]);
    }

    public function update(Book $book, BookRequest $request): JsonResponse
    {
        $data = $request->validated();

        $book = $this->bookService->update($book, $data);

        return response()->json([
            'message' => 'Book updated successfully',
            'data' =>  BookResource::make($book)
        ]);
    }

    public function destroy(Book $book): JsonResponse|int
    {
        if($this->bookService->delete($book)){
            return response()->json([], Response::HTTP_NO_CONTENT);
        }

        return response()->json(['message' => 'Unable to delete book. Please try again']);
    }
}
