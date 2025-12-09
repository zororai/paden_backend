<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

DB::table('users')
    ->where('email', 'admin@paden.co.zw')
    ->update(['email_verified_at' => now()]);

echo "Admin email verified successfully!\n";
