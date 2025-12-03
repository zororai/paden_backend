# Password Reset API Testing Script for PowerShell
# Tests the password reset endpoints for mazaruracarlos@gmail.com

Write-Host "=== Password Reset API Testing ===" -ForegroundColor Cyan
Write-Host ""

$email = "mazaruracarlos@gmail.com"
$baseUrl = "http://localhost:8000/api"

# Test 1: Send password reset email
Write-Host "Test 1: Requesting password reset link..." -ForegroundColor Yellow
Write-Host ""

$body = @{
    email = $email
} | ConvertTo-Json

try {
    $response = Invoke-RestMethod -Uri "$baseUrl/password/email" `
        -Method Post `
        -ContentType "application/json" `
        -Body $body
    
    Write-Host "✅ SUCCESS!" -ForegroundColor Green
    Write-Host "Response: $($response.message)" -ForegroundColor Green
    Write-Host ""
} catch {
    Write-Host "❌ FAILED!" -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
}

# Instructions for Step 2
Write-Host "=== Next Steps ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Check your email at: $email" -ForegroundColor White
Write-Host "   (If using Mailpit, visit: http://localhost:8025)" -ForegroundColor White
Write-Host ""
Write-Host "2. Copy the reset token from the email" -ForegroundColor White
Write-Host ""
Write-Host "3. Run the following command to test password reset:" -ForegroundColor White
Write-Host ""
Write-Host "   `$token = 'YOUR_TOKEN_FROM_EMAIL'" -ForegroundColor Cyan
Write-Host "   `$resetBody = @{" -ForegroundColor Cyan
Write-Host "       email = '$email'" -ForegroundColor Cyan
Write-Host "       token = `$token" -ForegroundColor Cyan
Write-Host "       password = 'newpassword123'" -ForegroundColor Cyan
Write-Host "       password_confirmation = 'newpassword123'" -ForegroundColor Cyan
Write-Host "   } | ConvertTo-Json" -ForegroundColor Cyan
Write-Host ""
Write-Host "   Invoke-RestMethod -Uri '$baseUrl/password/reset' ``" -ForegroundColor Cyan
Write-Host "       -Method Post ``" -ForegroundColor Cyan
Write-Host "       -ContentType 'application/json' ``" -ForegroundColor Cyan
Write-Host "       -Body `$resetBody" -ForegroundColor Cyan
Write-Host ""

# Alternative: Check if Mailpit is accessible
Write-Host "=== Checking Mailpit ===" -ForegroundColor Cyan
Write-Host ""

try {
    $mailpitResponse = Invoke-WebRequest -Uri "http://localhost:8025" -TimeoutSec 2 -ErrorAction Stop
    Write-Host "✅ Mailpit is running at http://localhost:8025" -ForegroundColor Green
    Write-Host "   Open this URL in your browser to view the reset email" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Mailpit is not accessible at http://localhost:8025" -ForegroundColor Yellow
    Write-Host "   The email might be configured to use a different mail service" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "=== Testing Complete ===" -ForegroundColor Cyan
