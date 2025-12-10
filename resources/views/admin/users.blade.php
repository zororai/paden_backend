@extends('layouts.admin')

@section('content')
<div class="header">
    <div class="header-left">
        <h1>User Management</h1>
        <p>Manage all users in the system</p>
    </div>
    <button onclick="toggleForm()" style="background: #10b981; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
        <span style="font-size: 18px;">+</span> Add New User
    </button>
</div>

@if(session('success'))
<div style="background: #d1fae5; border: 1px solid #10b981; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
    {{ session('success') }}
</div>
@endif

<!-- Add User Form -->
<div id="addUserForm" style="display: none; margin-bottom: 20px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Create New User</div>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}" style="padding: 20px;">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">First Name *</label>
                    <input type="text" name="name" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" value="{{ old('name') }}">
                    @error('name')
                        <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Last Name *</label>
                    <input type="text" name="surname" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" value="{{ old('surname') }}">
                    @error('surname')
                        <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Email *</label>
                    <input type="email" name="email" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" value="{{ old('email') }}">
                    @error('email')
                        <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Phone</label>
                    <input type="text" name="phone" style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" value="{{ old('phone') }}">
                    @error('phone')
                        <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">Password *</label>
                    <input type="password" name="password" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                    @error('password')
                        <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">User Type *</label>
                    <select name="type" required style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        <option value="">Select Type</option>
                        <option value="student" {{ old('type') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="landlord" {{ old('type') == 'landlord' ? 'selected' : '' }}>Landlord</option>
                        <option value="agent" {{ old('type') == 'agent' ? 'selected' : '' }}>Agent</option>
                    </select>
                    @error('type')
                        <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="toggleForm()" style="background: #e5e7eb; color: #374151; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit" style="background: #10b981; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <div class="card-title">All Users ({{ $users->count() }})</div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">ID</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Name</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Email</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Phone</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Type</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Role</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Verified</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Joined</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 15px; font-weight: 500;">#{{ $user->id }}</td>
                    <td style="padding: 15px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            <div>
                                <div style="font-weight: 500;">{{ $user->name }} {{ $user->surname }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 15px; color: #6b7280;">{{ $user->email }}</td>
                    <td style="padding: 15px; color: #6b7280;">{{ $user->phone ?? 'N/A' }}</td>
                    <td style="padding: 15px;">
                        @if($user->type == 'student')
                            <span style="background: #dbeafe; color: #1e40af; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                                🎓 Student
                            </span>
                        @elseif($user->type == 'landlord')
                            <span style="background: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                                🏠 Landlord
                            </span>
                        @elseif($user->type == 'agent')
                            <span style="background: #e0e7ff; color: #4338ca; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                                💼 Agent
                            </span>
                        @else
                            <span style="background: #f3f4f6; color: #6b7280; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                                {{ ucfirst($user->type) }}
                            </span>
                        @endif
                    </td>
                    <td style="padding: 15px;">
                        @if($user->role == 'admin')
                            <span style="background: #fce7f3; color: #9f1239; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                                👑 Admin
                            </span>
                        @else
                            <span style="background: #f3f4f6; color: #6b7280; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                                User
                            </span>
                        @endif
                    </td>
                    <td style="padding: 15px;">
                        @if($user->email_verified_at)
                            <span style="background: #d1fae5; color: #065f46; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                                ✓ Verified
                            </span>
                        @else
                            <span style="background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                                ✗ Not Verified
                            </span>
                        @endif
                    </td>
                    <td style="padding: 15px; color: #6b7280;">{{ $user->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding: 40px; text-align: center; color: #9ca3af;">
                        No users found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleForm() {
    const form = document.getElementById('addUserForm');
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

// Show form if there are validation errors
@if($errors->any())
    document.getElementById('addUserForm').style.display = 'block';
@endif
</script>
@endsection
