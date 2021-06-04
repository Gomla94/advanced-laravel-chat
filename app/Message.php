<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];
    public $timestamps = false;


    public function user()
    {
        return $this->belongsTo(User::class, 'from', 'id');
    }

    // public function setCreatedAtAttribute($value)
    // {
    //     $this->attributes['created_at'] = date_format($value, "g:i A");
    // }
}
