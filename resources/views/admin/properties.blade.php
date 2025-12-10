@extends('layouts.admin')

@section('content')
<div class="header">
    <div class="header-left">
        <h1>Properties</h1>
        <p>Manage all property listings</p>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <div class="card-title">All Properties ({{ $properties->count() }})</div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Title</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Type</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Price</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">City</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">View Location</th>
                </tr>
            </thead>
            <tbody>
                @forelse($properties as $property)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 15px;">
                        <div style="font-weight: 500;">{{ $property->title }}</div>
                    </td>
                    <td style="padding: 15px;">
                        <span style="background: #e0e7ff; color: #4338ca; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                            {{ $property->type }}
                        </span>
                    </td>
                    <td style="padding: 15px;">
                        <span style="background: #d1fae5; color: #065f46; padding: 6px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                            ${{ number_format($property->price, 2) }}
                        </span>
                    </td>
                    <td style="padding: 15px; color: #6b7280;">{{ $property->city }}</td>
                    <td style="padding: 15px;">
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($property->location . ', ' . $property->city . ', ' . $property->state) }}"
                           target="_blank"
                           style="display: inline-flex; align-items: center; gap: 5px; background: #3b82f6; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500; transition: background 0.3s;">
                            <span>📍</span> View on Map
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 40px; text-align: center; color: #9ca3af;">
                        No properties found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
