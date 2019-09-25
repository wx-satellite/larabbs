<?php

namespace App\Models;

class Reply extends Model
{
    protected $fillable = ['content'];


    public function user() {
        return $this->belongsTo(User::class, "user_id");
    }

    public function topic() {
        return $this->belongsTo(Topic::class,"topic_id");
    }
}
