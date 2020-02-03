<?php

// database/seeders/UniversitySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\University;

class UniversitySeeder extends Seeder
{
    public function run()
    {
        $universities = [
    ['university' => 'Bindura University of Science Education (BUSE)', 'latitude' => -17.3017, 'longitude' => 31.3304],
    ['university' => 'Catholic University in Zimbabwe (CUZ)', 'latitude' => -17.8289, 'longitude' => 31.0522],
    ['university' => 'Chinhoyi University of Technology (CUT)', 'latitude' => -17.3596, 'longitude' => 30.1816],
    ['university' => 'Great Zimbabwe University (GZU)', 'latitude' => -20.0765, 'longitude' => 30.8293],
    ['university' => 'Gwanda State University (GSU)', 'latitude' => -21.0088, 'longitude' => 29.0026],
    ['university' => 'Harare Institute of Technology (HIT)', 'latitude' => -17.7868, 'longitude' => 31.0530],
    ['university' => 'Lupane State University (LSU)', 'latitude' => -18.9322, 'longitude' => 27.8153],
    ['university' => 'Manicaland State University of Applied Sciences (MSUAS)', 'latitude' => -18.8995, 'longitude' => 32.6076],
    ['university' => 'Marondera University of Agricultural Science & Technology (MUAST)', 'latitude' => -18.1867, 'longitude' => 31.5512],
    ['university' => 'Midlands State University (MSU)', 'latitude' => -19.4560, 'longitude' => 29.8156],
    ['university' => 'National University of Science and Technology, Zimbabwe (NUST)', 'latitude' => -20.1416, 'longitude' => 28.5828],
    ['university' => 'Reformed Church University', 'latitude' => -20.0718, 'longitude' => 30.8322],
    ['university' => 'Solusi University', 'latitude' => -20.2842, 'longitude' => 28.3352],
    ['university' => 'Sourthen Africa Methodist University (SAMU)', 'latitude' => -18.9999, 'longitude' => 29.0000], // approximate
    ['university' => 'University of Zimbabwe (UZ)', 'latitude' => -17.7846, 'longitude' => 31.0533],
    ['university' => 'Women\'s University in Africa (WUA)', 'latitude' => -17.9266, 'longitude' => 31.1156],
    ['university' => 'Zimbabwe Ezekiel Guti University (ZEGU)', 'latitude' => -17.3349, 'longitude' => 31.3253],
    ['university' => 'Africa University (AU)', 'latitude' => -18.9447, 'longitude' => 32.6722],
];



        foreach ($universities as $university) {
            University::create($university);
        }
    }
}
