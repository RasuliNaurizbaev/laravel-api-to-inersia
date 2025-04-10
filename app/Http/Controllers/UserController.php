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
        /* 
        Validation
        */
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        /*
        Database Insert
        */
        $user = User::create($validated);

        Auth::login($user);

        return redirect("/dashboard");
    }
    
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Проверка пользователя
        $user = User::where('email', $credentials['email'])->first();

        if (Auth::attempt($credentials)) {
            $request -> session()->regenerate();
            return redirect("/dashboard");
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function logout(Request $request)
    {
        auth()->guard('web')->logout();
        
        $request->user()->tokens()->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect("/");
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
