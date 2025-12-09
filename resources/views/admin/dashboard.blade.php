@extends('layouts.admin')

@section('content')
<div class="header">
    <div class="header-left">
        <h1>Dashboard</h1>
        <p>Plan, prioritize, and accomplish your tasks with ease.</p>
    </div>
    <div class="header-right">
        <button class="btn btn-primary">+ Add Project</button>
        <button class="btn btn-secondary">Import Data</button>
        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div>
                <div style="font-weight: 600; font-size: 14px;">{{ Auth::user()->name }}</div>
                <div style="font-size: 12px; color: #6b7280;">{{ Auth::user()->email }}</div>
            </div>
        </div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card green">
        <div class="stat-title">Total Projects</div>
        <div class="stat-value">{{ \App\Models\Properties::count() }}</div>
        <div class="stat-subtitle">📈 15% increase from last month</div>
        <div class="stat-icon" style="background: rgba(255,255,255,0.2);">📊</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Total Users</div>
        <div class="stat-value">{{ \App\Models\User::count() }}</div>
        <div class="stat-subtitle">📈 20% increase from last month</div>
        <div class="stat-icon">👥</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Admin Users</div>
        <div class="stat-value">{{ \App\Models\User::where('role', 'admin')->count() }}</div>
        <div class="stat-subtitle">Of total users</div>
        <div class="stat-icon">👤</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Verified Users</div>
        <div class="stat-value">{{ \App\Models\User::whereNotNull('email_verified_at')->count() }}</div>
        <div class="stat-subtitle">Email verified</div>
        <div class="stat-icon">✅</div>
    </div>
</div>

<div class="content-grid">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Recent Activities</div>
        </div>
        <div style="color: #6b7280; text-align: center; padding: 40px 0;">
            📊 Activity chart would go here
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Team Collaboration</div>
            <button class="btn btn-secondary" style="padding: 5px 15px; font-size: 13px;">+ Add Member</button>
        </div>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            @foreach(\App\Models\User::where('role', 'admin')->limit(4)->get() as $admin)
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="user-avatar">{{ strtoupper(substr($admin->name, 0, 1)) }}</div>
                    <div>
                        <div style="font-weight: 500; font-size: 14px;">{{ $admin->name }}</div>
                        <div style="font-size: 12px; color: #9ca3af;">{{ $admin->email }}</div>
                    </div>
                </div>
                <span style="font-size: 12px; color: #10b981; background: #d1fae5; padding: 4px 10px; border-radius: 12px;">Active</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
