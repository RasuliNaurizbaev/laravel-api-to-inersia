<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TodoController;
use Inertia\Inertia;

Route::get('/test', function () {
    return response()->json([
        'message' => 'Welcome to the API',
    ]);
})->withoutMiddleware(['web']);


Route::middleware(["auth:sanctum"])->group(function(){
    //User
    Route::get("/users", [UserController::class, "getAllUser"]);
    Route::get("/user/{id}", [UserController::class, "getUserByID"]);
    Route::put("/user/{id}", [UserController::class, "update"])->withoutMiddleware(['web']);
    Route::delete("/user/{id}", [UserController::class, "delete"])->withoutMiddleware(['web']);
    
    //Todo
    Route::get("/posts", [TodoController::class, "getPosts"]);
    Route::get("/post/{id}", [TodoController::class, "getByIDPost"]);
    Route::post("/post", [TodoController::class, "post"])->withoutMiddleware(['web']);
    Route::put("/post/{id}", [TodoController::class, "updatePost"])->withoutMiddleware(['web']);
    Route::delete("/post/{id}", [TodoController::class, "deletePost"])->withoutMiddleware(['web']);
});
Route::post("/register", [UserController::class, "registerUser"]);
Route::post("/login", [UserController::class, "login"]);

Route::post("/logout", [UserController::class, "logout"]);
Route::get("/register", function () {
    return Inertia::render("Register");
});
Route::get("/", function () {
    return Inertia::render("Login");
});
Route::get("/dashboard", function () {
    return Inertia::render("Home");
});