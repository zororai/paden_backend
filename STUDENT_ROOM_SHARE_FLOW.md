# Student Room Share Request Flow Documentation

Complete documentation for the Room Share Request feature in the Student Housing system.

---

## Overview

The Room Share Request feature allows students to find and connect with potential roommates at their university. Students can post room share requests for properties they're interested in, browse requests from other students at the same university, and communicate to arrange shared accommodation.

**Target Users:** Students only (Student Housing Context)

**Base URL:** `http://your-domain/api`

---

## Table of Contents

1. [Feature Overview](#feature-overview)
2. [User Flow Diagram](#user-flow-diagram)
3. [API Endpoints](#api-endpoints)
4. [Request Lifecycle](#request-lifecycle)
5. [Complete User Journey](#complete-user-journey)
6. [Frontend Implementation Guide](#frontend-implementation-guide)
7. [Database Schema](#database-schema)

---

## Feature Overview

### What is Room Share?

Room Share allows students to:
- **Post requests** to find roommates for specific properties
- **Browse requests** from other students at their university
- **View profiles** of potential roommates
- **Connect directly** via phone/email/chat
- **Manage their posts** (view, delete)

### Key Features

✅ **University-Scoped:** Only see requests from students at your university  
✅ **Property-Based:** Each request is tied to a specific property  
✅ **Preference Filters:** Specify preferred year, gender, rent sharing conditions  
✅ **Profile Viewing:** View detailed profiles of interested students  
✅ **Direct Communication:** Access to phone numbers and email for direct contact  
✅ **Status Management:** Track pending, accepted, rejected requests  

---

## User Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    STUDENT ROOM SHARE FLOW                   │
└─────────────────────────────────────────────────────────────┘

┌──────────────────┐
│  Student Login   │
└────────┬─────────┘
         │
         ▼
┌──────────────────────────────────────────────────────────────┐
│              MAIN ROOM SHARE DASHBOARD                        │
│  ┌──────────────────┐  ┌──────────────────┐                 │
│  │  My Posts        │  │  Browse Posts    │                 │
│  │  (Sent Requests) │  │  (University)    │                 │
│  └────────┬─────────┘  └────────┬─────────┘                 │
└───────────┼──────────────────────┼───────────────────────────┘
            │                      │
            │                      │
    ┌───────▼────────┐    ┌───────▼────────┐
    │  View My Posts │    │  Browse Posts  │
    │  - Edit        │    │  from Others   │
    │  - Delete      │    │  at University │
    └───────┬────────┘    └───────┬────────┘
            │                      │
            │                      ▼
            │             ┌─────────────────┐
            │             │  View Post      │
            │             │  Details        │
            │             └────────┬────────┘
            │                      │
            │                      ▼
            │             ┌─────────────────┐
            │             │  View Student   │
            │             │  Profile        │
            │             └────────┬────────┘
            │                      │
            │                      ▼
            │             ┌─────────────────┐
            │             │  Contact        │
            │             │  - Phone        │
            │             │  - Email        │
            │             │  - Chat         │
            │             └─────────────────┘
            │
            ▼
    ┌──────────────────┐
    │  Create New Post │
    │  - Select Property│
    │  - Add Message   │
    │  - Set Preferences│
    └──────────────────┘
```

---

## API Endpoints

### 1. Post a Room Share Request

**Endpoint:** `POST /api/room-share/send`

**Authentication:** Required (Bearer Token)

**Description:** Create a new room share request for a specific property. The request is visible to all students at your university.

**Request Body:**
```json
{
  "property_id": 5,
  "message": "Hi, I'm looking for a roommate. Would you like to share a room?",
  "preferred_year": "2nd Year",
  "preferred_gender": "Male",
  "rent_sharing_conditions": "50/50 split on rent and utilities"
}
```

**Fields:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| property_id | integer | Yes | ID of the property to share |
| message | string | No | Personal message (max 1000 chars) |
| preferred_year | string | No | Preferred academic year (max 50 chars) |
| preferred_gender | string | No | Preferred gender (max 20 chars) |
| rent_sharing_conditions | string | No | Rent sharing details (max 1000 chars) |

**Success Response (201):**
```json
{
  "message": "Room share request posted successfully to all students at your university.",
  "request": {
    "id": 15,
    "sender_id": 10,
    "property_id": 5,
    "university": "University of Zimbabwe",
    "message": "Hi, I'm looking for a roommate...",
    "preferred_year": "2nd Year",
    "preferred_gender": "Male",
    "rent_sharing_conditions": "50/50 split on rent and utilities",
    "status": "pending",
    "created_at": "2026-01-15T18:30:00.000000Z",
    "sender": {
      "id": 10,
      "name": "John",
      "surname": "Doe",
      "email": "john@example.com",
      "university": "University of Zimbabwe",
      "phone": "+263771234567",
      "image": "http://your-domain/storage/users/john.jpg"
    },
    "property": {
      "id": 5,
      "title": "Cozy Room near Campus",
      "price": 250,
      "location": "Avondale",
      "image": "http://your-domain/storage/properties/room.jpg"
    }
  }
}
```

**Error Responses:**

**400 - Validation Error:**
```json
{
  "errors": {
    "property_id": ["The property id field is required."]
  }
}
```

**409 - Duplicate Request:**
```json
{
  "message": "You have already posted a room share request for this property."
}
```

---

### 2. Get My Posted Requests

**Endpoint:** `GET /api/room-share/sent`

**Authentication:** Required (Bearer Token)

**Description:** Retrieve all room share requests posted by the authenticated student.

**Success Response (200):**
```json
{
  "message": "Your room share posts retrieved successfully.",
  "requests": [
    {
      "id": 15,
      "university": "University of Zimbabwe",
      "property": {
        "id": 5,
        "title": "Cozy Room near Campus",
        "price": 250,
        "location": "Avondale",
        "image": "http://your-domain/storage/properties/room.jpg"
      },
      "message": "Hi, I'm looking for a roommate...",
      "preferred_year": "2nd Year",
      "preferred_gender": "Male",
      "rent_sharing_conditions": "50/50 split on rent and utilities",
      "status": "pending",
      "created_at": "2026-01-15T18:30:00.000000Z",
      "updated_at": "2026-01-15T18:30:00.000000Z"
    }
  ]
}
```

---

### 3. Browse University Room Share Posts

**Endpoint:** `GET /api/room-share/university`

**Authentication:** Required (Bearer Token)

**Description:** Get all room share posts from other students at your university. Excludes your own posts and only shows pending requests.

**Success Response (200):**
```json
{
  "message": "University room share posts retrieved successfully.",
  "requests": [
    {
      "id": 12,
      "sender": {
        "id": 8,
        "name": "Jane",
        "surname": "Smith",
        "email": "jane@example.com",
        "university": "University of Zimbabwe",
        "phone": "+263771234568",
        "image": "http://your-domain/storage/users/jane.jpg"
      },
      "property": {
        "id": 3,
        "title": "Modern Apartment",
        "price": 300,
        "location": "Mount Pleasant",
        "image": "http://your-domain/storage/properties/apt.jpg"
      },
      "message": "Looking for a clean and quiet roommate",
      "preferred_year": "3rd Year",
      "preferred_gender": "Female",
      "rent_sharing_conditions": "Equal split, shared cooking",
      "status": "pending",
      "created_at": "2026-01-15T16:00:00.000000Z",
      "updated_at": "2026-01-15T16:00:00.000000Z"
    }
  ]
}
```

---

### 4. View Student Profile

**Endpoint:** `GET /api/room-share/student/{id}`

**Authentication:** Required (Bearer Token)

**Description:** View detailed profile of a student who posted a room share request.

**Path Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | Student user ID |

**Success Response (200):**
```json
{
  "message": "Student profile retrieved successfully.",
  "student": {
    "id": 8,
    "name": "Jane",
    "surname": "Smith",
    "email": "jane@example.com",
    "university": "University of Zimbabwe",
    "type": "student",
    "phone": "+263771234568",
    "image": "http://your-domain/storage/users/jane.jpg"
  }
}
```

**Error Response (404):**
```json
{
  "message": "Student not found."
}
```

---

### 5. Get Students at University

**Endpoint:** `GET /api/room-share/students`

**Authentication:** Required (Bearer Token)

**Description:** Get a list of all students at your university (or specified university).

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| university | string | No | Filter by university (defaults to your university) |

**Success Response (200):**
```json
{
  "message": "Students retrieved successfully.",
  "students": [
    {
      "id": 8,
      "name": "Jane",
      "surname": "Smith",
      "email": "jane@example.com",
      "university": "University of Zimbabwe",
      "type": "student",
      "phone": "+263771234568",
      "image": "http://your-domain/storage/users/jane.jpg"
    }
  ]
}
```

---

### 6. Delete Room Share Request

**Endpoint:** `DELETE /api/room-share/{id}`

**Authentication:** Required (Bearer Token)

**Description:** Delete your own room share request.

**Path Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | Request ID |

**Success Response (200):**
```json
{
  "message": "Room share request deleted successfully."
}
```

**Error Response (404):**
```json
{
  "message": "Request not found or unauthorized."
}
```

---

### 7. Accept Room Share Request (Reserved)

**Endpoint:** `PUT /api/room-share/accept/{id}`

**Authentication:** Required (Bearer Token)

**Note:** Currently not actively used in the flow. Reserved for future direct acceptance feature.

---

### 8. Reject Room Share Request (Reserved)

**Endpoint:** `PUT /api/room-share/reject/{id}`

**Authentication:** Required (Bearer Token)

**Note:** Currently not actively used in the flow. Reserved for future direct rejection feature.

---

## Request Lifecycle

### Status Flow

```
┌─────────────┐
│   PENDING   │ ← Initial status when created
└──────┬──────┘
       │
       ├──────────────┐
       │              │
       ▼              ▼
┌─────────────┐  ┌─────────────┐
│  ACCEPTED   │  │  REJECTED   │ (Reserved for future use)
└─────────────┘  └─────────────┘
       │              │
       └──────┬───────┘
              │
              ▼
       ┌─────────────┐
       │   DELETED   │
       └─────────────┘
```

### Current Implementation

In the current implementation:
- All requests start as **"pending"**
- Students browse pending requests and contact each other directly
- Communication happens outside the app (phone, email, chat)
- Students can **delete** their own requests
- Accept/Reject endpoints exist but are reserved for future features

---

## Complete User Journey

### Scenario: John wants to find a roommate

#### Step 1: John Posts a Room Share Request

```javascript
// John finds a property he likes (property_id: 5)
// He posts a room share request

const postRequest = async () => {
  const response = await fetch('http://your-domain/api/room-share/send', {
    method: 'POST',
    headers: {
      'Authorization': 'Bearer ' + token,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      property_id: 5,
      message: "Hi! I'm a 2nd year Computer Science student looking for a clean and responsible roommate. I'm quiet during study hours but enjoy socializing on weekends.",
      preferred_year: "2nd Year",
      preferred_gender: "Male",
      rent_sharing_conditions: "50/50 split on rent, utilities, and groceries. Shared cooking and cleaning schedule."
    })
  });
  
  const data = await response.json();
  console.log(data.message); // "Room share request posted successfully..."
};
```

#### Step 2: Jane Browses Room Share Posts

```javascript
// Jane is also looking for accommodation
// She browses posts from students at her university

const browsePosts = async () => {
  const response = await fetch('http://your-domain/api/room-share/university', {
    method: 'GET',
    headers: {
      'Authorization': 'Bearer ' + janeToken
    }
  });
  
  const data = await response.json();
  // Jane sees John's post along with others
  data.requests.forEach(request => {
    console.log(`${request.sender.name}: ${request.message}`);
    console.log(`Property: ${request.property.title} - $${request.property.price}`);
  });
};
```

#### Step 3: Jane Views John's Profile

```javascript
// Jane is interested in John's post
// She views his full profile

const viewProfile = async (studentId) => {
  const response = await fetch(`http://your-domain/api/room-share/student/${studentId}`, {
    method: 'GET',
    headers: {
      'Authorization': 'Bearer ' + janeToken
    }
  });
  
  const data = await response.json();
  console.log('Student:', data.student.name);
  console.log('University:', data.student.university);
  console.log('Phone:', data.student.phone);
  console.log('Email:', data.student.email);
};
```

#### Step 4: Jane Contacts John

```javascript
// Jane decides to contact John
// She can use:
// 1. Phone number to call/WhatsApp
// 2. Email to send a message
// 3. In-app chat (if implemented)

const contactStudent = (phone, email) => {
  // Option 1: Call/WhatsApp
  window.location.href = `tel:${phone}`;
  
  // Option 2: Email
  window.location.href = `mailto:${email}`;
  
  // Option 3: In-app chat
  navigateToChat(studentId);
};
```

#### Step 5: John Manages His Posts

```javascript
// John can view all his posted requests

const viewMyPosts = async () => {
  const response = await fetch('http://your-domain/api/room-share/sent', {
    method: 'GET',
    headers: {
      'Authorization': 'Bearer ' + token
    }
  });
  
  const data = await response.json();
  console.log('My posts:', data.requests);
};

// John can delete a post if he found a roommate

const deletePost = async (requestId) => {
  const response = await fetch(`http://your-domain/api/room-share/${requestId}`, {
    method: 'DELETE',
    headers: {
      'Authorization': 'Bearer ' + token
    }
  });
  
  const data = await response.json();
  console.log(data.message); // "Room share request deleted successfully."
};
```

---

## Frontend Implementation Guide

### React/React Native Example

```javascript
import React, { useState, useEffect } from 'react';

const RoomShareDashboard = () => {
  const [myPosts, setMyPosts] = useState([]);
  const [universityPosts, setUniversityPosts] = useState([]);
  const [loading, setLoading] = useState(true);
  const token = localStorage.getItem('auth_token');

  // Fetch my posts
  const fetchMyPosts = async () => {
    const response = await fetch('/api/room-share/sent', {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    const data = await response.json();
    setMyPosts(data.requests);
  };

  // Fetch university posts
  const fetchUniversityPosts = async () => {
    const response = await fetch('/api/room-share/university', {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    const data = await response.json();
    setUniversityPosts(data.requests);
  };

  // Create new post
  const createPost = async (postData) => {
    const response = await fetch('/api/room-share/send', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(postData)
    });
    
    if (response.ok) {
      alert('Post created successfully!');
      fetchMyPosts(); // Refresh
    }
  };

  // Delete post
  const deletePost = async (id) => {
    if (confirm('Are you sure you want to delete this post?')) {
      const response = await fetch(`/api/room-share/${id}`, {
        method: 'DELETE',
        headers: { 'Authorization': `Bearer ${token}` }
      });
      
      if (response.ok) {
        alert('Post deleted successfully!');
        fetchMyPosts(); // Refresh
      }
    }
  };

  // View student profile
  const viewProfile = async (studentId) => {
    const response = await fetch(`/api/room-share/student/${studentId}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    const data = await response.json();
    // Show profile modal
    showProfileModal(data.student);
  };

  useEffect(() => {
    Promise.all([fetchMyPosts(), fetchUniversityPosts()])
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="room-share-dashboard">
      <h1>Room Share</h1>
      
      {/* My Posts Section */}
      <section>
        <h2>My Posts</h2>
        {myPosts.map(post => (
          <div key={post.id} className="post-card">
            <h3>{post.property.title}</h3>
            <p>{post.message}</p>
            <p>Status: {post.status}</p>
            <button onClick={() => deletePost(post.id)}>Delete</button>
          </div>
        ))}
      </section>

      {/* University Posts Section */}
      <section>
        <h2>Find Roommates</h2>
        {universityPosts.map(post => (
          <div key={post.id} className="post-card">
            <img src={post.sender.image} alt={post.sender.name} />
            <h3>{post.sender.name} {post.sender.surname}</h3>
            <p>{post.message}</p>
            <div className="property-info">
              <h4>{post.property.title}</h4>
              <p>${post.property.price}/month - {post.property.location}</p>
            </div>
            <div className="preferences">
              <span>Year: {post.preferred_year}</span>
              <span>Gender: {post.preferred_gender}</span>
            </div>
            <button onClick={() => viewProfile(post.sender.id)}>
              View Profile
            </button>
            <button onClick={() => window.location.href = `tel:${post.sender.phone}`}>
              Call
            </button>
          </div>
        ))}
      </section>
    </div>
  );
};
```

---

## Database Schema

### `room_share_requests` Table

```sql
CREATE TABLE room_share_requests (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    sender_id BIGINT NOT NULL,
    receiver_id BIGINT NULL,
    property_id BIGINT NOT NULL,
    university VARCHAR(255) NOT NULL,
    message TEXT NULL,
    preferred_year VARCHAR(50) NULL,
    preferred_gender VARCHAR(20) NULL,
    rent_sharing_conditions TEXT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    
    INDEX idx_university (university),
    INDEX idx_status (status),
    INDEX idx_sender (sender_id),
    INDEX idx_property (property_id)
);
```

---

## Best Practices

### For Students

1. **Be Specific:** Provide detailed information about yourself and preferences
2. **Be Honest:** Accurately describe your lifestyle and expectations
3. **Respond Quickly:** Contact interested students promptly
4. **Meet in Person:** Arrange to meet potential roommates before committing
5. **Verify Property:** Visit the property together before making decisions
6. **Clean Up:** Delete your post once you've found a roommate

### For Developers

1. **University Filtering:** Always filter by university to maintain privacy
2. **Status Management:** Keep track of request status for future features
3. **Profile Privacy:** Only show necessary information to other students
4. **Rate Limiting:** Implement rate limits to prevent spam
5. **Moderation:** Consider adding reporting/flagging features
6. **Notifications:** Implement push notifications for new posts (future)

---

## Security Considerations

1. ✅ **Authentication Required:** All endpoints require valid bearer token
2. ✅ **University Scoping:** Students only see posts from their university
3. ✅ **Ownership Validation:** Users can only delete their own posts
4. ✅ **Duplicate Prevention:** One request per property per student
5. ⚠️ **Contact Information:** Phone/email exposed - ensure users consent
6. ⚠️ **Profile Privacy:** Consider adding privacy settings

---

## Future Enhancements

### Planned Features

1. **Direct Acceptance/Rejection:** Implement accept/reject workflow
2. **In-App Messaging:** Built-in chat between interested students
3. **Push Notifications:** Real-time alerts for new posts
4. **Advanced Filters:** Filter by price range, location, preferences
5. **Matching Algorithm:** AI-powered roommate matching
6. **Reviews/Ratings:** Rate roommate experiences
7. **Saved Searches:** Save search criteria for future notifications
8. **Property Tours:** Schedule property viewings together

---

## Testing with cURL

### Post a Request
```bash
curl -X POST http://your-domain/api/room-share/send \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "property_id": 5,
    "message": "Looking for a roommate!",
    "preferred_year": "2nd Year",
    "preferred_gender": "Male",
    "rent_sharing_conditions": "50/50 split"
  }'
```

### Get University Posts
```bash
curl -X GET http://your-domain/api/room-share/university \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### View Student Profile
```bash
curl -X GET http://your-domain/api/room-share/student/8 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Delete Request
```bash
curl -X DELETE http://your-domain/api/room-share/15 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Support

For issues or questions:
- Check API response error messages
- Review logs in `storage/logs/laravel.log`
- Ensure user has correct role and university set
- Verify authentication token is valid

---

**Last Updated:** January 15, 2026  
**Version:** 1.0.0  
**Context:** Student Housing Only
