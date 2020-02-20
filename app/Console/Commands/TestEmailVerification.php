<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailVerificationCode;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;

class TestEmailVerification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-verification {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email verification by sending a verification code';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing email verification for: {$email}");
        
        try {
            // Create or get a test user
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $this->warn("User not found. Creating a temporary test user...");
                $user = User::create([
                    'name' => 'Test User',
                    'email' => $email,
                    'password' => bcrypt('password123'),
                    'role' => 'user'
                ]);
            }
            
            // Generate verification code
            $verificationCode = EmailVerificationCode::createForEmail($email);
            
            $this->info("Generated verification code: {$verificationCode->code}");
            
            // Send notification
            $user->notify(new EmailVerificationNotification($verificationCode->code));
            
            $this->info("✓ Verification email sent successfully to {$email}");
            $this->info("Verification code: {$verificationCode->code}");
            $this->info("Code expires in 1 minute");
            $this->info("Please check the inbox (and spam folder)");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("✗ Failed to send verification email: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
