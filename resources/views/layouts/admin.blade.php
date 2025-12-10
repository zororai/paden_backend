<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Admin Panel') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: white;
            padding: 30px 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 40px;
            font-size: 20px;
            font-weight: 600;
        }

        .logo-icon {
            width: 35px;
            height: 35px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .nav-section {
            margin-bottom: 30px;
        }

        .nav-title {
            color: #9ca3af;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: #6b7280;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }

        .nav-item:hover {
            background: #f9fafb;
            color: #111827;
        }

        .nav-item.active {
            background: #10b981;
            color: white;
        }

        .main-content {
            flex: 1;
            padding: 30px 40px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header-left h1 {
            font-size: 32px;
            margin-bottom: 5px;
        }

        .header-left p {
            color: #6b7280;
        }

        .header-right {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #10b981;
            color: white;
        }

        .btn-primary:hover {
            background: #059669;
        }

        .btn-secondary {
            background: white;
            border: 1px solid #e5e7eb;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: relative;
        }

        .stat-card.green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .stat-title {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .stat-card.green .stat-title {
            color: rgba(255,255,255,0.8);
        }

        .stat-value {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-subtitle {
            font-size: 13px;
            color: #9ca3af;
        }

        .stat-card.green .stat-subtitle {
            color: rgba(255,255,255,0.7);
        }

        .stat-icon {
            position: absolute;
            top: 25px;
            right: 25px;
            width: 40px;
            height: 40px;
            background: #f3f4f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            margin-right: 10px;
        }

        .logout-btn {
            background: #ef4444;
            color: white;
        }

        .logout-btn:hover {
            background: #dc2626;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            cursor: pointer;
        }

        .dropdown-menu {
            display: none;
            padding-left: 35px;
            margin-top: 5px;
        }

        .dropdown.open .dropdown-menu {
            display: block;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            color: #6b7280;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.3s;
            font-size: 14px;
        }

        .dropdown-item:hover {
            background: #f9fafb;
            color: #111827;
        }

        .dropdown-toggle::after {
            content: '▼';
            font-size: 10px;
            transition: transform 0.3s;
        }

        .dropdown.open .dropdown-toggle::after {
            transform: rotate(180deg);
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo">
            <div class="logo-icon">D</div>
            <span>Paden</span>
        </div>

        <nav>
            <div class="nav-section">
                <div class="nav-title">Menu</div>
                <div class="dropdown">
                    <div class="nav-item dropdown-toggle {{ request()->routeIs('dashboard') || request()->routeIs('admin.regPaymentAnalytics') || request()->routeIs('admin.directionPaymentAnalytics') || request()->routeIs('admin.universityAnalytics') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                        <span>📊 Dashboard</span>
                    </div>
                    <div class="dropdown-menu">
                        <a href="{{ route('dashboard') }}" class="dropdown-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <span>📈</span> Overview
                        </a>
                        <a href="{{ route('admin.regPaymentAnalytics') }}" class="dropdown-item {{ request()->routeIs('admin.regPaymentAnalytics') ? 'active' : '' }}">
                            <span>📝</span> Reg Payment
                        </a>
                        <a href="{{ route('admin.directionPaymentAnalytics') }}" class="dropdown-item {{ request()->routeIs('admin.directionPaymentAnalytics') ? 'active' : '' }}">
                            <span>🧭</span> Direction Payment
                        </a>
                        <a href="{{ route('admin.universityAnalytics') }}" class="dropdown-item {{ request()->routeIs('admin.universityAnalytics') ? 'active' : '' }}">
                            <span>🏫</span> University
                        </a>
                    </div>
                </div>
                <div class="dropdown">
                    <div class="nav-item dropdown-toggle {{ request()->routeIs('admin.users') || request()->routeIs('admin.landlords') || request()->routeIs('admin.students') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                        <span>👥 Users</span>
                    </div>
                    <div class="dropdown-menu">
                        <a href="{{ route('admin.users') }}" class="dropdown-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                            <span>👤</span> All Users
                        </a>
                        <a href="{{ route('admin.landlords') }}" class="dropdown-item {{ request()->routeIs('admin.landlords') ? 'active' : '' }}">
                            <span>🏠</span> Landlords
                        </a>
                        <a href="{{ route('admin.students') }}" class="dropdown-item {{ request()->routeIs('admin.students') ? 'active' : '' }}">
                            <span>🎓</span> Students
                        </a>
                    </div>
                </div>
                <div class="dropdown">
                    <div class="nav-item dropdown-toggle {{ request()->routeIs('admin.properties') || request()->routeIs('admin.universities') || request()->routeIs('admin.reviews') || request()->routeIs('admin.likes') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                        <span>🏘️ Properties</span>
                    </div>
                    <div class="dropdown-menu">
                        <a href="{{ route('admin.properties') }}" class="dropdown-item {{ request()->routeIs('admin.properties') ? 'active' : '' }}">
                            <span>🏠</span> Properties
                        </a>
                        <a href="{{ route('admin.universities') }}" class="dropdown-item {{ request()->routeIs('admin.universities') ? 'active' : '' }}">
                            <span>🏫</span> University
                        </a>
                        <div style="padding: 8px 20px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 8px;">Property Valuation</div>
                        <a href="{{ route('admin.reviews') }}" class="dropdown-item {{ request()->routeIs('admin.reviews') ? 'active' : '' }}">
                            <span>⭐</span> Reviews
                        </a>
                        <a href="{{ route('admin.likes') }}" class="dropdown-item {{ request()->routeIs('admin.likes') ? 'active' : '' }}">
                            <span>❤️</span> Likes
                        </a>
                    </div>
                </div>
                <div class="dropdown">
                    <div class="nav-item dropdown-toggle {{ request()->routeIs('admin.regPayments') || request()->routeIs('admin.directionPayments') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                        <span>💰 Payments</span>
                    </div>
                    <div class="dropdown-menu">
                        <a href="{{ route('admin.regPayments') }}" class="dropdown-item {{ request()->routeIs('admin.regPayments') ? 'active' : '' }}">
                            <span>📝</span> Reg Payment
                        </a>
                        <a href="{{ route('admin.directionPayments') }}" class="dropdown-item {{ request()->routeIs('admin.directionPayments') ? 'active' : '' }}">
                            <span>🧭</span> Direction Payment
                        </a>
                    </div>
                </div>
                <a href="#" class="nav-item">
                    <span>📅</span> Calendar
                </a>
                <a href="#" class="nav-item">
                    <span>📈</span> Analytics
                </a>
                <a href="#" class="nav-item">
                    <span>👥</span> Team
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-title">General</div>
                <a href="#" class="nav-item">
                    <span>⚙️</span> Setting
                </a>
                <a href="#" class="nav-item">
                    <span>❓</span> Help
                </a>
                <form method="POST" action="{{ route('admin.logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="nav-item" style="width: 100%; background: none; border: none; cursor: pointer; text-align: left;">
                        <span>🚪</span> Logout
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <main class="main-content">
        @yield('content')
    </main>

    <script>
        function toggleDropdown(element) {
            const dropdown = element.parentElement;
            dropdown.classList.toggle('open');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('open'));
            }
        });
    </script>
</body>
</html>
