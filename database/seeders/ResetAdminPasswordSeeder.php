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
        $defaultPassword = env('ADMIN_RESET_PASSWORD', 'Admin@123456');

        $admins = User::where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            $this->command->warn('No admin users found.');
            return;
        }

        foreach ($admins as $admin) {
            $admin->password = Hash::make($defaultPassword);
            $admin->save();

            $this->command->info("Password reset for admin: {$admin->email}");
        }

        $this->command->newLine();
        $this->command->warn('⚠️  Default password used: ' . $defaultPassword);
        $this->command->warn('⚠️  Please change this password immediately after logging in!');
    }
}
