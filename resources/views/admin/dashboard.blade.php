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
</div>

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
