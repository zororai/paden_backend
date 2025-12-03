<?php

/**
 * Password Reset Testing Script
 * 
 * This script tests the password reset functionality for the email: mazaruracarlos@gmail.com
 * 
 * Steps:
 * 1. Check if user exists in database
 * 2. Test sending password reset email
 * 3. Retrieve the reset token from database
 * 4. Test resetting password with the token
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

echo "=== Password Reset Testing Script ===\n\n";

$testEmail = 'mazaruracarlos@gmail.com';

// Step 1: Check if user exists
echo "Step 1: Checking if user exists...\n";
$user = User::where('email', $testEmail)->first();

if (!$user) {
    echo "❌ User with email '{$testEmail}' does not exist.\n";
    echo "Creating test user...\n";
    
    $user = User::create([
        'name' => 'Carlos',
        'surname' => 'Mazarura',
        'email' => $testEmail,
        'university' => 'Test University',
        'type' => 'student',
        'image' => 'default.jpg',
        'phone' => '+1234567890',
        'role' => 'user',
        'password' => Hash::make('oldpassword123'),
        'email_verified_at' => now(),
    ]);
    
    echo "✅ Test user created successfully!\n";
} else {
    echo "✅ User exists: {$user->name} ({$user->email})\n";
}

echo "\n";

// Step 2: Send password reset link
echo "Step 2: Sending password reset link...\n";
$response = Password::sendResetLink(['email' => $testEmail]);

if ($response == Password::RESET_LINK_SENT) {
    echo "✅ Password reset link sent successfully!\n";
} else {
    echo "❌ Failed to send password reset link.\n";
    echo "Response: {$response}\n";
}

echo "\n";

// Step 3: Retrieve the reset token from database
echo "Step 3: Retrieving reset token from database...\n";
$tokenRecord = DB::table('password_reset_tokens')
    ->where('email', $testEmail)
    ->first();

if ($tokenRecord) {
    echo "✅ Reset token found in database!\n";
    echo "Email: {$tokenRecord->email}\n";
    echo "Token (hashed): " . substr($tokenRecord->token, 0, 20) . "...\n";
    echo "Created at: {$tokenRecord->created_at}\n";
    
    // Note: The actual token is hashed in the database
    // For testing, we'll need to use the plain token that was sent via email
    echo "\n⚠️  Note: The token in the database is hashed.\n";
    echo "To test the reset endpoint, you need the plain token from the email.\n";
} else {
    echo "❌ No reset token found in database.\n";
}

echo "\n";

// Step 4: Instructions for manual testing
echo "Step 4: Manual Testing Instructions\n";
echo "=====================================\n\n";

echo "To complete the password reset test:\n\n";

echo "1. Check your email inbox for: {$testEmail}\n";
echo "   (If using Mailpit, visit: http://localhost:8025)\n\n";

echo "2. Copy the reset token from the email\n\n";

echo "3. Test the password reset endpoint using curl or Postman:\n\n";

echo "   POST http://localhost:8000/api/password/reset\n";
echo "   Content-Type: application/json\n\n";

echo "   Body:\n";
echo "   {\n";
echo "       \"email\": \"{$testEmail}\",\n";
echo "       \"token\": \"YOUR_TOKEN_FROM_EMAIL\",\n";
echo "       \"password\": \"newpassword123\",\n";
echo "       \"password_confirmation\": \"newpassword123\"\n";
echo "   }\n\n";

echo "4. Expected response:\n";
echo "   {\n";
echo "       \"message\": \"Password reset successful.\"\n";
echo "   }\n\n";

// Generate curl commands for easy testing
echo "=== Quick Test Commands ===\n\n";

echo "# Step 1: Request password reset\n";
echo "curl -X POST http://localhost:8000/api/password/email \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -d '{\"email\": \"{$testEmail}\"}'\n\n";

echo "# Step 2: Reset password (replace YOUR_TOKEN with actual token from email)\n";
echo "curl -X POST http://localhost:8000/api/password/reset \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -d '{\n";
echo "    \"email\": \"{$testEmail}\",\n";
echo "    \"token\": \"YOUR_TOKEN\",\n";
echo "    \"password\": \"newpassword123\",\n";
echo "    \"password_confirmation\": \"newpassword123\"\n";
echo "  }'\n\n";

echo "=== Testing Complete ===\n";
