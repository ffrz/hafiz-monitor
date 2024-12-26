<?php

namespace App\Models;

class HafizMemorizedSurah extends Model
{
    protected $table = 'hafiz_memorized_surahs';

    public $timestamps = false;

    protected $fillable = [
        'hafiz_id',
        'surah_id',
    ];

    public function hafiz()
    {
        return $this->belongsTo(Hafiz::class);
    }

    public function surah()
    {
        return $this->belongsTo(Surah::class);
    }
}
