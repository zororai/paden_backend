# General Housing API Documentation

Complete API documentation for the General Housing module integration.

---

## Table of Contents

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [User Registration & Login](#user-registration--login)
4. [Tenant Endpoints](#tenant-endpoints)
5. [Landlord Endpoints](#landlord-endpoints)
6. [Admin Endpoints](#admin-endpoints)
7. [Error Handling](#error-handling)
8. [Complete Integration Flow](#complete-integration-flow)

---

## Overview

**Base URL:** `http://your-domain/api`

**Authentication:** Bearer Token (Sanctum)

**Content-Type:** `application/json`

### User Roles
- **Tenant** - Browse properties, send enquiries, receive notifications
- **Landlord** - Manage profile, create/update/delete properties
- **Admin** - Manage all properties and users

---

## Authentication

All authenticated endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer {token}
```

The token is returned upon successful registration or login.

---

## User Registration & Login

### 1. Register New User

**Endpoint:** `POST /register/general`

**Authentication:** None (Public)

**Request Body:**
```json
{
  "name": "John Doe",
  "phone": "+263771234567",
  "surname": "Doe",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "tenant"
}
```

**Fields:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | Yes | User's full name |
| surname | string | No | User's surname |
| phone | string | Yes* | Phone number (unique) |
| email | string | No | Email address (unique) |
| password | string | Yes | Minimum 6 characters |
| password_confirmation | string | Yes | Must match password |
| role | string | Yes | Either "tenant" or "landlord" |

*Either phone or email must be provided

**Success Response (201):**
```json
{
  "status": true,
  "message": "Registration successful",
  "token": "1|abcdefghijklmnopqrstuvwxyz...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": null,
    "phone": "+263771234567",
    "role": "tenant",
    "housing_context": "general",
    "profile_complete": false
  }
}
```

**Error Response (422):**
```json
{
  "status": false,
  "errors": {
    "phone": ["This phone number is already registered."],
    "password": ["Passwords do not match."]
  }
}
```

---

### 2. Login

**Endpoint:** `POST /login`

**Authentication:** None (Public)

**Request Body (Phone):**
```json
{
  "phone": "+263771234567",
  "password": "password123"
}
```

**Request Body (Email):**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Success Response (200):**
```json
{
  "status": true,
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "phone": "+263771234567",
    "role": "tenant",
    "housing_context": "general",
    "profile_complete": false,
    "image": null
  },
  "token": "1|XyZabc123..."
}
```

**Error Response (401):**
```json
{
  "status": false,
  "message": "Invalid credentials"
}
```

---

## Tenant Endpoints

All tenant endpoints require:
- **Authentication:** Bearer Token
- **Role:** tenant
- **Housing Context:** general

### 1. Get All Properties

**Endpoint:** `GET /general/properties`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| location | string | No | Filter by location |
| city | string | No | Filter by city |
| min_price | number | No | Minimum price |
| max_price | number | No | Maximum price |
| property_type | string | No | room, cottage, flat, house |

**Example Request:**
```
GET /general/properties?city=Harare&min_price=100&max_price=500&property_type=room
```

**Success Response (200):**
```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "title": "Cozy Room in Harare",
      "description": "Spacious room with modern amenities",
      "price": 250,
      "location": "Avondale",
      "city": "Harare",
      "property_type": "room",
      "amenities": ["WiFi", "Water", "Electricity"],
      "availability_status": "Available",
      "image": "http://your-domain/storage/properties/image.jpg",
      "images": {
        "main": "http://your-domain/storage/properties/main.jpg",
        "kitchen": "http://your-domain/storage/properties/kitchen.jpg",
        "bathroom": "http://your-domain/storage/properties/bathroom.jpg",
        "outside": "http://your-domain/storage/properties/outside.jpg"
      },
      "landlord": {
        "id": 5,
        "name": "Jane Smith"
      },
      "created_at": "2026-01-11T10:30:00.000000Z"
    }
  ]
}
```

---

### 2. Get Single Property

**Endpoint:** `GET /general/properties/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "status": true,
  "data": {
    "id": 1,
    "title": "Cozy Room in Harare",
    "description": "Spacious room with modern amenities",
    "price": 250,
    "location": "Avondale",
    "city": "Harare",
    "property_type": "room",
    "amenities": ["WiFi", "Water", "Electricity"],
    "availability_status": "Available",
    "bedrooms": 1,
    "bathrooms": 1,
    "size": "15sqm",
    "images": {
      "main": "http://your-domain/storage/properties/main.jpg",
      "kitchen": "http://your-domain/storage/properties/kitchen.jpg",
      "bathroom": "http://your-domain/storage/properties/bathroom.jpg",
      "outside": "http://your-domain/storage/properties/outside.jpg",
      "landlord": "http://your-domain/storage/properties/landlord.jpg"
    },
    "landlord": {
      "id": 5,
      "name": "Jane Smith",
      "phone": "+263771234567",
      "image": "http://your-domain/storage/users/avatar.jpg",
      "whatsapp_enabled": true
    },
    "created_at": "2026-01-11T10:30:00.000000Z"
  }
}
```

**Error Response (404):**
```json
{
  "status": false,
  "message": "Property not found"
}
```

---

### 3. Send Enquiry

**Endpoint:** `POST /general/enquiries`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "property_id": 1,
  "message": "Hi, I'm interested in this property. Is it still available?"
}
```

**Success Response (201):**
```json
{
  "status": true,
  "message": "Enquiry sent successfully",
  "data": {
    "id": 10,
    "property_id": 1,
    "tenant_id": 2,
    "landlord_id": 5,
    "message": "Hi, I'm interested in this property. Is it still available?",
    "status": "pending",
    "created_at": "2026-01-11T14:30:00.000000Z"
  }
}
```

---

### 4. Get Notifications

**Endpoint:** `GET /general/notifications`

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "type": "enquiry_response",
      "title": "Landlord Responded",
      "message": "Jane Smith responded to your enquiry about 'Cozy Room in Harare'",
      "data": {
        "property_id": 1,
        "enquiry_id": 10
      },
      "read": false,
      "created_at": "2026-01-11T15:00:00.000000Z"
    }
  ]
}
```

---

### 5. Mark Notifications as Read

**Endpoint:** `POST /general/notifications/mark-read`

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "status": true,
  "message": "All notifications marked as read"
}
```

---

## Landlord Endpoints

All landlord endpoints require:
- **Authentication:** Bearer Token
- **Role:** landlord
- **Housing Context:** general

### 1. Get Profile Status

**Endpoint:** `GET /general/landlord/profile/status`

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "status": true,
  "profile_complete": false,
  "missing_fields": ["address", "id_number", "bank_details"],
  "message": "Please complete your profile to start listing properties"
}
```

---

### 2. Update Profile

**Endpoint:** `POST /general/landlord/profile`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "address": "123 Main Street, Harare",
  "id_number": "63-123456A78",
  "bank_name": "CBZ Bank",
  "account_number": "1234567890",
  "whatsapp_enabled": true
}
```

**Success Response (200):**
```json
{
  "status": true,
  "message": "Profile updated successfully",
  "profile_complete": true
}
```

---

### 3. Get Own Properties

**Endpoint:** `GET /general/landlord/properties`

**Headers:**
```
Authorization: Bearer {token}
```

**Note:** Requires completed profile

**Success Response (200):**
```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "title": "Cozy Room in Harare",
      "price": 250,
      "location": "Avondale",
      "city": "Harare",
      "property_type": "room",
      "availability_status": "Available",
      "enquiries_count": 5,
      "created_at": "2026-01-11T10:30:00.000000Z"
    }
  ]
}
```

---

### 4. Create Property

**Endpoint:** `POST /general/landlord/properties`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Note:** Requires completed profile

**Request Body (Form Data):**
```
title: Cozy Room in Harare
pcontent: Spacious room with modern amenities
price: 250
location: Avondale
city: Harare
property_type: room
bedroom: 1
bathroom: 1
size: 15sqm
amenities[]: WiFi
amenities[]: Water
amenities[]: Electricity
pimage: [file]
pimage1: [file]
pimage2: [file]
pimage3: [file]
pimage4: [file]
```

**Fields:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| title | string | Yes | Property title |
| pcontent | text | Yes | Property description |
| price | number | Yes | Monthly rent |
| location | string | Yes | Specific location/suburb |
| city | string | Yes | City name |
| property_type | string | Yes | room, cottage, flat, house |
| bedroom | integer | No | Number of bedrooms |
| bathroom | integer | No | Number of bathrooms |
| size | string | No | Property size (e.g., "15sqm") |
| amenities | array | No | List of amenities |
| pimage | file | Yes | Main property image |
| pimage1 | file | No | Kitchen image |
| pimage2 | file | No | Bathroom image |
| pimage3 | file | No | Outside image |
| pimage4 | file | No | Additional image |

**Success Response (201):**
```json
{
  "status": true,
  "message": "Property created successfully",
  "data": {
    "id": 1,
    "title": "Cozy Room in Harare",
    "price": 250,
    "location": "Avondale",
    "city": "Harare",
    "property_type": "room",
    "availability_status": "Available"
  }
}
```

---

### 5. Update Property

**Endpoint:** `PUT /general/landlord/properties/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Note:** Requires completed profile

**Request Body:**
```json
{
  "title": "Updated Room Title",
  "price": 300,
  "availability_status": "Occupied"
}
```

**Success Response (200):**
```json
{
  "status": true,
  "message": "Property updated successfully"
}
```

---

### 6. Delete Property

**Endpoint:** `DELETE /general/landlord/properties/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Note:** Requires completed profile

**Success Response (200):**
```json
{
  "status": true,
  "message": "Property deleted successfully"
}
```

---

## Admin Endpoints

All admin endpoints require:
- **Authentication:** Bearer Token
- **Role:** admin

### 1. Get All Properties

**Endpoint:** `GET /admin/general/properties`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| status | string | No | Filter by status |
| page | integer | No | Page number |
| per_page | integer | No | Items per page |

**Success Response (200):**
```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "title": "Cozy Room in Harare",
      "landlord": {
        "id": 5,
        "name": "Jane Smith",
        "phone": "+263771234567"
      },
      "price": 250,
      "city": "Harare",
      "availability_status": "Available",
      "created_at": "2026-01-11T10:30:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "total_pages": 5,
    "total_items": 50,
    "per_page": 10
  }
}
```

---

### 2. Get All Users

**Endpoint:** `GET /admin/general/users`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| role | string | No | Filter by role (tenant/landlord) |
| page | integer | No | Page number |

**Success Response (200):**
```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "phone": "+263771234567",
      "email": "john@example.com",
      "role": "tenant",
      "profile_complete": true,
      "created_at": "2026-01-10T08:00:00.000000Z"
    }
  ]
}
```

---

### 3. Update Property Status

**Endpoint:** `PATCH /admin/general/property/{id}/status`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "availability_status": "Suspended",
  "reason": "Violates terms of service"
}
```

**Success Response (200):**
```json
{
  "status": true,
  "message": "Property status updated successfully"
}
```

---

## Error Handling

### Common HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful |
| 201 | Created | Resource created successfully |
| 401 | Unauthorized | Invalid or missing token |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Resource not found |
| 422 | Unprocessable Entity | Validation errors |
| 500 | Internal Server Error | Server error |

### Error Response Format

```json
{
  "status": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

---

## Complete Integration Flow

### Tenant Flow

```
1. Register as Tenant
   POST /register/general
   { "name": "...", "phone": "...", "role": "tenant", ... }
   → Save token

2. Login (if already registered)
   POST /login
   { "phone": "...", "password": "..." }
   → Save token

3. Browse Properties
   GET /general/properties?city=Harare&min_price=100
   Headers: Authorization: Bearer {token}

4. View Property Details
   GET /general/properties/1
   Headers: Authorization: Bearer {token}

5. Send Enquiry
   POST /general/enquiries
   Headers: Authorization: Bearer {token}
   { "property_id": 1, "message": "..." }

6. Check Notifications
   GET /general/notifications
   Headers: Authorization: Bearer {token}

7. Mark Notifications as Read
   POST /general/notifications/mark-read
   Headers: Authorization: Bearer {token}
```

### Landlord Flow

```
1. Register as Landlord
   POST /register/general
   { "name": "...", "phone": "...", "role": "landlord", ... }
   → Save token

2. Check Profile Status
   GET /general/landlord/profile/status
   Headers: Authorization: Bearer {token}

3. Complete Profile (if not complete)
   POST /general/landlord/profile
   Headers: Authorization: Bearer {token}
   { "address": "...", "id_number": "...", ... }

4. Create Property
   POST /general/landlord/properties
   Headers: Authorization: Bearer {token}
   Content-Type: multipart/form-data
   [Form data with property details and images]

5. View Own Properties
   GET /general/landlord/properties
   Headers: Authorization: Bearer {token}

6. Update Property
   PUT /general/landlord/properties/1
   Headers: Authorization: Bearer {token}
   { "price": 300, ... }

7. Delete Property
   DELETE /general/landlord/properties/1
   Headers: Authorization: Bearer {token}
```

---

## Frontend Implementation Tips

### 1. Token Management

```javascript
// Save token after login/registration
localStorage.setItem('auth_token', response.token);
localStorage.setItem('user', JSON.stringify(response.user));

// Add token to all requests
const config = {
  headers: {
    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
    'Content-Type': 'application/json'
  }
};
```

### 2. Role-Based Routing

```javascript
const user = JSON.parse(localStorage.getItem('user'));

if (user.role === 'tenant') {
  // Show tenant dashboard
  // Routes: /properties, /enquiries, /notifications
} else if (user.role === 'landlord') {
  // Show landlord dashboard
  // Routes: /my-properties, /add-property, /profile
}
```

### 3. Profile Completion Check (Landlords)

```javascript
// Before allowing property creation
const checkProfile = async () => {
  const response = await fetch('/api/general/landlord/profile/status', {
    headers: { 'Authorization': `Bearer ${token}` }
  });
  const data = await response.json();
  
  if (!data.profile_complete) {
    // Redirect to profile completion page
    // Show missing fields: data.missing_fields
  }
};
```

### 4. Image Upload (Property Creation)

```javascript
const formData = new FormData();
formData.append('title', 'Property Title');
formData.append('price', 250);
formData.append('pimage', imageFile); // File from input

const response = await fetch('/api/general/landlord/properties', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`
    // Don't set Content-Type, browser will set it with boundary
  },
  body: formData
});
```

---

## Testing with cURL

### Register
```bash
curl -X POST http://your-domain/api/register/general \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "phone": "+263771234567",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "tenant"
  }'
```

### Login
```bash
curl -X POST http://your-domain/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+263771234567",
    "password": "password123"
  }'
```

### Get Properties
```bash
curl -X GET "http://your-domain/api/general/properties?city=Harare" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Support

For additional information:
- **Swagger Documentation:** `http://your-domain/api/documentation`
- **API Base URL:** `http://your-domain/api`
- **Authentication:** Sanctum Bearer Token

---

**Last Updated:** January 11, 2026
**Version:** 1.0.0
