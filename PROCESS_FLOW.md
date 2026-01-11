# Paden - Process Flow Documentation

## Overview

Paden is a **student accommodation platform** built with Laravel. It connects **students** looking for accommodation with **landlords** listing properties near universities. The platform supports payments via Paynow (mobile money), real-time chat, property reviews, and room-sharing features.

---

## System Architecture

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│   Mobile App    │────▶│   Laravel API    │────▶│    Database     │
│   (Frontend)    │◀────│  (Sanctum Auth)  │◀────│    (MySQL)      │
└─────────────────┘     └──────────────────┘     └─────────────────┘
                               │
                    ┌──────────┼──────────┐
                    ▼          ▼          ▼
              ┌─────────┐ ┌─────────┐ ┌─────────┐
              │ Paynow  │ │ Pusher  │ │ Google  │
              │(Payment)│ │ (Chat)  │ │ (OAuth) │
              └─────────┘ └─────────┘ └─────────┘
```

---

## User Roles

| Role       | Description                                      |
|------------|--------------------------------------------------|
| **Student** | Searches for accommodation, pays fees, chats with landlords |
| **Landlord** | Lists properties, manages listings, communicates with students |
| **Admin**   | Manages users, views analytics, oversees platform |

---

## Core Process Flows

### 1. User Registration & Authentication

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Register  │───▶│   Upload    │───▶│    Pay      │───▶│   Access    │
│   Account   │    │   Profile   │    │ Reg. Fee    │    │    Home     │
│             │    │   Image     │    │ (Students)  │    │             │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
```

**Endpoints:**
- `POST /api/useregister` - Register new user
- `POST /api/login` - Login (email or phone)
- `POST /api/upload` - Upload profile image
- `POST /api/payment/regpayment` - Pay registration fee

**Flow Details:**
1. User registers with name, surname, email/phone, password, university, and role
2. System creates user with `image = "new"` flag
3. User must upload profile image (redirected if `image === "new"`)
4. Students must pay registration fee before accessing property listings
5. Token-based authentication via Laravel Sanctum

---

### 2. Property Listing Flow (Landlords)

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Login as  │───▶│   Create    │───▶│   Manage    │
│  Landlord   │    │  Property   │    │  Listings   │
└─────────────┘    └─────────────┘    └─────────────┘
                          │
                          ▼
                   ┌─────────────┐
                   │   Upload    │
                   │   Images    │
                   │ (Room, Kit- │
                   │ chen, etc.) │
                   └─────────────┘
```

**Endpoints:**
- `POST /api/properties` - Create property listing
- `GET /api/myproperties` - View own properties
- `PUT /api/properties/{id}` - Update property
- `DELETE /api/properties/{id}` - Delete property
- `PATCH /api/properties/{id}/roomnumber` - Update room availability

**Property Data Includes:**
- Location, description, price
- Amenities: Fridge, Bathroom, Tank, Stove, Solar, Parking, WiFi
- Images: Room, Kitchen, Toilet, Outside view, Landlord photo
- University/City association
- Room count & availability status

---

### 3. Property Search & Discovery (Students)

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Login &   │───▶│   Browse    │───▶│    View     │───▶│   Get       │
│   Pay Fee   │    │  Properties │    │   Details   │    │ Directions  │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
                          │                   │
                          ▼                   ▼
                   ┌─────────────┐    ┌─────────────┐
                   │   Filter/   │    │    Like/    │
                   │   Search    │    │   Review    │
                   └─────────────┘    └─────────────┘
```

**Endpoints:**
- `GET /api/home` - Get properties for user's university
- `GET /api/homedisplay/{id}` - View property details
- `POST /api/search` - Search properties
- `GET /api/properties/bylocation` - Filter by location
- `POST /api/home/like` - Like a property
- `GET /api/properties/directions/{id}` - Get directions to property

**Home Flow Logic:**
1. Verify user role is "Student"
2. Check if profile image is uploaded
3. Verify registration fee payment (`regMoney` table)
4. Return properties matching user's university/city

---

### 4. Payment Flow

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Initiate  │───▶│   Paynow    │───▶│    Poll     │───▶│   Record    │
│   Payment   │    │   Request   │    │  Transaction│    │   Payment   │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
```

**Payment Types:**
1. **Registration Payment** (`POST /api/payment/regpayment`)
   - Required for students to access property listings
   - Stored in `regMoney` table

2. **Direction Payment** (`POST /api/directions/pay/{id}`)
   - Payment to get directions to specific property
   - Stored in `Directions` table

**Payment Methods:** EcoCash, OneMoney (via Paynow)

---

### 5. Communication Flow

#### Real-Time Chat (via Pusher)
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Student   │───▶│   Pusher    │───▶│  Landlord   │
│   Sends     │    │  Broadcast  │    │  Receives   │
│   Message   │    │             │    │   Message   │
└─────────────┘    └─────────────┘    └─────────────┘
```

**Chat Endpoints:**
- `POST /api/chat/send` - Send message
- `GET /api/chat/messages/{userId}` - Get messages with user
- `GET /api/chat/conversations` - Get all conversations
- `POST /api/chat/mark-read/{userId}` - Mark messages as read

#### SMS Notifications
- `POST /api/sms/send/{userId}` - Send SMS notification

---

### 6. Room Share Requests

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Student   │───▶│   Create    │───▶│   Other     │
│   Posts     │    │  Room Share │    │  Students   │
│   Request   │    │   Listing   │    │    View     │
└─────────────┘    └─────────────┘    └─────────────┘
                                             │
                          ┌──────────────────┼──────────────────┐
                          ▼                  ▼                  ▼
                   ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
                   │   Accept    │    │   Reject    │    │   Message   │
                   └─────────────┘    └─────────────┘    └─────────────┘
```

**Endpoints:**
- `POST /api/room-share/send` - Create room share request
- `GET /api/room-share/university` - View university posts
- `PUT /api/room-share/accept/{id}` - Accept request
- `PUT /api/room-share/reject/{id}` - Reject request
- `GET /api/room-share/students` - View potential roommates

---

### 7. Reviews & Ratings

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Student   │───▶│   Submit    │───▶│   Property  │
│   Views     │    │   Review    │    │   Rating    │
│  Property   │    │             │    │   Updated   │
└─────────────┘    └─────────────┘    └─────────────┘
```

**Endpoints:**
- `POST /api/reviews/store` - Submit review
- `GET /api/property/{id}/reviews` - Get property reviews
- `GET /api/property/{id}/rating-summary` - Get rating summary
- `DELETE /api/reviews/delete/{reviewId}` - Delete review

---

### 8. Admin Dashboard Flow

```
┌─────────────┐    ┌─────────────┐
│   Admin     │───▶│  Dashboard  │
│   Login     │    │   Access    │
└─────────────┘    └─────────────┘
                          │
         ┌────────────────┼────────────────┐
         ▼                ▼                ▼
  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐
  │   Manage    │  │    View     │  │   Payment   │
  │   Users     │  │  Analytics  │  │   Reports   │
  └─────────────┘  └─────────────┘  └─────────────┘
```

**Admin Routes (Web):**
- `/admin/dashboard` - Main dashboard
- `/admin/landlords` - Manage landlords
- `/admin/students` - Manage students
- `/admin/properties` - View all properties
- `/admin/reg-payments` - Registration payments
- `/admin/direction-payments` - Direction payments
- `/admin/universities` - Manage universities
- `/admin/reviews` - Moderate reviews
- `/admin/users` - User management & permissions

---

## Database Models

| Model | Purpose |
|-------|---------|
| `User` | User accounts (students, landlords, admins) |
| `Properties` | Property listings |
| `regMoney` | Registration payments |
| `Directions` | Direction payment records |
| `Message` | Chat messages |
| `Review` | Property reviews |
| `Like` | Property likes |
| `Views` | Property view tracking |
| `University` | University list |
| `RoomShareRequest` | Room sharing requests |
| `EmailVerificationCode` | Email verification |

---

## Authentication Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    Authentication Methods                        │
├─────────────────────────────────────────────────────────────────┤
│  1. Email + Password                                             │
│  2. Phone + Password                                             │
│  3. Google OAuth (Social Login)                                  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Laravel Sanctum Token                         │
│         (Bearer token for all authenticated API requests)        │
└─────────────────────────────────────────────────────────────────┘
```

**Password Reset:**
- `POST /api/password/email` - Request reset link
- `POST /api/password/reset` - Reset password

**Email Verification:**
- `POST /api/email/verify` - Verify email
- `POST /api/email/resend` - Resend verification

---

## API Security

- **Authentication:** Laravel Sanctum (token-based)
- **Rate Limiting:** 60 requests/minute per user
- **Middleware:** `auth:sanctum` for protected routes
- **Authorization:** Role-based access (Student/Landlord/Admin)

---

## External Integrations

| Service | Purpose |
|---------|---------|
| **Paynow** | Mobile money payments (EcoCash, OneMoney) |
| **Pusher** | Real-time chat broadcasting |
| **Google OAuth** | Social authentication |
| **SMS Gateway** | SMS notifications |

---

## File Storage

- Profile images: `storage/profile/`
- Property images: `storage/properties/`
- Accessible via: `asset('storage/...')`
