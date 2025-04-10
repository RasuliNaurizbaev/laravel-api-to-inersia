<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TodoController;
use Inertia\Inertia;

Route::get('/test', function () {
    return response()->json([
        'message' => 'Welcome to the API',
    ]);
});

// Routes protected by Sanctum authentication
Route::middleware(["auth:sanctum"])->group(function(){
    // User Routes
    Route::get("/users", [UserController::class, "getAllUser"]);
    Route::get("/user/{id}", [UserController::class, "getUserByID"]);
    Route::put("/user/{id}", [UserController::class, "update"]);
    Route::delete("/user/{id}", [UserController::class, "delete"]);
    
    Route::post("/logout", [UserController::class, "logout"]);
    
    // Todo Routes (API endpoints)
    Route::get('/todos', [TodoController::class, 'getPosts']);
    Route::get('/todos/{id}', [TodoController::class, 'getByIDPost']);
    Route::post('/todos', [TodoController::class, 'post']);
    Route::put('/todos/{id}', [TodoController::class, 'updatePost']);
    Route::patch('/todos/{id}', [TodoController::class, 'updatePost']);
    Route::delete('/todos/{id}', [TodoController::class, 'deletePost']);

    // Dashboard (Inertia view)
    Route::get("/dashboard", [TodoController::class,"dashboard"]);
});

// Public Routes for authentication
Route::post("/register", [UserController::class, "registerUser"]);
Route::post("/login", [UserController::class, "login"]);

Route::get("/register", function () {
    return Inertia::render("Register");
});
Route::get("/", function () {
    return Inertia::render("Login");
})->name("login");
