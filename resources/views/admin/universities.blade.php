@extends('layouts.admin')

@section('content')
<div class="header">
    <div class="header-left">
        <h1>Universities</h1>
        <p>Manage all universities</p>
    </div>
    <div class="header-right">
        <button onclick="toggleAddForm()" style="background: #10b981; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; display: flex; align-items: center; gap: 8px;">
            <span>➕</span> Add University
        </button>
    </div>
</div>

<!-- Add University Form -->
<div id="addUniversityForm" style="display: none; margin-top: 20px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Add New University</div>
        </div>
        <div style="padding: 20px;">
            <form action="{{ route('admin.universities.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">University Name *</label>
                    <input type="text" name="university" required
                           style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;"
                           placeholder="Enter university name">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">Latitude *</label>
                        <input type="text" name="latitude" required
                               style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;"
                               placeholder="e.g., -17.8252">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">Longitude *</label>
                        <input type="text" name="longitude" required
                               style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;"
                               placeholder="e.g., 31.0335">
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" style="background: #10b981; color: white; padding: 10px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                        Save University
                    </button>
                    <button type="button" onclick="toggleAddForm()" style="background: #e5e7eb; color: #374151; padding: 10px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
    <div style="margin-top: 20px; padding: 15px; background: #d1fae5; color: #065f46; border-radius: 8px; font-weight: 500;">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div style="margin-top: 20px; padding: 15px; background: #fee2e2; color: #991b1b; border-radius: 8px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

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

<script>
    function toggleAddForm() {
        const form = document.getElementById('addUniversityForm');
        if (form.style.display === 'none') {
            form.style.display = 'block';
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            form.style.display = 'none';
        }
    }
</script>
@endsection
