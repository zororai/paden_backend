@extends('layouts.admin')

@section('content')
<div class="header">
    <div class="header-left">
        <h1>Registration Payment Analytics</h1>
        <p>View payment trends and growth over time</p>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <div class="card-title">Payment Growth</div>
        <div style="display: flex; gap: 10px;">
            <button class="period-btn active" data-period="daily">Daily</button>
            <button class="period-btn" data-period="weekly">Weekly</button>
            <button class="period-btn" data-period="monthly">Monthly</button>
            <button class="period-btn" data-period="yearly">Yearly</button>
        </div>
    </div>
    <div style="padding: 20px;">
        <canvas id="paymentChart" style="width: 100%; max-height: 400px;"></canvas>
    </div>
</div>

<div class="stats-grid" style="margin-top: 30px;">
    <div class="stat-card">
        <div class="stat-title">Total Payments</div>
        <div class="stat-value">{{ \App\Models\regMoney::count() }}</div>
        <div class="stat-subtitle">All time transactions</div>
        <div class="stat-icon">📝</div>
    </div>

    <div class="stat-card green">
        <div class="stat-title">Total Revenue</div>
        <div class="stat-value">${{ number_format(\App\Models\regMoney::sum('amount'), 2) }}</div>
        <div class="stat-subtitle">All time earnings</div>
        <div class="stat-icon">💰</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">This Month</div>
        <div class="stat-value">{{ \App\Models\regMoney::whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count() }}</div>
        <div class="stat-subtitle">${{ number_format(\App\Models\regMoney::whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('amount'), 2) }} revenue</div>
        <div class="stat-icon">📊</div>
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
        fetch('/admin/reg-payment-chart-data?period=' + period)
            .then(response => response.json())
            .then(data => {
                const labels = data.map(item => item.label);
                const amounts = data.map(item => item.amount);
                const counts = data.map(item => item.count);

                if (chart) {
                    chart.destroy();
                }

                const ctx = document.getElementById('paymentChart').getContext('2d');
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Amount ($)',
                                data: amounts,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true,
                                yAxisID: 'y',
                            },
                            {
                                label: 'Transactions',
                                data: counts,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                fill: true,
                                yAxisID: 'y1',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.datasetIndex === 0) {
                                            label += '$' + context.parsed.y.toFixed(2);
                                        } else {
                                            label += context.parsed.y;
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Amount ($)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value;
                                    }
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Transactions'
                                },
                                grid: {
                                    drawOnChartArea: false,
                                },
                            },
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
