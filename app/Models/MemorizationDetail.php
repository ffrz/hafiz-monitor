<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemorizationDetail extends Model
{
    protected $timestamps = false;

    protected $fillable = [
        'memorization_id',
        'ayah_id',
        'score',
        'notes',
    ];

    public function memorization()
    {
        return $this->belongsTo(Memorization::class);
    }
}
