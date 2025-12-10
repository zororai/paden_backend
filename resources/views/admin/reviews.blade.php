@extends('layouts.admin')

@section('content')
<div class="header">
    <div class="header-left">
        <h1>Property Reviews</h1>
        <p>Manage all property reviews and ratings</p>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <div class="card-title">All Reviews ({{ $reviews->count() }})</div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">ID</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">User</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Property</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Rating</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Comment</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Status</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 15px; font-weight: 500;">#{{ $review->id }}</td>
                    <td style="padding: 15px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="user-avatar">{{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}</div>
                            <div>
                                <div style="font-weight: 500;">{{ $review->user->name ?? 'N/A' }}</div>
                                <div style="font-size: 12px; color: #6b7280;">{{ $review->user->email ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 15px;">
                        <div style="font-weight: 500;">{{ Str::limit($review->property->title ?? 'Property #' . $review->properties_id, 30) }}</div>
                        <div style="font-size: 12px; color: #6b7280;">{{ $review->property->city ?? 'N/A' }}</div>
                    </td>
                    <td style="padding: 15px;">
                        <div style="display: flex; align-items: center; gap: 4px;">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= ($review->Rating ?? 0))
                                    <span style="color: #fbbf24; font-size: 18px;">⭐</span>
                                @else
                                    <span style="color: #d1d5db; font-size: 18px;">⭐</span>
                                @endif
                            @endfor
                            <span style="margin-left: 8px; font-weight: 600; color: #374151;">{{ $review->Rating ?? 0 }}/5</span>
                        </div>
                    </td>
                    <td style="padding: 15px; color: #6b7280; max-width: 300px;">
                        {{ Str::limit($review->comment ?? 'No comment', 60) }}
                    </td>
                    <td style="padding: 15px;">
                        @if($review->flag == 'approved' || !$review->flag)
                            <span style="background: #d1fae5; color: #065f46; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                                Approved
                            </span>
                        @else
                            <span style="background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                                Flagged
                            </span>
                        @endif
                    </td>
                    <td style="padding: 15px; color: #6b7280;">{{ $review->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 40px; text-align: center; color: #9ca3af;">
                        No reviews found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
