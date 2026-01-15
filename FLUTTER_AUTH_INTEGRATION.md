# Flutter Authentication Integration Guide

## Overview

This document provides Flutter integration instructions for the device-based persistent authentication system. The backend uses Laravel Sanctum with device tracking to enable auto-login functionality.

---

## Backend Endpoints

### Authentication Endpoints

| Endpoint | Method | Auth Required | Description |
|----------|--------|---------------|-------------|
| `/api/login` | POST | No | Login with device info |
| `/api/me` | GET | Yes | Auto-login check, returns user data |
| `/api/logout` | POST | Yes | Logout current device |
| `/api/logout-all` | POST | Yes | Logout from all devices |
| `/api/devices` | GET | Yes | List all logged-in devices |
| `/api/devices/{deviceId}` | DELETE | Yes | Logout specific device |

---

## Endpoint Details

### 1. Login - `POST /api/login`

**Request Body (UPDATED):**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "device_id": "550e8400-e29b-41d4-a716-446655440000",
  "device_name": "Samsung Galaxy S24",
  "platform": "android"
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| email | string | Yes (or phone) | User email |
| phone | string | Yes (or email) | User phone |
| password | string | Yes | User password (min 6 chars) |
| device_id | string | **Yes (NEW)** | Permanent UUID from device |
| device_name | string | No | Human-readable device name |
| platform | string | **Yes (NEW)** | `android`, `ios`, or `web` |

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "role": "Student",
    "housing_context": "student",
    "profile_complete": false,
    "image": "https://yourdomain.com/storage/profile/image.jpg",
    "email": "user@example.com"
  },
  "token": "1|abc123xyz...",
  "device": {
    "device_id": "550e8400-e29b-41d4-a716-446655440000",
    "platform": "android"
  }
}
```

**Response (401 Unauthorized):**
```json
{
  "status": false,
  "message": "Invalid credentials"
}
```

---

### 2. Auto-Login Check - `GET /api/me`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "status": true,
  "user": {
    "id": 1,
    "name": "John Doe",
    "surname": "Doe",
    "email": "user@example.com",
    "phone": "+263771234567",
    "role": "Student",
    "housing_context": "student",
    "profile_complete": false,
    "image": "https://yourdomain.com/storage/profile/image.jpg"
  },
  "device": {
    "device_id": "550e8400-e29b-41d4-a716-446655440000",
    "device_name": "Samsung Galaxy S24",
    "platform": "android",
    "last_seen_at": "2026-01-15T06:30:00.000000Z"
  }
}
```

**Response (401 Unauthorized):**
```json
{
  "message": "Unauthenticated."
}
```

---

### 3. Logout (Current Device) - `POST /api/logout`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Logged out successfully"
}
```

---

### 4. Logout All Devices - `POST /api/logout-all`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Logged out from all devices successfully"
}
```

---

### 5. List Devices - `GET /api/devices`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "status": true,
  "devices": [
    {
      "device_id": "550e8400-e29b-41d4-a716-446655440000",
      "device_name": "Samsung Galaxy S24",
      "platform": "android",
      "last_seen_at": "2026-01-15T06:30:00.000000Z",
      "is_current": true
    },
    {
      "device_id": "660e8400-e29b-41d4-a716-446655440001",
      "device_name": "iPhone 15 Pro",
      "platform": "ios",
      "last_seen_at": "2026-01-14T10:00:00.000000Z",
      "is_current": false
    }
  ]
}
```

---

### 6. Logout Specific Device - `DELETE /api/devices/{deviceId}`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "status": true,
  "message": "Device logged out successfully"
}
```

**Response (404 Not Found):**
```json
{
  "status": false,
  "message": "Device not found"
}
```

---

## Flutter Implementation

### Required Packages

Add to `pubspec.yaml`:
```yaml
dependencies:
  uuid: ^4.0.0
  flutter_secure_storage: ^9.0.0
  http: ^1.1.0
  device_info_plus: ^10.0.0  # Optional: for device name
```

Run:
```bash
flutter pub get
```

---

### Auth Service Implementation

Create `lib/services/auth_service.dart`:

```dart
import 'dart:convert';
import 'dart:io';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:http/http.dart' as http;
import 'package:uuid/uuid.dart';

class AuthService {
  static const String baseUrl = 'https://your-api-domain.com';
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  // ========== DEVICE ID MANAGEMENT ==========
  
  /// Get or create a permanent device ID
  Future<String> getDeviceId() async {
    String? deviceId = await _storage.read(key: 'device_id');
    if (deviceId == null) {
      deviceId = const Uuid().v4();
      await _storage.write(key: 'device_id', value: deviceId);
    }
    return deviceId;
  }

  /// Get platform string
  String getPlatform() {
    if (Platform.isAndroid) return 'android';
    if (Platform.isIOS) return 'ios';
    return 'web';
  }

  // ========== TOKEN MANAGEMENT ==========

  /// Save auth token
  Future<void> saveToken(String token) async {
    await _storage.write(key: 'auth_token', value: token);
  }

  /// Get stored token
  Future<String?> getToken() async {
    return await _storage.read(key: 'auth_token');
  }

  /// Clear token
  Future<void> clearToken() async {
    await _storage.delete(key: 'auth_token');
  }

  // ========== AUTH ENDPOINTS ==========

  /// Login with email/phone and password
  Future<Map<String, dynamic>> login({
    String? email,
    String? phone,
    required String password,
    String? deviceName,
  }) async {
    final deviceId = await getDeviceId();

    final body = {
      'password': password,
      'device_id': deviceId,
      'device_name': deviceName ?? 'Flutter App',
      'platform': getPlatform(),
    };

    if (email != null) {
      body['email'] = email;
    } else if (phone != null) {
      body['phone'] = phone;
    }

    final response = await http.post(
      Uri.parse('$baseUrl/api/login'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: jsonEncode(body),
    );

    final data = jsonDecode(response.body);

    if (response.statusCode == 200 && data['status'] == true) {
      await saveToken(data['token']);
    }

    return data;
  }

  /// Check if user is logged in (auto-login)
  Future<Map<String, dynamic>?> checkAuth() async {
    final token = await getToken();
    if (token == null) return null;

    final response = await http.get(
      Uri.parse('$baseUrl/api/me'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      // Token invalid, clear it
      await clearToken();
      return null;
    }
  }

  /// Logout from current device
  Future<bool> logout() async {
    final token = await getToken();
    if (token == null) return false;

    final response = await http.post(
      Uri.parse('$baseUrl/api/logout'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );

    await clearToken();
    return response.statusCode == 200;
  }

  /// Logout from all devices
  Future<bool> logoutAll() async {
    final token = await getToken();
    if (token == null) return false;

    final response = await http.post(
      Uri.parse('$baseUrl/api/logout-all'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );

    await clearToken();
    return response.statusCode == 200;
  }

  /// Get list of all devices
  Future<List<dynamic>?> getDevices() async {
    final token = await getToken();
    if (token == null) return null;

    final response = await http.get(
      Uri.parse('$baseUrl/api/devices'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['devices'];
    }
    return null;
  }

  /// Logout from specific device
  Future<bool> logoutDevice(String deviceId) async {
    final token = await getToken();
    if (token == null) return false;

    final response = await http.delete(
      Uri.parse('$baseUrl/api/devices/$deviceId'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );

    return response.statusCode == 200;
  }
}
```

---

### App Startup Flow

In `lib/main.dart` or your splash screen:

```dart
import 'package:flutter/material.dart';
import 'services/auth_service.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  final AuthService _authService = AuthService();

  @override
  void initState() {
    super.initState();
    _checkAuth();
  }

  Future<void> _checkAuth() async {
    final userData = await _authService.checkAuth();

    if (userData != null) {
      // User is logged in - navigate to home
      Navigator.pushReplacementNamed(context, '/home');
    } else {
      // Not logged in - navigate to login
      Navigator.pushReplacementNamed(context, '/login');
    }
  }

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      body: Center(
        child: CircularProgressIndicator(),
      ),
    );
  }
}
```

---

### Login Screen Example

```dart
class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final AuthService _authService = AuthService();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isLoading = false;

  Future<void> _login() async {
    setState(() => _isLoading = true);

    final result = await _authService.login(
      email: _emailController.text,
      password: _passwordController.text,
      deviceName: 'My Flutter App',
    );

    setState(() => _isLoading = false);

    if (result['status'] == true) {
      Navigator.pushReplacementNamed(context, '/home');
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'] ?? 'Login failed')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            TextField(
              controller: _emailController,
              decoration: const InputDecoration(labelText: 'Email'),
            ),
            const SizedBox(height: 16),
            TextField(
              controller: _passwordController,
              obscureText: true,
              decoration: const InputDecoration(labelText: 'Password'),
            ),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: _isLoading ? null : _login,
              child: _isLoading
                  ? const CircularProgressIndicator()
                  : const Text('Login'),
            ),
          ],
        ),
      ),
    );
  }
}
```

---

## Authentication Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        APP STARTUP                               │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌──────────────────┐
                    │  Read stored     │
                    │  auth token      │
                    └──────────────────┘
                              │
              ┌───────────────┴───────────────┐
              │                               │
              ▼                               ▼
       Token exists?                    No token
              │                               │
              ▼                               ▼
     ┌──────────────────┐           ┌──────────────────┐
     │  Call GET /me    │           │  Show Login      │
     │  with Bearer     │           │  Screen          │
     │  token           │           └──────────────────┘
     └──────────────────┘
              │
      ┌───────┴───────┐
      │               │
      ▼               ▼
   200 OK          401 Error
      │               │
      ▼               ▼
┌──────────┐   ┌──────────────┐
│  Auto    │   │  Clear token │
│  Login   │   │  Show Login  │
│  Success │   │  Screen      │
└──────────┘   └──────────────┘
```

---

## Security Notes

- **Device ID** is generated once and stored permanently
- **Token** is stored in secure storage (encrypted)
- **One token per device** - previous tokens are revoked on new login
- **Rate limiting** - Login endpoint limited to 10 requests/minute
- **No cookies** - Pure token-based authentication

---

## Summary

| What Changed | Details |
|--------------|---------|
| Login endpoint | Now requires `device_id` and `platform` |
| New endpoint | `GET /api/me` for auto-login check |
| New endpoint | `POST /api/logout` for current device |
| New endpoint | `POST /api/logout-all` for all devices |
| New endpoint | `GET /api/devices` to list devices |
| New endpoint | `DELETE /api/devices/{id}` to logout device |

---

*Document Version: 1.0*  
*Last Updated: January 2026*
