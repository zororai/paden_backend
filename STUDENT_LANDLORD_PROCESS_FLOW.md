# Student & Landlord Accommodation Process Flow

## Overview

This document outlines the complete process flows for **Students** and **Landlords** on the Paden accommodation platform. The platform facilitates student housing discovery, landlord property management, and secure communication between both parties.

---

## Student Process Flow

### 1. Registration & Onboarding

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Download   │───▶│   Register   │───▶│   Upload     │───▶│    Pay       │
│     App      │    │   Account    │    │   Profile    │    │  Reg. Fee    │
│              │    │              │    │   Image      │    │   ($X)       │
└──────────────┘    └──────────────┘    └──────────────┘    └──────────────┘
                                                                    │
                                                                    ▼
                                                           ┌──────────────┐
                                                           │   Access     │
                                                           │   Platform   │
                                                           └──────────────┘
```

**Step-by-Step:**

| Step | Action | Endpoint | Details |
|------|--------|----------|---------|
| 1 | Create Account | `POST /api/useregister` | Provide name, surname, email/phone, password, university, role="Student" |
| 2 | Upload Profile Image | `POST /api/upload` | Profile image required before platform access |
| 3 | Pay Registration Fee | `POST /api/payment/regpayment` | Payment via EcoCash/OneMoney (Paynow) |
| 4 | Access Home | `GET /api/home` | View properties for student's university |

---

### 2. Property Discovery

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Browse     │───▶│   Filter/    │───▶│   View       │
│  Properties  │    │   Search     │    │   Details    │
└──────────────┘    └──────────────┘    └──────────────┘
       │                                       │
       │                                       ▼
       │                              ┌──────────────┐
       │                              │  Like/Save   │
       │                              │  Property    │
       │                              └──────────────┘
       │                                       │
       ▼                                       ▼
┌──────────────┐                      ┌──────────────┐
│   Sort by    │                      │   View       │
│   Distance   │                      │   Reviews    │
└──────────────┘                      └──────────────┘
```

**Available Actions:**

| Action | Endpoint | Description |
|--------|----------|-------------|
| Browse Properties | `GET /api/home` | View all properties for student's university |
| Search Properties | `POST /api/search` | Search by keywords |
| Filter by Location | `GET /api/properties/bylocation` | Filter by area/city |
| View Property Details | `GET /api/homedisplay/{id}` | Full property information with images |
| Like Property | `POST /api/home/like` | Save to favorites |
| View Reviews | `GET /api/property/{id}/reviews` | Read landlord/property reviews |

---

### 3. Getting Directions to Property

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Select     │───▶│   Pay        │───▶│   Receive    │
│   Property   │    │  Direction   │    │   GPS        │
│              │    │     Fee      │    │  Directions  │
└──────────────┘    └──────────────┘    └──────────────┘
```

**Process:**

1. Student selects a property they're interested in
2. Pay direction fee via `POST /api/directions/pay/{id}`
3. Receive exact location/directions via `GET /api/properties/directions/{id}`

---

### 4. Communication with Landlord

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   View       │───▶│   Start      │───▶│   Real-time  │
│   Property   │    │   Chat       │    │   Messaging  │
└──────────────┘    └──────────────┘    └──────────────┘
                                               │
                                               ▼
                                        ┌──────────────┐
                                        │   Arrange    │
                                        │   Viewing    │
                                        └──────────────┘
```

**Chat Endpoints:**

| Action | Endpoint |
|--------|----------|
| Send Message | `POST /api/chat/send` |
| Get Messages | `GET /api/chat/messages/{userId}` |
| View Conversations | `GET /api/chat/conversations` |
| Mark as Read | `POST /api/chat/mark-read/{userId}` |

---

### 5. Room Sharing

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Post       │───▶│   Other      │───▶│   Accept/    │
│   Room Share │    │   Students   │    │   Reject     │
│   Request    │    │   Respond    │    │   Offers     │
└──────────────┘    └──────────────┘    └──────────────┘
```

**Room Share Flow:**

| Action | Endpoint |
|--------|----------|
| Create Request | `POST /api/room-share/send` |
| View University Posts | `GET /api/room-share/university` |
| View Potential Roommates | `GET /api/room-share/students` |
| Accept Request | `PUT /api/room-share/accept/{id}` |
| Reject Request | `PUT /api/room-share/reject/{id}` |

---

### 6. Leave Reviews

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Stay at    │───▶│   Submit     │───▶│   Review     │
│   Property   │    │   Review     │    │   Published  │
└──────────────┘    └──────────────┘    └──────────────┘
```

**Endpoints:**

- Submit Review: `POST /api/reviews/store`
- View Rating Summary: `GET /api/property/{id}/rating-summary`
- Delete Review: `DELETE /api/reviews/delete/{reviewId}`

---

## Landlord Process Flow

### 1. Registration & Profile Setup

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Register   │───▶│   Upload     │───▶│   Complete   │
│   Account    │    │   Profile    │    │   Profile    │
│   (Landlord) │    │   Image      │    │   Details    │
└──────────────┘    └──────────────┘    └──────────────┘
                                               │
                                               ▼
                                        ┌──────────────┐
                                        │   Dashboard  │
                                        │   Access     │
                                        └──────────────┘
```

**Registration:**

| Step | Action | Endpoint |
|------|--------|----------|
| 1 | Create Account | `POST /api/useregister` (role="Landlord") |
| 2 | Upload Profile Image | `POST /api/upload` |
| 3 | Access Dashboard | `GET /api/myproperties` |

---

### 2. Property Listing Management

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Create     │───▶│   Upload     │───▶│   Property   │
│   Listing    │    │   Property   │    │   Live       │
│              │    │   Images     │    │              │
└──────────────┘    └──────────────┘    └──────────────┘
       │
       ▼
┌───────────────────────────────────────────────────────┐
│                  PROPERTY MANAGEMENT                   │
├──────────────┬──────────────┬──────────────┬──────────┤
│    View      │    Edit      │   Update     │  Delete  │
│  Listings    │   Details    │   Rooms      │ Listing  │
└──────────────┴──────────────┴──────────────┴──────────┘
```

**Property CRUD Operations:**

| Action | Endpoint | Method |
|--------|----------|--------|
| Create Property | `/api/properties` | POST |
| View Own Properties | `/api/myproperties` | GET |
| Update Property | `/api/properties/{id}` | PUT |
| Delete Property | `/api/properties/{id}` | DELETE |
| Update Room Availability | `/api/properties/{id}/roomnumber` | PATCH |

**Property Details Required:**

- **Location Info:** Address, city, university association
- **Description:** Property details, rules
- **Pricing:** Monthly rent
- **Amenities:** Fridge, Bathroom, Tank, Stove, Solar, Parking, WiFi
- **Images:** Room, Kitchen, Toilet, Outside view, Landlord photo
- **Availability:** Room count, available rooms

---

### 3. Managing Inquiries & Communication

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Receive    │───▶│   Chat with  │───▶│   Arrange    │
│   Student    │    │   Student    │    │   Property   │
│   Messages   │    │              │    │   Viewing    │
└──────────────┘    └──────────────┘    └──────────────┘
```

**Communication Tools:**

| Feature | Endpoint |
|---------|----------|
| View All Conversations | `GET /api/chat/conversations` |
| Send Response | `POST /api/chat/send` |
| Send SMS Notification | `POST /api/sms/send/{userId}` |

---

### 4. Monitor Property Performance

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   View       │───▶│   Check      │───▶│   Respond    │
│   Analytics  │    │   Reviews    │    │   to         │
│   (Views)    │    │              │    │   Feedback   │
└──────────────┘    └──────────────┘    └──────────────┘
```

**Metrics Available:**

- Property views count
- Student likes/favorites
- Reviews and ratings
- Inquiry messages

---

## Interaction Flow: Student ↔ Landlord

```
                    ┌─────────────────────────────────┐
                    │          PADEN PLATFORM          │
                    └─────────────────────────────────┘
                                    │
          ┌─────────────────────────┼─────────────────────────┐
          │                         │                         │
          ▼                         ▼                         ▼
   ┌─────────────┐          ┌─────────────┐          ┌─────────────┐
   │   STUDENT   │          │    CHAT     │          │  LANDLORD   │
   │             │◀────────▶│   SYSTEM    │◀────────▶│             │
   └─────────────┘          │  (Pusher)   │          └─────────────┘
          │                 └─────────────┘                  │
          │                                                  │
          ▼                                                  ▼
   ┌─────────────┐                                   ┌─────────────┐
   │ - Search    │                                   │ - List      │
   │ - Browse    │                                   │ - Manage    │
   │ - Like      │                                   │ - Update    │
   │ - Pay       │                                   │ - Respond   │
   │ - Review    │                                   │ - Monitor   │
   └─────────────┘                                   └─────────────┘
```

---

## Complete Journey: Student Finding Accommodation

```
┌───────────────────────────────────────────────────────────────────────────┐
│                         STUDENT ACCOMMODATION JOURNEY                      │
└───────────────────────────────────────────────────────────────────────────┘
                                     │
     ┌───────────────────────────────┼───────────────────────────────┐
     │                               │                               │
     ▼                               ▼                               ▼
┌─────────┐                    ┌─────────┐                    ┌─────────┐
│ PHASE 1 │                    │ PHASE 2 │                    │ PHASE 3 │
│ ONBOARD │                    │ DISCOVER│                    │ CONNECT │
└─────────┘                    └─────────┘                    └─────────┘
     │                               │                               │
     ▼                               ▼                               ▼
┌─────────────┐              ┌─────────────┐              ┌─────────────┐
│ Register    │              │ Browse      │              │ Chat with   │
│ Upload Pic  │              │ Search      │              │ Landlord    │
│ Pay Reg Fee │              │ Filter      │              │             │
└─────────────┘              │ Like/Save   │              └─────────────┘
                             └─────────────┘                     │
                                    │                            ▼
                                    ▼                     ┌─────────────┐
                             ┌─────────────┐              │ Pay for     │
                             │ View Details│              │ Directions  │
                             │ Read Reviews│              └─────────────┘
                             └─────────────┘                     │
                                                                 ▼
                                                          ┌─────────────┐
                                                          │ Visit       │
                                                          │ Property    │
                                                          └─────────────┘
                                                                 │
                                                                 ▼
                                                          ┌─────────────┐
                                                          │ Move In &   │
                                                          │ Leave Review│
                                                          └─────────────┘
```

---

## Complete Journey: Landlord Listing Property

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
│ Register    │              │ Create      │              │ Respond to  │
│ as Landlord │              │ Property    │              │ Inquiries   │
│             │              │ Listing     │              │             │
└─────────────┘              └─────────────┘              └─────────────┘
     │                               │                            │
     ▼                               ▼                            ▼
┌─────────────┐              ┌─────────────┐              ┌─────────────┐
│ Upload      │              │ Add Details │              │ Chat with   │
│ Profile     │              │ & Amenities │              │ Students    │
│ Image       │              │             │              │             │
└─────────────┘              └─────────────┘              └─────────────┘
                                    │                            │
                                    ▼                            ▼
                             ┌─────────────┐              ┌─────────────┐
                             │ Upload      │              │ Arrange     │
                             │ Property    │              │ Viewings    │
                             │ Images      │              │             │
                             └─────────────┘              └─────────────┘
                                    │                            │
                                    ▼                            ▼
                             ┌─────────────┐              ┌─────────────┐
                             │ Property    │              │ Update      │
                             │ Goes Live   │              │ Availability│
                             └─────────────┘              └─────────────┘
```

---

## Payment Summary

| Payment Type | Payer | Purpose | Endpoint |
|--------------|-------|---------|----------|
| Registration Fee | Student | Access platform | `POST /api/payment/regpayment` |
| Direction Fee | Student | Get property location | `POST /api/directions/pay/{id}` |

**Payment Methods:** EcoCash, OneMoney (via Paynow integration)

---

## Security & Authentication

- **Token-Based Auth:** Laravel Sanctum
- **Rate Limiting:** 60 requests/minute
- **Role-Based Access:** Student, Landlord, Admin
- **Profile Verification:** Image upload required

---

## Summary

| User Type | Key Actions |
|-----------|-------------|
| **Student** | Register → Pay Fee → Browse → Search → Chat → Get Directions → Visit → Review |
| **Landlord** | Register → Create Listing → Upload Images → Manage Properties → Respond to Inquiries |

---

*Document Version: 1.0*  
*Last Updated: January 2026*
