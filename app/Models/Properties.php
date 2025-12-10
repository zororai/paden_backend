<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Properties extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
'title',
'pcontent',
'type',
'bhk',
'stype',
'bedroom',
'bathroom',
'balcony',
'kitchen',
'hall',
'floor',
'size',
'price',
'location',
'city',
'state',
'feature',
'pimage',
'pimage1',
'pimage2',
'pimage3',
'pimage4',
'uid',
'status',
'mapimage',
'topmapimage',
'groundmapimage',
'totalfloor',
'date',
'isFeatured',
'count',
'likes',


    ];



   // public function likes()
   // {
   //  return $this->belongsToMany(User::class,'like')->withTimestamps();
//}
public $timestamps = true;

public function user()
{
    return $this->belongsTo(User::class, 'uid'); // 'uid' is your custom foreign key
}


public function likes()
{
    return $this->hasMany(Like::class);
}

}
