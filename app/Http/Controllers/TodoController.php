<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TodoModel;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    // Renders the Inertia dashboard view
    public function dashboard() {
        $todos = TodoModel::where("user_id", Auth::user()->id)->get();
        return Inertia::render("Home", [
            "todos" => $todos
        ]);
    }
    
    // Returns all todos for the authenticated user as JSON
    public function getPosts(Request $request){
        $todos = TodoModel::where("user_id", $request->user()->id)->get();
        return response()->json($todos);
    }
    
    // Returns a single todo by ID as JSON
    public function getByIDPost($id){
        $todo = TodoModel::find($id);
        if (!$todo) {
            return response()->json(["message" => "Todo not found"], 404);
        }
        return response()->json($todo);
    }
    
    // Creates a new todo for the authenticated user
    public function post(Request $request){
        $request->validate([
            "title" => "required",
            "description" => "required",
            "is_completed" => "boolean"
        ]);
        $todo = TodoModel::create([
            "title" => $request->title,
            "description" => $request->description,
            "is_completed" => $request->is_completed,
            "user_id" => $request->user()->id
        ]);
        return response()->json($todo, 201);
    }
    
    // Updates an existing todo by ID
    public function updatePost(Request $request, $id){
        $todo = TodoModel::find($id);
        if(!$todo){
            return response()->json(["message" => "Todo not found"], 404);
        }
        $request->validate([
            "title" => "required",
            "description" => "required",
            "is_completed" => "boolean"
        ]);
        $todo->update([
            "title" => $request->title,
            "description" => $request->description,
            "is_completed" => $request->is_completed
        ]);
        return response()->json($todo);
    }
    
    // Deletes a todo by ID
    public function deletePost($id){
        $todo = TodoModel::find($id);
        if(!$todo){
            return response()->json(["message" => "Todo not found"], 404);
        }
        $todo->delete();
        return response()->json(["message" => "Todo deleted successfully"]);
    }
}
