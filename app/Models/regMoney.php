<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class regMoney extends Model
{
    protected $fillable = [
     
     
        'user_id',
        'amount',
        'reference_number',
        'phone_number',
       

 ];
}
