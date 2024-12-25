<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hafiz extends Model
{
    public $table = 'huffaz';
    use HasFactory;

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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
