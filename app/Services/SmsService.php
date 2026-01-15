<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $apiUrl;
    protected $username;
    protected $password;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.inboxiq.url', 'https://api.inboxiq.co.zw/api/v1/send-sms');
        $this->username = config('services.inboxiq.username');
        $this->password = config('services.inboxiq.password');
        $this->apiKey = config('services.inboxiq.api_key');
    }

    public function sendVerificationCode($phone, $code)
    {
        $message = "Your verification code is: {$code}. This code will expire in 2 minutes.";
        
        return $this->sendSms($phone, $message);
    }

    public function sendSms($destination, $messageText)
    {
        try {
            $authToken = base64_encode("{$this->username}:{$this->password}");

            $response = Http::withHeaders([
                'Authorization' => "Basic {$authToken}",
                'key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'destination' => $destination,
                'messageText' => $messageText,
            ]);

            if ($response->successful()) {
                Log::info('SMS sent successfully', [
                    'destination' => $destination,
                    'response' => $response->json()
                ]);
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            Log::error('SMS sending failed', [
                'destination' => $destination,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to send SMS',
                'details' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('SMS sending exception', [
                'destination' => $destination,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
