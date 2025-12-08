# Test SMS API Endpoint

# Configuration
$baseUrl = "http://paden.co.zw"
$token = "YOUR_BEARER_TOKEN_HERE"  # Replace with actual token from login
$userId = 1  # Replace with actual user ID who has a phone number
$propertyAddress = "123 Main Street, Harare"

Write-Host "Testing SMS API Endpoint..." -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Prepare request
$headers = @{
    "Authorization" = "Bearer $token"
    "Content-Type" = "application/json"
    "Accept" = "application/json"
}

$body = @{
    propertyAddress = $propertyAddress
} | ConvertTo-Json

Write-Host "Request Details:" -ForegroundColor Yellow
Write-Host "URL: $baseUrl/api/sms/send/$userId" -ForegroundColor White
Write-Host "Property Address: $propertyAddress" -ForegroundColor White
Write-Host ""

try {
    # Send request
    Write-Host "Sending SMS request..." -ForegroundColor Green
    $response = Invoke-RestMethod -Uri "$baseUrl/api/sms/send/$userId" -Method POST -Headers $headers -Body $body
    
    Write-Host "============================================" -ForegroundColor Green
    Write-Host "SUCCESS!" -ForegroundColor Green
    Write-Host "============================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Response:" -ForegroundColor Yellow
    Write-Host ($response | ConvertTo-Json -Depth 10) -ForegroundColor White
    
} catch {
    Write-Host "============================================" -ForegroundColor Red
    Write-Host "ERROR!" -ForegroundColor Red
    Write-Host "============================================" -ForegroundColor Red
    Write-Host ""
    Write-Host "Status Code: $($_.Exception.Response.StatusCode.value__)" -ForegroundColor Red
    Write-Host "Error Message:" -ForegroundColor Yellow
    
    $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
    $reader.BaseStream.Position = 0
    $reader.DiscardBufferedData()
    $responseBody = $reader.ReadToEnd()
    Write-Host $responseBody -ForegroundColor White
}

Write-Host ""
Write-Host "Test completed." -ForegroundColor Cyan
