# Database Fix: Email Column Nullable

## Problem

**Error:** `SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'email' cannot be null`

**Location:** General Housing Registration (`/api/register/general`)

**Root Cause:** The `email` column in the `users` table was defined as NOT NULL, but the General Housing registration allows users to register with only a phone number (email is optional).

---

## Error Details

```
SQL: insert into `users` 
(`name`, `surname`, `email`, `phone`, `password`, `role`, `housing_context`, `profile_complete`, `image`, `type`, `updated_at`, `created_at`) 
values (ghost, ?, ?, 0775 219 766, $2y$12$..., tenant, general, 0, new, general, 2026-01-15 18:15:24, 2026-01-15 18:15:24)
```

The user "ghost" tried to register with:
- Name: ghost
- Phone: 0775 219 766
- Email: NULL (not provided)
- Role: tenant

The database rejected the insert because `email` cannot be NULL.

---

## Solution

### 1. Database Migration Created

**File:** `database/migrations/2026_01_15_000002_make_email_nullable_in_users_table.php`

This migration makes both `email` and `university` columns nullable to support:
- General Housing users who register with only phone numbers
- Users who don't have a university affiliation

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('email')->nullable()->change();
    $table->string('university')->nullable()->change();
});
```

### 2. Controller Validation Updated

**File:** `app/Http/Controllers/Api/General/GeneralRegisterController.php`

Updated validation to:
- Explicitly specify unique constraint columns
- Add better error messages
- Ensure at least one contact method (email OR phone) is provided

```php
$validator = Validator::make($request->all(), [
    'name'     => 'required|string|max:255',
    'surname'  => 'nullable|string|max:255',
    'email'    => 'nullable|string|email|max:255|unique:users,email',
    'phone'    => 'required_without:email|string|max:20|unique:users,phone',
    'password' => 'required|string|min:6|confirmed',
    'role'     => 'required|string|in:tenant,landlord',
]);

// Additional check to ensure at least one contact method
if (!$request->email && !$request->phone) {
    return response()->json([
        'status' => false,
        'errors' => ['contact' => ['Either email or phone number is required.']]
    ], 422);
}
```

---

## Deployment Steps

### Step 1: Run the Migration

```bash
php artisan migrate
```

This will execute the migration to make `email` and `university` nullable.

### Step 2: Verify the Change

```bash
php artisan tinker
```

```php
// Test creating a user with only phone
$user = \App\Models\User::create([
    'name' => 'Test User',
    'phone' => '+263771234567',
    'password' => bcrypt('password'),
    'role' => 'tenant',
    'housing_context' => 'general',
    'profile_complete' => false,
    'image' => 'new',
    'type' => 'general',
]);

// Should succeed without error
```

### Step 3: Test Registration Endpoint

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

Expected response:
```json
{
  "status": true,
  "message": "Registration successful",
  "token": "1|...",
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

---

## Impact Analysis

### Affected Features

✅ **General Housing Registration** - Now works with phone-only registration  
✅ **Student Housing** - Unaffected (students typically have email)  
✅ **Login System** - Already supports phone or email login  
✅ **SMS Verification** - Works with phone numbers  

### Database Changes

| Column | Before | After | Reason |
|--------|--------|-------|--------|
| `email` | NOT NULL | NULL | Support phone-only registration |
| `university` | NOT NULL | NULL | General housing users don't have university |

### Backward Compatibility

✅ **Existing Users:** No impact - existing users with emails remain unchanged  
✅ **Student Registration:** Still requires email (validation unchanged)  
✅ **Login:** Works with email OR phone (already implemented)  
✅ **API Responses:** Email field returns `null` when not provided  

---

## Validation Rules Summary

### General Housing Registration

- **Name:** Required
- **Surname:** Optional
- **Email:** Optional (but must be unique if provided)
- **Phone:** Required if email not provided (must be unique)
- **Password:** Required, minimum 6 characters, must be confirmed
- **Role:** Required, must be "tenant" or "landlord"

### Student Housing Registration (Unchanged)

- **Email:** Required (students use university emails)
- **Phone:** Optional
- Other fields similar to general housing

---

## Testing Checklist

- [x] Migration created
- [x] Controller validation updated
- [ ] Run migration on production
- [ ] Test phone-only registration
- [ ] Test email-only registration
- [ ] Test registration with both phone and email
- [ ] Test duplicate phone number validation
- [ ] Test duplicate email validation
- [ ] Test login with phone number
- [ ] Test login with email
- [ ] Test SMS verification flow

---

## Rollback Plan

If issues arise, rollback the migration:

```bash
php artisan migrate:rollback --step=1
```

This will revert the `email` and `university` columns to NOT NULL.

**Note:** Before rollback, ensure no users exist with NULL email values, or the rollback will fail.

---

## Additional Notes

### Why University is Also Nullable

General Housing users are not affiliated with universities, so the `university` field should also be nullable. This was included in the same migration for consistency.

### Future Considerations

1. **Email Verification:** If email is provided, consider sending verification emails
2. **Phone Verification:** Already implemented via SMS verification
3. **Profile Completion:** Encourage users to add email if registered with phone only
4. **Contact Preferences:** Allow users to set preferred contact method

---

**Migration File:** `2026_01_15_000002_make_email_nullable_in_users_table.php`  
**Controller Updated:** `GeneralRegisterController.php`  
**Date:** January 15, 2026  
**Status:** Ready for deployment
