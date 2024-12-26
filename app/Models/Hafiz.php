<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hafiz extends Model
{
    use HasFactory;

    protected $table = 'hafizes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'birth_date',
        'gender',
        'phone',
        'address',
        'notes',
        'active',
        'surahs',
        'juzs',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function memorizedJuzs()
    {
        return $this->hasMany(HafizMemorizedJuz::class);
    }

    public function memorizedSurahs()
    {
        return $this->hasMany(HafizMemorizedJuz::class);
    }
}
