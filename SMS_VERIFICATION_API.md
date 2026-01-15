# SMS Verification Login API Documentation

Complete API documentation for SMS-based authentication for both Student and General Housing contexts.

---

## Overview

The SMS verification system replaces email verification with a secure 6-digit code sent via SMS. The code expires in 2 minutes and provides enhanced security for login.

**Base URL:** `http://your-domain/api`

**SMS Provider:** InboxIQ (https://api.inboxiq.co.zw)

---

## Authentication Flow

### Step 1: Request Verification Code

**Endpoint:** `POST /api/login/request-code`

**Authentication:** None (Public)

**Rate Limit:** 5 requests per minute

**Description:** Validates credentials and sends a 6-digit verification code to the user's phone number.

**Request Body:**
```json
{
  "phone": "+263771234567",
  "password": "password123"
}
```

**Fields:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| phone | string | Yes | User's phone number (must be registered) |
| password | string | Yes | User's password (minimum 6 characters) |

**Success Response (200):**
```json
{
  "status": true,
  "message": "Verification code sent to your phone",
  "expires_in": 120
}
```

**Error Responses:**

**401 - Invalid Credentials:**
```json
{
  "status": false,
  "message": "Invalid credentials"
}
```

**422 - Validation Error:**
```json
{
  "status": false,
  "errors": {
    "phone": ["The phone field is required."],
    "password": ["The password must be at least 6 characters."]
  }
}
```

**500 - SMS Sending Failed:**
```json
{
  "status": false,
  "message": "Failed to send verification code. Please try again."
}
```

---

### Step 2: Verify Code and Complete Login

**Endpoint:** `POST /api/login/verify-code`

**Authentication:** None (Public)

**Rate Limit:** 10 requests per minute

**Description:** Validates the 6-digit code and completes the login process, returning an authentication token.

**Request Body:**
```json
{
  "phone": "+263771234567",
  "code": "123456",
  "device_id": "unique-device-identifier",
  "device_name": "iPhone 13 Pro",
  "platform": "ios"
}
```

**Fields:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| phone | string | Yes | User's phone number |
| code | string | Yes | 6-digit verification code |
| device_id | string | Yes | Unique device identifier |
| device_name | string | No | Human-readable device name |
| platform | string | Yes | Device platform: `android`, `ios`, or `web` |

**Success Response (200):**
```json
{
  "status": true,
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "phone": "+263771234567",
    "email": "john@example.com",
    "role": "tenant",
    "housing_context": "general",
    "profile_complete": true,
    "image": "http://your-domain/storage/users/avatar.jpg"
  },
  "token": "1|XyZabc123...",
  "device": {
    "device_id": "unique-device-identifier",
    "platform": "ios"
  }
}
```

**Error Responses:**

**401 - Invalid Code:**
```json
{
  "status": false,
  "message": "Invalid verification code"
}
```

**401 - Expired Code:**
```json
{
  "status": false,
  "message": "Verification code has expired"
}
```

**404 - User Not Found:**
```json
{
  "status": false,
  "message": "User not found"
}
```

**422 - Validation Error:**
```json
{
  "status": false,
  "errors": {
    "code": ["The code must be 6 characters."],
    "platform": ["The selected platform is invalid."]
  }
}
```

---

### Step 3: Resend Verification Code (Optional)

**Endpoint:** `POST /api/login/resend-code`

**Authentication:** None (Public)

**Rate Limit:** 3 requests per minute

**Description:** Resends a new verification code if the previous one expired or was not received.

**Request Body:**
```json
{
  "phone": "+263771234567"
}
```

**Success Response (200):**
```json
{
  "status": true,
  "message": "Verification code resent to your phone",
  "expires_in": 120
}
```

**Error Responses:**

**404 - Phone Not Found:**
```json
{
  "status": false,
  "message": "Phone number not found"
}
```

**500 - SMS Sending Failed:**
```json
{
  "status": false,
  "message": "Failed to send verification code. Please try again."
}
```

---

## Complete Login Flow Example

### For Student Housing Users

```javascript
// Step 1: Request verification code
const requestCode = async () => {
  const response = await fetch('http://your-domain/api/login/request-code', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      phone: '+263771234567',
      password: 'password123'
    })
  });
  
  const data = await response.json();
  if (data.status) {
    console.log('Code sent! Expires in:', data.expires_in, 'seconds');
    // Show code input screen
  }
};

// Step 2: Verify code
const verifyCode = async (code) => {
  const response = await fetch('http://your-domain/api/login/verify-code', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      phone: '+263771234567',
      code: code,
      device_id: 'device-unique-id-123',
      device_name: 'iPhone 13',
      platform: 'ios'
    })
  });
  
  const data = await response.json();
  if (data.status) {
    // Save token
    localStorage.setItem('auth_token', data.token);
    localStorage.setItem('user', JSON.stringify(data.user));
    
    // Redirect based on housing context
    if (data.user.housing_context === 'student') {
      // Redirect to student dashboard
    } else if (data.user.housing_context === 'general') {
      // Redirect to general housing dashboard
    }
  }
};

// Step 3: Resend code if needed
const resendCode = async () => {
  const response = await fetch('http://your-domain/api/login/resend-code', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      phone: '+263771234567'
    })
  });
  
  const data = await response.json();
  if (data.status) {
    console.log('New code sent!');
  }
};
```

---

### For General Housing Users

The flow is identical for general housing users. The system automatically detects the user's `housing_context` and returns it in the response.

---

## SMS Message Format

When a verification code is requested, the user receives an SMS with the following format:

```
Your verification code is: 123456. This code will expire in 2 minutes.
```

---

## Security Features

1. **Code Expiration:** All codes expire after 2 minutes
2. **One-Time Use:** Codes are marked as verified after successful use
3. **Rate Limiting:** 
   - Request code: 5 attempts per minute
   - Verify code: 10 attempts per minute
   - Resend code: 3 attempts per minute
4. **Old Code Invalidation:** Requesting a new code invalidates all previous unverified codes for that phone number
5. **Credential Validation:** Password is validated before sending SMS to prevent abuse

---

## Testing with cURL

### Request Code
```bash
curl -X POST http://your-domain/api/login/request-code \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+263771234567",
    "password": "password123"
  }'
```

### Verify Code
```bash
curl -X POST http://your-domain/api/login/verify-code \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+263771234567",
    "code": "123456",
    "device_id": "test-device-123",
    "device_name": "Test Device",
    "platform": "web"
  }'
```

### Resend Code
```bash
curl -X POST http://your-domain/api/login/resend-code \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+263771234567"
  }'
```

---

## Environment Configuration

Add the following to your `.env` file:

```env
# InboxIQ SMS Service
INBOXIQ_URL=https://api.inboxiq.co.zw/api/v1/send-sms
INBOXIQ_USERNAME=your_username
INBOXIQ_PASSWORD=your_password
INBOXIQ_API_KEY=your_api_key
```

---

## Database Setup

Run the migration to create the verification codes table:

```bash
php artisan migrate
```

This creates the `verification_codes` table with the following structure:
- `id` - Primary key
- `phone` - User's phone number
- `code` - 6-digit verification code
- `expires_at` - Expiration timestamp
- `verified` - Boolean flag
- `created_at` - Creation timestamp
- `updated_at` - Update timestamp

---

## Error Handling Best Practices

### Frontend Implementation

```javascript
const handleLogin = async (phone, password) => {
  try {
    // Step 1: Request code
    const codeResponse = await fetch('/api/login/request-code', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ phone, password })
    });
    
    const codeData = await codeResponse.json();
    
    if (!codeData.status) {
      if (codeResponse.status === 401) {
        alert('Invalid phone number or password');
      } else if (codeResponse.status === 500) {
        alert('Failed to send SMS. Please try again.');
      }
      return;
    }
    
    // Show code input with countdown timer
    showCodeInput(codeData.expires_in);
    
  } catch (error) {
    console.error('Login error:', error);
    alert('Network error. Please check your connection.');
  }
};

const handleVerifyCode = async (phone, code, deviceInfo) => {
  try {
    const response = await fetch('/api/login/verify-code', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        phone,
        code,
        ...deviceInfo
      })
    });
    
    const data = await response.json();
    
    if (!data.status) {
      if (response.status === 401) {
        if (data.message.includes('expired')) {
          alert('Code expired. Please request a new one.');
        } else {
          alert('Invalid code. Please try again.');
        }
      }
      return;
    }
    
    // Success - save token and redirect
    localStorage.setItem('auth_token', data.token);
    window.location.href = '/dashboard';
    
  } catch (error) {
    console.error('Verification error:', error);
    alert('Network error. Please check your connection.');
  }
};
```

---

## Differences from Email Verification

| Feature | Email Verification (Old) | SMS Verification (New) |
|---------|-------------------------|------------------------|
| Delivery Method | Email | SMS |
| Code Length | Variable | 6 digits |
| Expiration | Varies | 2 minutes |
| Verification Required | After registration | On every login |
| Rate Limiting | Minimal | Strict (5/3 per minute) |
| Cost | Free | Per SMS charge |

---

## Support

For InboxIQ API issues:
- **API Documentation:** Contact InboxIQ support
- **API URL:** https://api.inboxiq.co.zw/api/v1/send-sms
- **Authentication:** Basic Auth + API Key

For application issues:
- Check logs in `storage/logs/laravel.log`
- SMS sending logs are automatically recorded

---

**Last Updated:** January 15, 2026  
**Version:** 1.0.0
