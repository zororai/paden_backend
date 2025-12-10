@extends('layouts.admin')

@section('content')
<div class="header">
    <div class="header-left">
        <h1>Property Likes</h1>
        <p>Manage all property likes</p>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <div class="card-title">All Likes ({{ $likes->count() }})</div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">ID</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">User</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Property</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Property Type</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Location</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Price</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Date Liked</th>
                </tr>
            </thead>
            <tbody>
                @forelse($likes as $like)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 15px; font-weight: 500;">#{{ $like->id }}</td>
                    <td style="padding: 15px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="user-avatar">{{ strtoupper(substr($like->user->name ?? 'U', 0, 1)) }}</div>
                            <div>
                                <div style="font-weight: 500;">{{ $like->user->name ?? 'N/A' }}</div>
                                <div style="font-size: 12px; color: #6b7280;">{{ $like->user->email ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 15px;">
                        <div style="font-weight: 500;">{{ Str::limit($like->property->title ?? 'Property #' . $like->properties_id, 40) }}</div>
                    </td>
                    <td style="padding: 15px;">
                        <span style="background: #e0e7ff; color: #4338ca; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                            {{ $like->property->type ?? 'N/A' }}
                        </span>
                    </td>
                    <td style="padding: 15px; color: #6b7280;">
                        <div>{{ $like->property->city ?? 'N/A' }}</div>
                        <div style="font-size: 12px;">{{ $like->property->state ?? '' }}</div>
                    </td>
                    <td style="padding: 15px;">
                        @if($like->property && $like->property->price)
                            <span style="background: #d1fae5; color: #065f46; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                                ${{ number_format($like->property->price, 2) }}
                            </span>
                        @else
                            <span style="color: #9ca3af;">N/A</span>
                        @endif
                    </td>
                    <td style="padding: 15px; color: #6b7280;">{{ $like->created_at->format('M d, Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 40px; text-align: center; color: #9ca3af;">
                        No likes found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
