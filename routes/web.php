<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::get('/posts', [PostController::class, 'index'])->name('posts.index'); // Public route

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('posts', PostController::class)->except(['index','update']);
    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::get('/user-feed', [PostController::class, 'userPosts'])->name('posts.userFeed');
    Route::post('/posts/update/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::post('/posts/{post}/repost', [PostController::class, 'repost'])->name('posts.repost');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');

});
