@extends('layouts.admin')

@section('content')
<div class="header">
    <div class="header-left">
        <h1>Students</h1>
        <p>Manage all student accounts</p>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <div class="card-title">All Students ({{ $students->count() }})</div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Name</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Email</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Phone</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">University</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Status</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Joined</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 15px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="user-avatar">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
                            <div>
                                <div style="font-weight: 500;">{{ $student->name }} {{ $student->surname }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 15px; color: #6b7280;">{{ $student->email }}</td>
                    <td style="padding: 15px; color: #6b7280;">{{ $student->phone ?? 'N/A' }}</td>
                    <td style="padding: 15px; color: #6b7280;">{{ $student->university ?? 'N/A' }}</td>
                    <td style="padding: 15px;">
                        @if($student->email_verified_at)
                            <span style="background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                                ✓ Verified
                            </span>
                        @else
                            <span style="background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;">
                                ✗ Not Verified
                            </span>
                        @endif
                    </td>
                    <td style="padding: 15px; color: #6b7280;">{{ $student->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 40px; text-align: center; color: #9ca3af;">
                        No students found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
