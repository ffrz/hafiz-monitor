<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ayah extends Model
{
    protected $table = 'ayahs';

    protected $fillable = [];

    public function surah()
    {
        return $this->belongsTo(Surah::class);
    }
}
