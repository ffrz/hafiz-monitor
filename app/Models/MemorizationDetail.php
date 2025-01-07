<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemorizationDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'memorization_id',
        'ayah_id',
        'score',
        'notes',
        'weighted_score',
    ];

    public function memorization()
    {
        return $this->belongsTo(Memorization::class);
    }

    public function ayah()
    {
        return $this->belongsTo(Ayah::class);
    }
}
