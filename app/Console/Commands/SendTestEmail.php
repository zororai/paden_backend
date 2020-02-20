<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {recipient}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify mail configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $recipient = $this->argument('recipient');
        
        $this->info("Sending test email to: {$recipient}");
        
        try {
            Mail::raw('This is a test email from Paden. Your mail configuration is working correctly!', function ($message) use ($recipient) {
                $message->to($recipient)
                    ->subject('Test Email from Paden - Mail Configuration Test');
            });
            
            $this->info("✓ Email sent successfully to {$recipient}");
            $this->info("Please check the inbox (and spam folder) at {$recipient}");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("✗ Failed to send email: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
