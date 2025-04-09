<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TodoModel;
class TodoController extends Controller
{
    public function getPosts(Request $request){
        $todos = TodoModel::where("user_id", $request->user()->id)->get();
        return response()->json($todos);
    }
    public function getByIDPost($id){
        $todo = TodoModel::find($id);
        if(!$todo){
            return response()->json([
                "message" => "Todo not found"
            ], 404);
        }
        return response()->json($todo);
    }
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
        return response()->json($todo);
    }
    public function updatePost(Request $request, $id){
        $todo = TodoModel::find($id);
        if(!$todo){
            return response()->json([
                "message" => "Todo not found"
            ], 404);
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
    public function deletePost($id){
        $todo = TodoModel::find($id);
        if(!$todo){
            return response()->json([
                "message" => "Todo not found"
            ], 404);
        }
        $todo->delete();
        return response()->json([
            "message" => "Todo deleted successfully"
        ]);
    }
}
