@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Admin Dashboard</h2>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </div>
                <div class="card-body">
                    <h3>Welcome, {{ Auth::user()->name }}!</h3>
                    <p>You are logged in as an administrator.</p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="row mb-4">
                <!-- Total Users -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Users</h5>
                            <p class="display-4">{{ \App\Models\User::count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Admin Users -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Admin Users</h5>
                            <p class="display-4">{{ \App\Models\User::where('role', 'admin')->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Verified Users -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Verified Users</h5>
                            <p class="display-4">{{ \App\Models\User::whereNotNull('email_verified_at')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
