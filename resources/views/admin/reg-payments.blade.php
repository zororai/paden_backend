@extends('layouts.admin')

@section('content')
<div class="header">
    <div class="header-left">
        <h1>Registration Payments</h1>
        <p>Manage all registration payment transactions</p>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <div class="card-title">Payment Growth</div>
        <div style="display: flex; gap: 10px;">
            <button class="period-btn active" data-period="daily">Daily</button>
            <button class="period-btn" data-period="weekly">Weekly</button>
            <button class="period-btn" data-period="monthly">Monthly</button>
        </div>
    </div>
    <div style="padding: 20px;">
        <canvas id="paymentChart" style="width: 100%; max-height: 400px;"></canvas>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <div class="card-title">All Reg Payments ({{ $payments->count() }})</div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">ID</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">User Name</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Email</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Amount</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Reference Number</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Phone Number</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 15px; font-weight: 500;">#{{ $payment->id }}</td>
                    <td style="padding: 15px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="user-avatar">{{ strtoupper(substr($payment->user->name ?? 'U', 0, 1)) }}</div>
                            <div>
                                <div style="font-weight: 500;">{{ $payment->user->name ?? 'N/A' }} {{ $payment->user->surname ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 15px; color: #6b7280;">{{ $payment->user->email ?? 'N/A' }}</td>
                    <td style="padding: 15px;">
                        <span style="background: #d1fae5; color: #065f46; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                            ${{ number_format($payment->amount, 2) }}
                        </span>
                    </td>
                    <td style="padding: 15px; color: #6b7280; font-family: monospace;">{{ $payment->reference_number }}</td>
                    <td style="padding: 15px; color: #6b7280;">{{ $payment->phone_number }}</td>
                    <td style="padding: 15px; color: #6b7280;">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 40px; text-align: center; color: #9ca3af;">
                        No registration payments found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
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
