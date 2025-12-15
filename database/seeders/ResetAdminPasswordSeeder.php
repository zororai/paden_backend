<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ResetAdminPasswordSeeder extends Seeder
{
    /**
     * Reset admin passwords with proper Bcrypt hash.
     * 
     * Usage: php artisan db:seed --class=ResetAdminPasswordSeeder
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@paden.co.zw');
        $defaultPassword = env('ADMIN_RESET_PASSWORD', 'Admin@123456');

        // Check if user exists with this email
        $user = User::where('email', $adminEmail)->first();

        if ($user) {
            // User exists - update to admin role and reset password
            $user->role = 'admin';
            $user->password = Hash::make($defaultPassword);
            $user->email_verified_at = $user->email_verified_at ?? now();
            $user->save();

            $this->command->info("Updated existing user to admin: {$user->email}");
        } else {
            // Create new admin user
            $user = User::create([
                'name' => 'Admin',
                'surname' => 'User',
                'email' => $adminEmail,
                'password' => Hash::make($defaultPassword),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);

            $this->command->info("Created new admin user: {$user->email}");
        }

        $this->command->newLine();
        $this->command->warn('⚠️  Email: ' . $adminEmail);
        $this->command->warn('⚠️  Password: ' . $defaultPassword);
        $this->command->warn('⚠️  Please change this password immediately after logging in!');
    }
}
