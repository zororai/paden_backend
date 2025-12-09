@extends('layouts.admin')

@section('content')
<div class="header">
    <div class="header-left">
        <h1>Landlords & Agents</h1>
        <p>Manage all landlords and property agents</p>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <div class="card-title">All Landlords ({{ $landlords->count() }})</div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Name</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Email</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Phone</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Type</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">University</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Status</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Joined</th>
                </tr>
            </thead>
            <tbody>
                @forelse($landlords as $landlord)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 15px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="user-avatar">{{ strtoupper(substr($landlord->name, 0, 1)) }}</div>
                            <div>
                                <div style="font-weight: 500;">{{ $landlord->name }} {{ $landlord->surname }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 15px; color: #6b7280;">{{ $landlord->email }}</td>
                    <td style="padding: 15px; color: #6b7280;">{{ $landlord->phone ?? 'N/A' }}</td>
                    <td style="padding: 15px;">
                        <span style="background: #dbeafe; color: #1e40af; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                            {{ ucfirst($landlord->type) }}
                        </span>
                    </td>
                    <td style="padding: 15px; color: #6b7280;">{{ $landlord->university ?? 'N/A' }}</td>
                    <td style="padding: 15px;">
                        @if($landlord->email_verified_at)
                            <span style="background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                                ✓ Verified
                            </span>
                        @else
                            <span style="background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                                ✗ Not Verified
                            </span>
                        @endif
                    </td>
                    <td style="padding: 15px; color: #6b7280;">{{ $landlord->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 40px; text-align: center; color: #9ca3af;">
                        No landlords found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
