<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function registerUser(Request $request){
        $validated = $request->validate([
            "name" => "required|string",
            "email" => "required|email|unique:users",
            "password" => "required|min:6"	
        ]);
    
        $user = User::create($validated);
        $token = $user->createToken("secret-token")->plainTextToken;
        // return response()->json(["message" => "User is created", $user, $token], 201);
        return redirect("/dashboard");
    }
    
    
    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    // Проверка пользователя
    $user = User::where('email', $credentials['email'])->first();

    if (!$user || !Hash::check($credentials['password'], $user->password)) {
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    // Логиним через Laravel Auth
    Auth::login($user);

    // Не нужен токен, если мы не используем API-авторизацию
    return redirect()->route('dashboard')->with([
        'message' => 'User successfully logged in'
    ]);
}

public function logout()
{
    if (Auth::check()) {
        Auth::user()->tokens()->delete(); // На случай, если хочешь токены удалить при выходе
        Auth::logout();
    }

    return redirect()->route('login')->with([
        'message' => 'User successfully logged out'
    ]);
}
    
    public function getUserByID(Request $request, $id){
        $user = User::find($id);
        if(!$user){
            return response()->json(["message" => "User not found"], 404);
        }
        return response()->json($user, 200);
    }
    
    public function getAllUser(){
        $users = User::all();
        if($users->isEmpty()) return response()->json(["message" => "Users is empty"], 404);	
        return response()->json($users, 200);
    }
    
    public function update(Request $request, $id){
        $validated = $request->validate([
            "name" => "required|string",
            "email" => "required|email|unique:users",
            "password" => "required|min:6"
        ]);
        $user = User::find($id);
        if(!$user) return response()->json(["message" => "User is not found"], 404);
        $user->update($validated);
        return response()->json(["message" => "User success updated"], 202);
    } 
    
    public function delete(Request $request, $id){
        $user = User::find($id);
        if(!$user) return response()->json(["message" => "User not found"], 404);
        $user->delete();
        return response()->json(["message" => "User is success deleted"], 202);
    }
}
