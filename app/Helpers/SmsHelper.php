<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class SmsHelper
{
    /**
     * Send SMS via InboxIQ API
     *
     * @param string $destination Phone number with country code (e.g., +263771234567)
     * @param string $messageText SMS message content
     * @param int $maxRetries Number of retry attempts (default: 3)
     * @return array
     */
    public static function sendSms($destination, $messageText, $maxRetries = 3)
    {
        // Validate phone number format
        if (!preg_match('/^\+\d{10,15}$/', $destination)) {
            Log::warning('SMS invalid phone format', ['destination' => $destination]);
            return [
                'success' => false,
                'message' => 'Invalid phone number format. Must be international format (e.g., +263771234567)'
            ];
        }

        $apiUrl = config('services.inboxiq.url', 'https://api.inboxiq.co.zw/api/v1/send-sms');
        $username = config('services.inboxiq.username');
        $password = config('services.inboxiq.password');
        $apiKey = config('services.inboxiq.api_key');

        // Check credentials
        if (!$username || !$password || !$apiKey) {
            Log::error('InboxIQ credentials missing', [
                'has_username' => !empty($username),
                'has_password' => !empty($password),
                'has_api_key' => !empty($apiKey)
            ]);
            return [
                'success' => false,
                'message' => 'SMS configuration error - credentials missing'
            ];
        }

        // Create Basic Auth token
        $authToken = base64_encode("{$username}:{$password}");

        $data = [
            'destination' => $destination,
            'messageText' => $messageText
        ];

        $httpCode = 0;
        $response = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $ch = curl_init($apiUrl);

            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Basic ' . $authToken,
                    'key: ' . $apiKey,
                    'Content-Type: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            $curlErrno = curl_errno($ch);

            curl_close($ch);

            // Handle cURL errors
            if ($curlErrno) {
                Log::error('SMS cURL error', [
                    'attempt' => $attempt,
                    'error' => $curlError,
                    'errno' => $curlErrno,
                    'destination' => $destination
                ]);

                if ($attempt < $maxRetries) {
                    sleep(1);
                    continue;
                }

                return [
                    'success' => false,
                    'message' => 'SMS sending failed - connection error',
                    'error' => $curlError
                ];
            }

            // Success (2xx response)
            if ($httpCode >= 200 && $httpCode < 300) {
                Log::info('SMS sent successfully', [
                    'destination' => $destination,
                    'http_code' => $httpCode,
                    'response' => $response
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'http_code' => $httpCode,
                    'response' => json_decode($response, true)
                ];
            }

            // Server error - retry
            if ($httpCode >= 500 && $attempt < $maxRetries) {
                Log::warning('SMS server error - retrying', [
                    'attempt' => $attempt,
                    'http_code' => $httpCode,
                    'destination' => $destination
                ]);
                sleep(1);
                continue;
            }

            // Client error or final attempt - don't retry
            break;
        }

        // Error response mapping
        $errorMessages = [
            400 => 'Bad Request - Invalid request parameters',
            401 => 'Unauthorized - Invalid authentication credentials',
            403 => 'Forbidden - Insufficient permissions',
            404 => 'Not Found - API endpoint not found',
            429 => 'Too Many Requests - Rate limit exceeded',
            500 => 'Internal Server Error - Try again later',
        ];

        $errorMessage = $errorMessages[$httpCode] ?? "HTTP Error {$httpCode}";

        Log::error('SMS sending failed', [
            'destination' => $destination,
            'http_code' => $httpCode,
            'error_message' => $errorMessage,
            'response' => $response
        ]);

        return [
            'success' => false,
            'message' => $errorMessage,
            'http_code' => $httpCode,
            'response' => json_decode($response, true)
        ];
    }

    /**
     * Send verification code SMS
     *
     * @param string $destination Phone number
     * @param string $code 6-digit verification code
     * @return array
     */
    public static function sendVerificationCode($destination, $code)
    {
        $message = "Your Paden verification code is: {$code}. This code will expire in 2 minutes.";
        return self::sendSms($destination, $message);
    }

    /**
     * Format phone number to international format
     *
     * @param string $phone Raw phone number
     * @param string $countryCode Default country code (default: +263 for Zimbabwe)
     * @return string Formatted phone number
     */
    public static function formatPhoneNumber($phone, $countryCode = '+263')
    {
        // Remove all non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // If starts with 0, replace with country code
        if (substr($phone, 0, 1) === '0') {
            $phone = $countryCode . substr($phone, 1);
        }

        // If doesn't start with +, add it
        if (substr($phone, 0, 1) !== '+') {
            $phone = '+' . $phone;
        }

        return $phone;
    }
}
