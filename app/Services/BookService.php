<?php

namespace App\Services;

use App\Models\Book;

class BookService
{

    public function store(array $data): Book
    {
        return Book::create($data);
    }

    public function update(Book $book, array $data)
    {
        return $book->update($data);
    }

    public function delete(Book $book)
    {
        return $book->delete();
    }

}
