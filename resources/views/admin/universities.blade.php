@extends('layouts.admin')

@section('content')
<div class="header">
    <div class="header-left">
        <h1>Universities</h1>
        <p>Manage all universities</p>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <div class="card-title">All Universities ({{ $universities->count() }})</div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">ID</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">University Name</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Latitude</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">Longitude</th>
                    <th style="padding: 15px; text-align: left; font-weight: 600; color: #6b7280;">View Location</th>
                </tr>
            </thead>
            <tbody>
                @forelse($universities as $university)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 15px; font-weight: 500;">#{{ $university->id }}</td>
                    <td style="padding: 15px;">
                        <div style="font-weight: 500;">{{ $university->university }}</div>
                    </td>
                    <td style="padding: 15px; color: #6b7280; font-family: monospace;">{{ $university->latitude ?? 'N/A' }}</td>
                    <td style="padding: 15px; color: #6b7280; font-family: monospace;">{{ $university->longitude ?? 'N/A' }}</td>
                    <td style="padding: 15px;">
                        @if($university->latitude && $university->longitude)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $university->latitude }},{{ $university->longitude }}"
                               target="_blank"
                               style="display: inline-flex; align-items: center; gap: 5px; background: #3b82f6; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500; transition: background 0.3s;">
                                <span>📍</span> View on Map
                            </a>
                        @else
                            <span style="color: #9ca3af;">No coordinates</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 40px; text-align: center; color: #9ca3af;">
                        No universities found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
