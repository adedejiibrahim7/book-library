<?php

use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\BooksController;
use Illuminate\Support\Facades\Route;



Route::prefix('v1')->group(function (){
    Route::prefix('auth')->controller(AuthController::class)->group(function(){
       Route::post('register', 'register')->name('auth.register');
       Route::post('login', 'login')->name('auth.login');
    });

    Route::middleware('auth.api')->group(function() {
        Route::resource('books', BooksController::class);
    });
});

