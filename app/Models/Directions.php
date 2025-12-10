<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Directions extends Model
{
    use HasFactory;

    protected $table = 'directions';

    protected $fillable = [
        'user_id',
        'properties_id',
        'amount',
        'reference_number',
        'phone_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Properties::class, 'properties_id');
    }
}
