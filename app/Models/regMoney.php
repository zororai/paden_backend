<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class regMoney extends Model
{
    use HasFactory;

    protected $table = 'reg_money';

    protected $fillable = [
        'user_id',
        'amount',
        'reference_number',
        'phone_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
