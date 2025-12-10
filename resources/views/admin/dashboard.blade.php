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
    <div class="stat-card">
        <div class="stat-title">Total Users</div>
        <div class="stat-value">{{ \App\Models\User::count() }}</div>
        <div class="stat-subtitle">All registered users</div>
        <div class="stat-icon">👥</div>
    </div>

    <div class="stat-card green">
        <div class="stat-title">Verified Students</div>
        <div class="stat-value">{{ \App\Models\User::where('type', 'student')->whereNotNull('email_verified_at')->count() }}</div>
        <div class="stat-subtitle">Out of {{ \App\Models\User::where('type', 'student')->count() }} students</div>
        <div class="stat-icon" style="background: rgba(255,255,255,0.2);">🎓</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Verified Landlords</div>
        <div class="stat-value">{{ \App\Models\User::whereIn('type', ['landlord', 'agent'])->whereNotNull('email_verified_at')->count() }}</div>
        <div class="stat-subtitle">Out of {{ \App\Models\User::whereIn('type', ['landlord', 'agent'])->count() }} landlords</div>
        <div class="stat-icon">🏠</div>
    </div>

    <div class="stat-card payment-dropdown-card" style="position: relative; cursor: pointer;" onclick="togglePaymentDropdown()">
        <div class="stat-title">💰 Payments</div>
        <div class="stat-value">{{ \App\Models\regMoney::count() + \App\Models\Directions::count() }}</div>
        <div class="stat-subtitle">Total transactions</div>
        <div class="stat-icon">💳</div>
        <div class="payment-dropdown" id="paymentDropdown" style="display: none;">
            <a href="{{ route('admin.regPayments') }}" class="payment-dropdown-item">
                <span>📝</span>
                <div>
                    <div style="font-weight: 600;">Reg Payments</div>
                    <div style="font-size: 12px; color: #6b7280;">{{ \App\Models\regMoney::count() }} transactions</div>
                </div>
            </a>
            <a href="{{ route('admin.directionPayments') }}" class="payment-dropdown-item">
                <span>🧭</span>
                <div>
                    <div style="font-weight: 600;">Direction Payments</div>
                    <div style="font-size: 12px; color: #6b7280;">{{ \App\Models\Directions::count() }} transactions</div>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
    .payment-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        margin-top: 10px;
        overflow: hidden;
        z-index: 1000;
    }

    .payment-dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px 20px;
        text-decoration: none;
        color: #1f2937;
        transition: background 0.2s;
        border-bottom: 1px solid #f3f4f6;
    }

    .payment-dropdown-item:last-child {
        border-bottom: none;
    }

    .payment-dropdown-item:hover {
        background: #f9fafb;
    }

    .payment-dropdown-item span {
        font-size: 24px;
    }

    .payment-dropdown-card:hover {
        transform: translateY(-2px);
    }
</style>

<script>
    function togglePaymentDropdown() {
        const dropdown = document.getElementById('paymentDropdown');
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const card = document.querySelector('.payment-dropdown-card');
        const dropdown = document.getElementById('paymentDropdown');
        if (dropdown && !card.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>

<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <div class="card-title">Verified User Registration Growth</div>
        <div style="display: flex; gap: 10px;">
            <button class="period-btn active" data-period="daily">Daily</button>
            <button class="period-btn" data-period="weekly">Weekly</button>
            <button class="period-btn" data-period="monthly">Monthly</button>
        </div>
    </div>
    <div style="padding: 20px;">
        <canvas id="userChart" style="max-height: 400px;"></canvas>
    </div>
</div>

<style>
    .period-btn {
        padding: 8px 16px;
        border: 1px solid #e5e7eb;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
    }

    .period-btn:hover {
        background: #f9fafb;
    }

    .period-btn.active {
        background: #10b981;
        color: white;
        border-color: #10b981;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let chart = null;

    function loadChartData(period) {
        fetch('/admin/chart-data?period=' + period)
            .then(response => response.json())
            .then(data => {
                const labels = data.map(item => item.label);
                const studentsData = data.map(item => item.students);
                const landlordsData = data.map(item => item.landlords);

                if (chart) {
                    chart.destroy();
                }

                const ctx = document.getElementById('userChart').getContext('2d');
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Students',
                                data: studentsData,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Landlords',
                                data: landlordsData,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            },
                            title: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            });
    }

    // Load initial data
    loadChartData('daily');

    // Period button handlers
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            loadChartData(this.dataset.period);
        });
    });
</script>
@endsection
