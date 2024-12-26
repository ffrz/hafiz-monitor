<?php

namespace App\Models;

class HafizMemorizedJuz extends Model
{
    protected $table = 'hafiz_memorized_juzs';

    public $timestamps = false;

    protected $fillable = [
        'hafiz_id',
        'juz',
    ];

    public function hafiz()
    {
        return $this->belongsTo(Hafiz::class);
    }
}
