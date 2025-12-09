<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::create([
    'name' => 'Admin',
    'surname' => 'User',
    'email' => 'admin@paden.co.zw',
    'password' => bcrypt('Admin@2025'),
    'university' => 'Admin',
    'type' => 'admin',
    'role' => 'admin',
    'image' => 'default.png',
    'phone' => '+263771234567',
    'email_verified_at' => now()
]);

echo "Admin user created successfully!\n";
echo "Email: " . $user->email . "\n";
echo "Password: Admin@2025\n";
echo "Login at: http://paden.co.zw/admin/login\n";
