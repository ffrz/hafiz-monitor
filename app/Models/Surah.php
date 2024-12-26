<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Surah extends Model
{
    protected $table = 'surahs';

    public function ayahs()
    {
        return $this->hasMany(Ayah::class);
    }
}
