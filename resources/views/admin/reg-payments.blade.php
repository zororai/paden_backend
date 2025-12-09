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
@endsection
