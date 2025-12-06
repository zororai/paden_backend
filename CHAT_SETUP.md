# Chat System Setup with Pusher

## Overview
Real-time chat system using Laravel, Sanctum authentication, and Pusher for WebSocket broadcasting.

## Setup Instructions

### 1. Install Pusher PHP SDK
```bash
composer require pusher/pusher-php-server
```

### 2. Configure Pusher Credentials
Update your `.env` file with your Pusher credentials:
```env
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 3. Run Database Migration
```bash
php artisan migrate
```

### 4. Regenerate Swagger Documentation
```bash
php artisan l5-swagger:generate
```

## API Endpoints

### 1. Send Message
**POST** `/api/chat/send`
- **Headers**: `Authorization: Bearer {token}`
- **Body**:
```json
{
  "receiver_id": 2,
  "message": "Hello, how are you?"
}
```
- **Response**:
```json
{
  "status": true,
  "message": "Message sent successfully",
  "data": {
    "id": 1,
    "message": "Hello, how are you?",
    "sender_id": 1,
    "receiver_id": 2,
    "created_at": "2025-12-06T10:30:00.000000Z"
  }
}
```

### 2. Get Messages with Specific User
**GET** `/api/chat/messages/{userId}`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "message": "Hello",
      "sender_id": 1,
      "receiver_id": 2,
      "is_read": true,
      "created_at": "2025-12-06T10:30:00.000000Z"
    }
  ]
}
```

### 3. Get All Conversations
**GET** `/api/chat/conversations`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
```json
{
  "status": true,
  "data": [
    {
      "user_id": 2,
      "user_name": "John Doe",
      "user_image": "http://example.com/storage/users/avatar.jpg",
      "last_message": "See you tomorrow!",
      "last_message_time": "2025-12-06T10:30:00.000000Z",
      "unread_count": 3
    }
  ]
}
```

### 4. Mark Messages as Read
**POST** `/api/chat/mark-read/{userId}`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
```json
{
  "status": true,
  "message": "Messages marked as read"
}
```

## Pusher Event Broadcasting

### Event: `MessageSent`
- **Channel**: `private-chat.{receiverId}`
- **Event Name**: `message.sent`
- **Data**:
```json
{
  "id": 1,
  "message": "Hello!",
  "sender": {
    "id": 1,
    "name": "Jane Doe",
    "image": "http://example.com/storage/users/avatar.jpg"
  },
  "created_at": "2025-12-06T10:30:00.000000Z"
}
```

## Frontend Integration Example (JavaScript)

```javascript
// Initialize Pusher
const pusher = new Pusher('YOUR_PUSHER_APP_KEY', {
  cluster: 'mt1',
  encrypted: true,
  authEndpoint: '/broadcasting/auth',
  auth: {
    headers: {
      'Authorization': 'Bearer ' + userToken
    }
  }
});

// Subscribe to private chat channel
const channel = pusher.subscribe('private-chat.' + currentUserId);

// Listen for incoming messages
channel.bind('message.sent', function(data) {
  console.log('New message received:', data);
  // Update your UI with the new message
  displayMessage(data);
});

// Send a message
async function sendMessage(receiverId, message) {
  const response = await fetch('/api/chat/send', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer ' + userToken
    },
    body: JSON.stringify({
      receiver_id: receiverId,
      message: message
    })
  });
  
  const result = await response.json();
  return result;
}
```

## Testing with cURL

### 1. Send a message:
```bash
curl -X POST 'http://paden2.test/api/chat/send' \
  -H 'Authorization: Bearer YOUR_TOKEN' \
  -H 'Content-Type: application/json' \
  -d '{
    "receiver_id": 2,
    "message": "Hello!"
  }'
```

### 2. Get messages:
```bash
curl -X GET 'http://paden2.test/api/chat/messages/2' \
  -H 'Authorization: Bearer YOUR_TOKEN'
```

### 3. Get conversations:
```bash
curl -X GET 'http://paden2.test/api/chat/conversations' \
  -H 'Authorization: Bearer YOUR_TOKEN'
```

## Security Features
- ✅ Authentication required (Sanctum)
- ✅ Private channels (users can only listen to their own chat channel)
- ✅ Message validation
- ✅ Prevention of self-messaging
- ✅ Automatic message read tracking

## Database Schema

### `messages` table:
- `id` - Primary key
- `sender_id` - Foreign key to users table
- `receiver_id` - Foreign key to users table
- `message` - Text content
- `is_read` - Boolean flag
- `created_at` - Timestamp
- `updated_at` - Timestamp

## Notes
- Messages are automatically broadcast to the receiver via Pusher
- Messages are marked as read when the receiver fetches them
- The system tracks unread message counts
- All timestamps are in ISO 8601 format
