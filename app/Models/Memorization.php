<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Memorization extends Model
{
    public $fillable = [
        'user_id',
        'hafiz_id',
        'title',
        'score',
        'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hafiz()
    {
        return $this->belongsTo(Hafiz::class);
    }

    public function details()
    {
        return $this->hasMany(MemorizationDetail::class);
    }
}
