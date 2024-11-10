<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $fillable = ['title', 'author', 'isbn', 'published_date', 'status'];


}
