# General Housing Process Flow - Tenants & Landlords

## Overview

This document outlines the complete process flows for **Tenants** and **Landlords** in the General Housing module. Unlike student housing, general housing serves the broader rental market without university affiliation requirements.

---

## Key Differences from Student Housing

| Aspect | Student Housing | General Housing |
|--------|-----------------|-----------------|
| Target Users | University students | General public |
| University Required | Yes | No |
| Registration Fee | Required | Not required |
| Property Types | Rooms near universities | Rooms, cottages, flats, houses |

---

## Tenant Process Flow

### 1. Registration & Onboarding

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Download   │───▶│   Register   │───▶│   Complete   │───▶│   Access     │
│     App      │    │   Account    │    │   Profile    │    │  Dashboard   │
│              │    │  (Tenant)    │    │              │    │              │
└──────────────┘    └──────────────┘    └──────────────┘    └──────────────┘
```

**Registration Steps:**

| Step | Action | Endpoint | Details |
|------|--------|----------|---------|
| 1 | Create Account | `POST /api/register/general` | name, email/phone, password, role="tenant" |
| 2 | Receive Token | - | Sanctum token issued automatically |
| 3 | Complete Profile | `POST /api/profile` | Add personal details |
| 4 | Access Dashboard | `GET /api/general/properties` | Browse available properties |

**Registration Data:**
- name
- email OR phone
- password
- role: `tenant`
- housing_context: `general` (set automatically)

---

### 2. Property Discovery

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Browse     │───▶│   Filter     │───▶│   View       │
│  Properties  │    │  Properties  │    │   Details    │
└──────────────┘    └──────────────┘    └──────────────┘
       │                   │                   │
       ▼                   ▼                   ▼
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Search     │    │  By Location │    │   Save to    │
│   Keywords   │    │  By Price    │    │  Favorites   │
│              │    │  By Type     │    │              │
└──────────────┘    └──────────────┘    └──────────────┘
```

**Property Discovery Endpoints:**

| Action | Endpoint | Method |
|--------|----------|--------|
| Browse All Properties | `/api/general/properties` | GET |
| View Property Details | `/api/general/properties/{id}` | GET |
| Filter Properties | `/api/general/properties?location=X&price=Y&type=Z` | GET |
| Save Favorite | `/api/general/favorites` | POST |

**Filter Options:**
- **Location:** City, suburb, area
- **Price Range:** Min/max monthly rent
- **Property Type:** Room, cottage, flat, house
- **Amenities:** WiFi, parking, furnished, etc.

---

### 3. Contacting Landlords

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   View       │───▶│   Send       │───▶│   Receive    │
│   Property   │    │   Enquiry    │    │   Response   │
└──────────────┘    └──────────────┘    └──────────────┘
                           │
                           ▼
                    ┌──────────────┐
                    │   Real-time  │
                    │   Chat       │
                    │   (Pusher)   │
                    └──────────────┘
```

**Communication Endpoints:**

| Action | Endpoint | Method |
|--------|----------|--------|
| Send Enquiry | `/api/general/enquiries` | POST |
| View Conversations | `/api/chat/conversations` | GET |
| Send Message | `/api/chat/send` | POST |
| Get Messages | `/api/chat/messages/{userId}` | GET |
| Mark as Read | `/api/chat/mark-read/{userId}` | POST |

---

### 4. Viewing & Notifications

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Arrange    │───▶│   Visit      │───▶│   Decide     │
│   Viewing    │    │   Property   │    │   to Rent    │
└──────────────┘    └──────────────┘    └──────────────┘
       │
       ▼
┌──────────────┐
│   Receive    │
│   Notifi-    │
│   cations    │
└──────────────┘
```

**Notification Endpoint:**
- `GET /api/general/notifications` - View all notifications

---

### 5. Complete Tenant Journey

```
┌───────────────────────────────────────────────────────────────────────────┐
│                         TENANT RENTAL JOURNEY                              │
└───────────────────────────────────────────────────────────────────────────┘
                                     │
     ┌───────────────────────────────┼───────────────────────────────┐
     │                               │                               │
     ▼                               ▼                               ▼
┌─────────┐                    ┌─────────┐                    ┌─────────┐
│ PHASE 1 │                    │ PHASE 2 │                    │ PHASE 3 │
│  SETUP  │                    │  SEARCH │                    │ CONNECT │
└─────────┘                    └─────────┘                    └─────────┘
     │                               │                               │
     ▼                               ▼                               ▼
┌─────────────┐              ┌─────────────┐              ┌─────────────┐
│ Register    │              │ Browse      │              │ Contact     │
│ Account     │              │ Listings    │              │ Landlord    │
│             │              │             │              │             │
└─────────────┘              └─────────────┘              └─────────────┘
     │                               │                            │
     ▼                               ▼                            ▼
┌─────────────┐              ┌─────────────┐              ┌─────────────┐
│ Complete    │              │ Filter by:  │              │ Send        │
│ Profile     │              │ - Location  │              │ Enquiry     │
│             │              │ - Price     │              │             │
└─────────────┘              │ - Type      │              └─────────────┘
                             └─────────────┘                     │
                                    │                            ▼
                                    ▼                     ┌─────────────┐
                             ┌─────────────┐              │ Chat with   │
                             │ View        │              │ Landlord    │
                             │ Details     │              └─────────────┘
                             └─────────────┘                     │
                                    │                            ▼
                                    ▼                     ┌─────────────┐
                             ┌─────────────┐              │ Arrange     │
                             │ Save        │              │ Viewing     │
                             │ Favorites   │              └─────────────┘
                             └─────────────┘                     │
                                                                 ▼
                                                          ┌─────────────┐
                                                          │ Move In     │
                                                          └─────────────┘
```

---

## Landlord Process Flow

### 1. Registration & Profile Setup

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Register   │───▶│   Verify     │───▶│   Complete   │───▶│   Dashboard  │
│   Account    │    │   Email/     │    │   Landlord   │    │   Access     │
│  (Landlord)  │    │   Phone      │    │   Profile    │    │              │
└──────────────┘    └──────────────┘    └──────────────┘    └──────────────┘
```

**Registration Steps:**

| Step | Action | Endpoint |
|------|--------|----------|
| 1 | Create Account | `POST /api/register/general` (role="landlord") |
| 2 | Check Profile Status | `GET /api/general/landlord/profile/status` |
| 3 | Complete Profile | `POST /api/general/landlord/profile` |
| 4 | Access Dashboard | `GET /api/general/landlord/properties` |

**Profile Requirements:**
- full_name
- phone
- preferred_contact
- whatsapp_enabled (boolean)

⚠️ **Profile must be completed before listing properties**

---

### 2. Property Listing Management

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Create     │───▶│   Add        │───▶│   Upload     │
│   New        │    │   Property   │    │   Property   │
│   Listing    │    │   Details    │    │   Images     │
└──────────────┘    └──────────────┘    └──────────────┘
                                               │
                                               ▼
                                        ┌──────────────┐
                                        │   Property   │
                                        │   Goes LIVE  │
                                        └──────────────┘
```

**Property CRUD Endpoints:**

| Action | Endpoint | Method |
|--------|----------|--------|
| Create Property | `/api/general/landlord/properties` | POST |
| View My Properties | `/api/general/landlord/properties` | GET |
| Update Property | `/api/general/landlord/properties/{id}` | PUT |
| Delete Property | `/api/general/landlord/properties/{id}` | DELETE |

**Property Attributes:**

| Field | Description |
|-------|-------------|
| title | Property listing title |
| description | Detailed description |
| price | Monthly rental amount |
| location | Address/area |
| property_type | room, cottage, flat, house |
| amenities | List of available amenities |
| images | Property photos |
| availability_status | Available/Occupied |

---

### 3. Property Types

```
┌───────────────────────────────────────────────────────┐
│                   PROPERTY TYPES                       │
├─────────────┬─────────────┬─────────────┬─────────────┤
│    ROOM     │   COTTAGE   │    FLAT     │   HOUSE     │
│             │             │             │             │
│  Single     │  Small      │  Apartment  │  Full       │
│  room in    │  standalone │  unit in    │  standalone │
│  shared     │  unit       │  building   │  house      │
│  property   │             │             │             │
└─────────────┴─────────────┴─────────────┴─────────────┘
```

---

### 4. Managing Inquiries

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Receive    │───▶│   Review     │───▶│   Respond    │
│   Tenant     │    │   Enquiry    │    │   to         │
│   Enquiry    │    │              │    │   Tenant     │
└──────────────┘    └──────────────┘    └──────────────┘
       │
       ▼
┌──────────────┐
│   Continue   │
│   Chat       │
│   Convo      │
└──────────────┘
```

**Communication Flow:**

1. Tenant sends enquiry about property
2. Landlord receives notification
3. Landlord reviews tenant profile
4. Real-time chat begins
5. Arrange property viewing
6. Finalize rental agreement

---

### 5. Complete Landlord Journey

```
┌───────────────────────────────────────────────────────────────────────────┐
│                         LANDLORD LISTING JOURNEY                           │
└───────────────────────────────────────────────────────────────────────────┘
                                     │
     ┌───────────────────────────────┼───────────────────────────────┐
     │                               │                               │
     ▼                               ▼                               ▼
┌─────────┐                    ┌─────────┐                    ┌─────────┐
│ PHASE 1 │                    │ PHASE 2 │                    │ PHASE 3 │
│  SETUP  │                    │  LIST   │                    │ MANAGE  │
└─────────┘                    └─────────┘                    └─────────┘
     │                               │                               │
     ▼                               ▼                               ▼
┌─────────────┐              ┌─────────────┐              ┌─────────────┐
│ Register    │              │ Create      │              │ View        │
│ as Landlord │              │ Property    │              │ Enquiries   │
│             │              │ Listing     │              │             │
└─────────────┘              └─────────────┘              └─────────────┘
     │                               │                            │
     ▼                               ▼                            ▼
┌─────────────┐              ┌─────────────┐              ┌─────────────┐
│ Complete    │              │ Set Details │              │ Respond to  │
│ Profile     │              │ - Title     │              │ Tenants     │
│ - Name      │              │ - Desc      │              │             │
│ - Phone     │              │ - Price     │              └─────────────┘
│ - Contact   │              │ - Location  │                     │
└─────────────┘              │ - Type      │                     ▼
                             │ - Amenities │              ┌─────────────┐
                             └─────────────┘              │ Arrange     │
                                    │                     │ Viewings    │
                                    ▼                     └─────────────┘
                             ┌─────────────┐                     │
                             │ Upload      │                     ▼
                             │ Images      │              ┌─────────────┐
                             └─────────────┘              │ Update      │
                                    │                     │ Availability│
                                    ▼                     └─────────────┘
                             ┌─────────────┐                     │
                             │ Property    │                     ▼
                             │ Goes Live   │              ┌─────────────┐
                             └─────────────┘              │ Mark as     │
                                                          │ Occupied    │
                                                          └─────────────┘
```

---

## Interaction Flow: Tenant ↔ Landlord

```
                    ┌─────────────────────────────────┐
                    │      GENERAL HOUSING PLATFORM    │
                    └─────────────────────────────────┘
                                    │
          ┌─────────────────────────┼─────────────────────────┐
          │                         │                         │
          ▼                         ▼                         ▼
   ┌─────────────┐          ┌─────────────┐          ┌─────────────┐
   │   TENANT    │          │    CHAT     │          │  LANDLORD   │
   │             │◀────────▶│   SYSTEM    │◀────────▶│             │
   └─────────────┘          │  (Pusher)   │          └─────────────┘
          │                 └─────────────┘                  │
          │                                                  │
          ▼                                                  ▼
   ┌─────────────┐                                   ┌─────────────┐
   │ Actions:    │                                   │ Actions:    │
   │ - Browse    │                                   │ - List      │
   │ - Filter    │                                   │ - Manage    │
   │ - Enquire   │                                   │ - Update    │
   │ - Save      │                                   │ - Respond   │
   │ - Message   │                                   │ - Mark      │
   └─────────────┘                                   └─────────────┘
```

---

## API Endpoint Summary

### Tenant Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/register/general` | POST | Register as tenant |
| `/api/login` | POST | Login (shared) |
| `/api/general/properties` | GET | Browse properties |
| `/api/general/properties/{id}` | GET | View property details |
| `/api/general/enquiries` | POST | Send enquiry to landlord |
| `/api/general/notifications` | GET | View notifications |
| `/api/chat/send` | POST | Send message |
| `/api/chat/conversations` | GET | View all chats |

### Landlord Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/register/general` | POST | Register as landlord |
| `/api/general/landlord/profile/status` | GET | Check profile status |
| `/api/general/landlord/profile` | POST | Complete profile |
| `/api/general/landlord/properties` | GET | View my properties |
| `/api/general/landlord/properties` | POST | Create listing |
| `/api/general/landlord/properties/{id}` | PUT | Update listing |
| `/api/general/landlord/properties/{id}` | DELETE | Delete listing |

---

## Property Visibility Rules

| User Type | Can View |
|-----------|----------|
| Tenant | Only active/available properties |
| Landlord | Only their own listings |
| Admin | All properties (for moderation) |

---

## Security & Middleware

**Applied Middleware:**
- `auth:sanctum` - Authentication
- `role:tenant` - Tenant-only routes
- `role:landlord` - Landlord-only routes
- `role:admin` - Admin-only routes

**Rate Limits:**
- Authentication: 10 requests/minute
- Property creation: 5 requests/minute
- Search: 60 requests/minute

---

## Shared Services

The following are shared with the Student Housing module:
- Authentication (Sanctum)
- Real-time messaging (Pusher)
- Image storage
- Notifications
- Admin moderation
- Rate limiting

---

## Summary

| User Type | Key Actions |
|-----------|-------------|
| **Tenant** | Register → Complete Profile → Browse → Filter → Enquire → Chat → View → Rent |
| **Landlord** | Register → Complete Profile → Create Listing → Upload Images → Respond → Manage |

---

*Document Version: 1.0*  
*Last Updated: January 2026*
