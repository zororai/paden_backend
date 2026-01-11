# GENERAL_HOUSING_PROCESS_FLOW

## SYSTEM CONTEXT
This document defines the back-end process flow for the General Housing module.
It reuses the same authentication, authorization, and user management logic defined in PROCESS_FLOW.
No separate or parallel login system exists.

Frontend reference: GENERAL_HOUSING_FLOW  
Authentication reference: PROCESS_FLOW (Laravel + Sanctum)

---

## CORE PRINCIPLES
- A single user table is used
- A single authentication system is used
- A single Sanctum token is issued per session
- Housing separation is handled through role and context, not separate accounts

---

## USER TYPES
- Tenant: general housing renter
- Landlord: general housing property owner
- Admin: system administrator (shared with student housing)

User attributes:
- role: `tenant | landlord | admin`
- housing_context: `general | student`

---

## AUTHENTICATION FLOW (SHARED)

### LOGIN
Endpoint:  
POST /api/login

Request fields:
- email OR phone
- password

Process:
1. Credentials are validated
2. User is authenticated
3. A Sanctum token is generated
4. The response includes:
   - authentication token
   - role
   - housing_context
   - profile completion status

The login flow is shared across all housing contexts.

---

### REGISTRATION (GENERAL HOUSING)
Endpoint:  
POST /api/register/general

Request fields:
- name
- email OR phone
- password
- role (tenant or landlord)

Process:
1. Input validation is performed
2. A new user record is created
3. The following attributes are set:
   - housing_context = general
   - role = tenant or landlord
4. Profile status is marked as incomplete
5. A Sanctum token is issued
6. Authentication details are returned

---

## POST-LOGIN ROUTING
After authentication:
1. The housing_context value is evaluated
2. If housing_context is `student`, the student housing flow applies
3. If housing_context is `general`, the general housing flow applies

---

## ROLE-BASED GENERAL HOUSING FLOW

### TENANT FLOW

Entry conditions:
- role = tenant
- housing_context = general

Process:
1. Token is validated using Sanctum
2. Profile status is verified
3. Tenant dashboard access is granted

Available endpoints:
- GET /api/general/properties
- GET /api/general/properties/{id}
- POST /api/general/enquiries
- GET /api/general/notifications

Tenant capabilities:
- Browse property listings
- Filter by location, price, and type
- View property details
- Contact landlords
- Save favorite listings

---

### LANDLORD FLOW

Entry conditions:
- role = landlord
- housing_context = general

Profile requirement:
- Profile completion is mandatory before listing properties

Endpoint:
- GET /api/general/landlord/profile/status

If the profile is incomplete, access is restricted until completion.

---

## LANDLORD PROFILE SETUP

Endpoint:  
POST /api/general/landlord/profile

Required data:
- full_name
- phone
- preferred_contact
- whatsapp_enabled

Process:
1. Submitted data is validated
2. Profile details are stored
3. Profile completion status is updated
4. Dashboard access is enabled

---

## LANDLORD DASHBOARD

Endpoints:
- GET /api/general/landlord/properties
- POST /api/general/landlord/properties
- PUT /api/general/landlord/properties/{id}
- DELETE /api/general/landlord/properties/{id}

Property attributes:
- title
- description
- price
- location
- property_type (room, cottage, flat, house)
- amenities
- images
- availability_status

---

## PROPERTY VISIBILITY RULES
- Only active properties are visible to tenants
- Landlords can access only their own listings
- Administrators can access all listings

---

## SHARED SERVICES
The following services are shared with other housing modules:
- Sanctum authentication
- Notifications
- Image storage and retrieval
- Messaging between tenants and landlords
- Administrative moderation
- Rate limiting
- Role-based access control

---

## ADMIN FLOW
Administrative capabilities include:
- User management
- Property approval and suspension
- Analytics access
- Listing moderation

Endpoints:
- GET /admin/general/properties
- GET /admin/general/users
- PATCH /admin/general/property/{id}/status

---

## SECURITY AND MIDDLEWARE

Applied middleware:
- auth:sanctum
- role:tenant
- role:landlord
- role:admin

Rate limits:
- Authentication endpoints: 10 requests per minute
- Property creation: 5 requests per minute
- Search operations: 60 requests per minute

---

## DATABASE IMPACT

Existing tables:
- users
- properties
- messages
- notifications

Additional fields:
- users.housing_context
- users.profile_complete
- properties.property_type

No duplicate or parallel tables are introduced.

---

## FLOW SUMMARY

Housing type selection  
→ Authentication via shared login  
→ Sanctum token issuance  
→ Context evaluation  
→ Role validation  
→ Profile enforcement  
→ Dashboard access

---

## FINAL NOTES
- General Housing and Student Housing share the same backend foundation
- Separation is achieved using role and housing context
- Authentication remains centralized
- The architecture supports extension without impacting existing flows

END OF FILE
