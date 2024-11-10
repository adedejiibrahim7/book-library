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
use OpenApi\Annotations as OA;

class BooksController extends Controller
{

    public function __construct(private readonly BookService $bookService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/books",
     *     summary="Fetch books with optional filters for author and availability",
     *     description="Retrieve a list of books, with optional filters for author and availability. Results are paginated with a limit of 10 items per page.",
     *     tags={"Books"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="author",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filter books by author name."
     *     ),
     *     @OA\Parameter(
     *         name="availability",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filter books by availability status."
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Page number for pagination."
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Books fetched successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Books fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
*                            @OA\Property(property="id", type="integer", example="b75bd679-00ec-432c-816a-f2ec8fee8178"),
     *                       @OA\Property(property="title", type="string", example="Half of a Yellow Sun"),
     *                       @OA\Property(property="author", type="string", example="Chinua Achebe"),
     *                       @OA\Property(property="isbn", type="string", example="ISBN"),
     *                       @OA\Property(property="status", type="string", example="available"),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized. Bearer token is required.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid query parameter.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Invalid request parameters.")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $books = Book::when($request->has('author'), function ($query) use($request){
            $query->where('author', $request->get('author'));
        })->when($request->has('availability'), function ($query) use ($request){
            $query->where('availability', $request->get('availability'));
        })->when($request->has('search'), function ($query) use ($request){
            $query->where('name', 'like', "%$request->get('search')%")
                ->orWhere('author', 'like', "%$request->get('search')%")
                ->orWhere('title', 'like', "%$request->get('search')%");
        })

        ->latest()->paginate(10);

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
