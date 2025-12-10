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

@if(session('error'))
<div style="background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
    {{ session('error') }}
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

            <div style="margin-bottom: 20px; padding: 20px; border: 2px solid #e5e7eb; border-radius: 8px; background: #f9fafb;">
                <div style="margin-bottom: 15px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="admin_access" value="1" style="width: 18px; height: 18px; cursor: pointer;" {{ old('admin_access') ? 'checked' : '' }}>
                        <span style="font-weight: 600; color: #374151;">Grant Admin Panel Access</span>
                    </label>
                    <p style="margin: 8px 0 0 28px; font-size: 13px; color: #6b7280;">Allow this user to access the admin dashboard</p>
                </div>

                <div style="border-top: 1px solid #e5e7eb; padding-top: 15px; margin-top: 15px;">
                    <div style="font-weight: 600; color: #374151; margin-bottom: 12px;">Select Permissions (check to grant access):</div>

                    <!-- Dashboard & Analytics -->
                    <div style="margin-bottom: 16px; padding: 12px; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">
                        <div style="font-size: 14px; color: #374151; font-weight: 600; margin-bottom: 10px;">📊 Dashboard & Analytics</div>
                        <div style="padding-left: 20px; display: grid; gap: 6px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="dashboard.overview" style="width: 14px; height: 14px; cursor: pointer;" {{ in_array('dashboard.overview', old('permissions', [])) ? 'checked' : '' }}>
                                <span style="font-size: 13px; color: #6b7280;">📈 Overview</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="dashboard.reg_payment" style="width: 14px; height: 14px; cursor: pointer;" {{ in_array('dashboard.reg_payment', old('permissions', [])) ? 'checked' : '' }}>
                                <span style="font-size: 13px; color: #6b7280;">📝 Reg Payment Analytics</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="dashboard.direction_payment" style="width: 14px; height: 14px; cursor: pointer;" {{ in_array('dashboard.direction_payment', old('permissions', [])) ? 'checked' : '' }}>
                                <span style="font-size: 13px; color: #6b7280;">🧭 Direction Payment Analytics</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="dashboard.university" style="width: 14px; height: 14px; cursor: pointer;" {{ in_array('dashboard.university', old('permissions', [])) ? 'checked' : '' }}>
                                <span style="font-size: 13px; color: #6b7280;">🏫 University Analytics</span>
                            </label>
                        </div>
                    </div>

                    <!-- User Management -->
                    <div style="margin-bottom: 16px; padding: 12px; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">
                        <div style="font-size: 14px; color: #374151; font-weight: 600; margin-bottom: 10px;">👥 User Management</div>
                        <div style="padding-left: 20px; display: grid; gap: 6px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="users.all" style="width: 14px; height: 14px; cursor: pointer;" {{ in_array('users.all', old('permissions', [])) ? 'checked' : '' }}>
                                <span style="font-size: 13px; color: #6b7280;">👥 All Users</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="users.landlords" style="width: 14px; height: 14px; cursor: pointer;" {{ in_array('users.landlords', old('permissions', [])) ? 'checked' : '' }}>
                                <span style="font-size: 13px; color: #6b7280;">🏠 Landlords</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="users.students" style="width: 14px; height: 14px; cursor: pointer;" {{ in_array('users.students', old('permissions', [])) ? 'checked' : '' }}>
                                <span style="font-size: 13px; color: #6b7280;">🎓 Students</span>
                            </label>
                        </div>
                    </div>                    <!-- Properties -->
                    <div style="margin-bottom: 16px; padding: 12px; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">
                        <div style="font-size: 14px; color: #374151; font-weight: 600; margin-bottom: 10px;">🏘️ Properties</div>
                        <div style="padding-left: 20px; display: grid; gap: 6px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="properties.list" style="width: 14px; height: 14px; cursor: pointer;" {{ in_array('properties.list', old('permissions', [])) ? 'checked' : '' }}>
                                <span style="font-size: 13px; color: #6b7280;">🏠 Properties List</span>
                            </label>
                        </div>
                    </div>

                    <!-- Universities -->
                    <div style="margin-bottom: 16px; padding: 12px; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">
                        <div style="font-size: 14px; color: #374151; font-weight: 600; margin-bottom: 10px;">🏫 Universities</div>
                        <div style="padding-left: 20px; display: grid; gap: 6px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="universities.list" style="width: 14px; height: 14px; cursor: pointer;" {{ in_array('universities.list', old('permissions', [])) ? 'checked' : '' }}>
                                <span style="font-size: 13px; color: #6b7280;">🏫 Universities List</span>
                            </label>
                        </div>
                    </div>

                    <!-- Reviews & Likes -->
                    <div style="margin-bottom: 16px; padding: 12px; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">
                        <div style="font-size: 14px; color: #374151; font-weight: 600; margin-bottom: 10px;">⭐ Reviews & Likes</div>
                        <div style="padding-left: 20px; display: grid; gap: 6px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="reviews.list" style="width: 14px; height: 14px; cursor: pointer;" {{ in_array('reviews.list', old('permissions', [])) ? 'checked' : '' }}>
                                <span style="font-size: 13px; color: #6b7280;">⭐ Reviews</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="reviews.likes" style="width: 14px; height: 14px; cursor: pointer;" {{ in_array('reviews.likes', old('permissions', [])) ? 'checked' : '' }}>
                                <span style="font-size: 13px; color: #6b7280;">❤️ Likes</span>
                            </label>
                        </div>
                    </div>

                    <!-- Payments -->
                    <div style="margin-bottom: 16px; padding: 12px; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">
                        <div style="font-size: 14px; color: #374151; font-weight: 600; margin-bottom: 10px;">💰 Payments</div>
                        <div style="padding-left: 20px; display: grid; gap: 6px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="payments.reg" style="width: 14px; height: 14px; cursor: pointer;" {{ in_array('payments.reg', old('permissions', [])) ? 'checked' : '' }}>
                                <span style="font-size: 13px; color: #6b7280;">📝 Reg Payment</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="payments.direction" style="width: 14px; height: 14px; cursor: pointer;" {{ in_array('payments.direction', old('permissions', [])) ? 'checked' : '' }}>
                                <span style="font-size: 13px; color: #6b7280;">🧭 Direction Payment</span>
                            </label>
                        </div>
                    </div>
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
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Admin Access</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Joined</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Actions</th>
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
                    <td style="padding: 15px;">
                        @if($user->role == 'admin')
                            <span style="background: #fce7f3; color: #9f1239; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                                👑 Full Access
                            </span>
                        @elseif($user->admin_access)
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <div style="position: relative; display: inline-block;">
                                    <button onclick="togglePermissionDropdown({{ $user->id }})" style="background: #d1fae5; color: #065f46; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600; cursor: pointer; border: 1px solid #10b981; display: flex; align-items: center; gap: 6px;">
                                        ✓ Granted ({{ count($user->permissions ?? []) }})
                                        <span style="font-size: 10px;">▼</span>
                                    </button>
                                    <div id="permDropdown{{ $user->id }}" style="display: none; position: absolute; top: 100%; left: 0; background: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 12px; min-width: 250px; z-index: 100; margin-top: 5px; max-height: 400px; overflow-y: auto;">
                                        @if(!empty($user->permissions) && count($user->permissions) > 0)
                                            <div style="font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px;">Permissions:</div>
                                            @foreach($user->permissions as $permission)
                                                <div style="margin-bottom: 6px;">
                                                    @if(str_starts_with($permission, 'dashboard.'))
                                                        @if($permission == 'dashboard.overview')
                                                            <div style="padding: 6px 8px; background: #fafafa; border-left: 3px solid #10b981; font-size: 11px; color: #374151;">
                                                                📈 Overview
                                                            </div>
                                                        @elseif($permission == 'dashboard.reg_payment')
                                                            <div style="padding: 6px 8px; background: #fafafa; border-left: 3px solid #10b981; font-size: 11px; color: #374151;">
                                                                📝 Reg Payment Analytics
                                                            </div>
                                                        @elseif($permission == 'dashboard.direction_payment')
                                                            <div style="padding: 6px 8px; background: #fafafa; border-left: 3px solid #10b981; font-size: 11px; color: #374151;">
                                                                🧭 Direction Payment Analytics
                                                            </div>
                                                        @elseif($permission == 'dashboard.university')
                                                            <div style="padding: 6px 8px; background: #fafafa; border-left: 3px solid #10b981; font-size: 11px; color: #374151;">
                                                                🏫 University Analytics
                                                            </div>
                                                        @endif
                                                    @elseif($permission == 'users')
                                                        <div style="padding: 6px 8px; background: #f3f4f6; border-radius: 6px; font-size: 12px; color: #374151; font-weight: 600;">
                                                            👥 User Management
                                                        </div>
                                                        <div style="padding-left: 20px; margin-top: 4px;">
                                                            <div style="padding: 4px 8px; background: #fafafa; border-left: 2px solid #3b82f6; margin-bottom: 2px; font-size: 11px; color: #6b7280;">
                                                                👤 All Users
                                                            </div>
                                                            <div style="padding: 4px 8px; background: #fafafa; border-left: 2px solid #3b82f6; margin-bottom: 2px; font-size: 11px; color: #6b7280;">
                                                                🏠 Landlords
                                                            </div>
                                                            <div style="padding: 4px 8px; background: #fafafa; border-left: 2px solid #3b82f6; font-size: 11px; color: #6b7280;">
                                                                🎓 Students
                                                            </div>
                                                        </div>
                                                    @elseif($permission == 'properties')
                                                        <div style="padding: 6px 8px; background: #f3f4f6; border-radius: 6px; font-size: 12px; color: #374151; font-weight: 600;">
                                                            🏘️ Properties
                                                        </div>
                                                        <div style="padding-left: 20px; margin-top: 4px;">
                                                            <div style="padding: 4px 8px; background: #fafafa; border-left: 2px solid #f59e0b; font-size: 11px; color: #6b7280;">
                                                                🏠 Properties List
                                                            </div>
                                                        </div>
                                                    @elseif($permission == 'universities')
                                                        <div style="padding: 6px 8px; background: #f3f4f6; border-radius: 6px; font-size: 12px; color: #374151; font-weight: 600;">
                                                            🏫 Universities
                                                        </div>
                                                        <div style="padding-left: 20px; margin-top: 4px;">
                                                            <div style="padding: 4px 8px; background: #fafafa; border-left: 2px solid #8b5cf6; font-size: 11px; color: #6b7280;">
                                                                🏫 Universities List
                                                            </div>
                                                        </div>
                                                    @elseif($permission == 'reviews')
                                                        <div style="padding: 6px 8px; background: #f3f4f6; border-radius: 6px; font-size: 12px; color: #374151; font-weight: 600;">
                                                            ⭐ Reviews & Likes
                                                        </div>
                                                        <div style="padding-left: 20px; margin-top: 4px;">
                                                            <div style="padding: 4px 8px; background: #fafafa; border-left: 2px solid #ec4899; margin-bottom: 2px; font-size: 11px; color: #6b7280;">
                                                                ⭐ Reviews
                                                            </div>
                                                            <div style="padding: 4px 8px; background: #fafafa; border-left: 2px solid #ec4899; font-size: 11px; color: #6b7280;">
                                                                ❤️ Likes
                                                            </div>
                                                        </div>
                                                    @elseif($permission == 'payments')
                                                        <div style="padding: 6px 8px; background: #f3f4f6; border-radius: 6px; font-size: 12px; color: #374151; font-weight: 600;">
                                                            💰 Payments
                                                        </div>
                                                        <div style="padding-left: 20px; margin-top: 4px;">
                                                            <div style="padding: 4px 8px; background: #fafafa; border-left: 2px solid #14b8a6; margin-bottom: 2px; font-size: 11px; color: #6b7280;">
                                                                📝 Reg Payment
                                                            </div>
                                                            <div style="padding: 4px 8px; background: #fafafa; border-left: 2px solid #14b8a6; font-size: 11px; color: #6b7280;">
                                                                🧭 Direction Payment
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div style="padding: 6px 8px; background: #f3f4f6; border-radius: 6px; font-size: 12px; color: #374151;">
                                                            {{ ucfirst($permission) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div style="font-size: 12px; color: #9ca3af; text-align: center; padding: 8px;">No permissions set</div>
                                        @endif
                                    </div>
                                </div>
                                <button onclick="togglePermissionsModal({{ $user->id }}, {{ json_encode($user->permissions ?? []) }})" style="background: #3b82f6; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 12px; cursor: pointer;">
                                    ⚙️ Edit
                                </button>
                            </div>
                        @else
                            <span style="background: #f3f4f6; color: #6b7280; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                                ✗ No Access
                            </span>
                        @endif
                    </td>
                    <td style="padding: 15px; color: #6b7280;">{{ $user->created_at->format('M d, Y') }}</td>
                    <td style="padding: 15px;">
                        @if($user->role !== 'admin')
                            <form method="POST" action="{{ route('admin.users.toggleAccess', $user->id) }}" style="display: inline;">
                                @csrf
                                <button type="submit" style="background: {{ $user->admin_access ? '#ef4444' : '#10b981' }}; color: white; padding: 8px 16px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                    {{ $user->admin_access ? '🔒 Revoke' : '🔓 Grant' }}
                                </button>
                            </form>
                        @else
                            <span style="color: #9ca3af; font-size: 13px;">Protected</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="padding: 40px; text-align: center; color: #9ca3af;">
                        No users found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Permissions Modal -->
<div id="permissionsModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; padding: 30px; max-width: 550px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <h2 style="margin-bottom: 20px; font-size: 20px; font-weight: 600;">Edit User Permissions</h2>

        <form id="permissionsForm" method="POST" action="">
            @csrf
            <div style="display: grid; gap: 12px; margin-bottom: 20px;">
                <!-- Dashboard & Analytics -->
                <div style="padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px;">
                    <div style="font-size: 15px; color: #374151; font-weight: 600; margin-bottom: 10px;">📊 Dashboard & Analytics</div>
                    <div style="padding-left: 20px; display: grid; gap: 6px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="permissions[]" value="dashboard.overview" style="width: 14px; height: 14px; cursor: pointer;">
                            <span style="font-size: 13px; color: #6b7280;">📈 Overview</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="permissions[]" value="dashboard.reg_payment" style="width: 14px; height: 14px; cursor: pointer;">
                            <span style="font-size: 13px; color: #6b7280;">📝 Reg Payment Analytics</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="permissions[]" value="dashboard.direction_payment" style="width: 14px; height: 14px; cursor: pointer;">
                            <span style="font-size: 13px; color: #6b7280;">🧭 Direction Payment Analytics</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="permissions[]" value="dashboard.university" style="width: 14px; height: 14px; cursor: pointer;">
                            <span style="font-size: 13px; color: #6b7280;">🏫 University Analytics</span>
                        </label>
                    </div>
                </div>

                <label style="cursor: pointer; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                        <input type="checkbox" name="permissions[]" value="users" style="width: 18px; height: 18px; cursor: pointer;">
                        <span style="font-size: 15px; color: #374151; font-weight: 600;">👥 User Management</span>
                    </div>
                    <div style="padding-left: 28px; font-size: 12px; color: #6b7280;">
                        All Users, Landlords, Students
                    </div>
                </label>

                <label style="cursor: pointer; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                        <input type="checkbox" name="permissions[]" value="properties" style="width: 18px; height: 18px; cursor: pointer;">
                        <span style="font-size: 15px; color: #374151; font-weight: 600;">🏘️ Properties</span>
                    </div>
                    <div style="padding-left: 28px; font-size: 12px; color: #6b7280;">
                        Properties List
                    </div>
                </label>

                <label style="cursor: pointer; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                        <input type="checkbox" name="permissions[]" value="universities" style="width: 18px; height: 18px; cursor: pointer;">
                        <span style="font-size: 15px; color: #374151; font-weight: 600;">🏫 Universities</span>
                    </div>
                    <div style="padding-left: 28px; font-size: 12px; color: #6b7280;">
                        Universities List
                    </div>
                </label>

                <label style="cursor: pointer; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                        <input type="checkbox" name="permissions[]" value="reviews" style="width: 18px; height: 18px; cursor: pointer;">
                        <span style="font-size: 15px; color: #374151; font-weight: 600;">⭐ Reviews & Likes</span>
                    </div>
                    <div style="padding-left: 28px; font-size: 12px; color: #6b7280;">
                        Reviews, Likes (Property Valuation section)
                    </div>
                </label>

                <label style="cursor: pointer; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                        <input type="checkbox" name="permissions[]" value="payments" style="width: 18px; height: 18px; cursor: pointer;">
                        <span style="font-size: 15px; color: #374151; font-weight: 600;">💰 Payments</span>
                    </div>
                    <div style="padding-left: 28px; font-size: 12px; color: #6b7280;">
                        Reg Payment, Direction Payment
                    </div>
                </label>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closePermissionsModal()" style="background: #e5e7eb; color: #374151; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit" style="background: #10b981; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Save Permissions
                </button>
            </div>
        </form>
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

function togglePermissionsModal(userId, currentPermissions) {
    const modal = document.getElementById('permissionsModal');
    const form = document.getElementById('permissionsForm');

    // Set form action
    form.action = `/admin/users/${userId}/permissions`;

    // Uncheck all checkboxes first
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = false);

    // Check the current permissions
    if (currentPermissions && currentPermissions.length > 0) {
        currentPermissions.forEach(permission => {
            const checkbox = form.querySelector(`input[value="${permission}"]`);
            if (checkbox) checkbox.checked = true;
        });
    }

    modal.style.display = 'flex';
}

function closePermissionsModal() {
    document.getElementById('permissionsModal').style.display = 'none';
}

function togglePermissionDropdown(userId) {
    const dropdown = document.getElementById('permDropdown' + userId);

    // Close all other dropdowns first
    document.querySelectorAll('[id^="permDropdown"]').forEach(d => {
        if (d.id !== 'permDropdown' + userId) {
            d.style.display = 'none';
        }
    });

    // Toggle current dropdown
    if (dropdown.style.display === 'none' || dropdown.style.display === '') {
        dropdown.style.display = 'block';
    } else {
        dropdown.style.display = 'none';
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick^="togglePermissionDropdown"]') &&
        !event.target.closest('[id^="permDropdown"]')) {
        document.querySelectorAll('[id^="permDropdown"]').forEach(d => {
            d.style.display = 'none';
        });
    }
});

// Show form if there are validation errors
@if($errors->any())
    document.getElementById('addUserForm').style.display = 'block';
@endif
</script>
@endsection
