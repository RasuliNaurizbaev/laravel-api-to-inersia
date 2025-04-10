<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TodoModel extends Model
{
    protected $table = "todo";
    protected $fillable = [
        "title",
        "description",
        "is_completed",
        "user_id"
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public $timestamps = true;
}
