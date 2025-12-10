@extends('layouts.admin')

@section('content')
<div class="header">
    <div class="header-left">
        <h1>University Analytics</h1>
        <p>Universities ranked by number of properties</p>
    </div>
</div>

<div class="stats-grid" style="margin-top: 20px; display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
    @forelse($universities as $university)
    <div class="card" style="padding: 0; overflow: hidden; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s;">
        <div style="padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div style="flex: 1;">
                    <h3 style="margin: 0 0 10px 0; font-size: 18px; font-weight: 600;">{{ $university['university'] }}</h3>
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; opacity: 0.9;">
                        <span>🏘️</span>
                        <span>{{ $university['property_count'] }} {{ $university['property_count'] == 1 ? 'Property' : 'Properties' }}</span>
                    </div>
                </div>
                <div style="background: rgba(255,255,255,0.2); padding: 12px; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    🎓
                </div>
            </div>
        </div>

        <div style="padding: 20px; background: white;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Latitude</div>
                    <div style="font-family: monospace; font-size: 13px; color: #374151; font-weight: 500;">{{ $university['latitude'] ?? 'N/A' }}</div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Longitude</div>
                    <div style="font-family: monospace; font-size: 13px; color: #374151; font-weight: 500;">{{ $university['longitude'] ?? 'N/A' }}</div>
                </div>
            </div>

            @if($university['latitude'] && $university['longitude'])
            <a href="https://www.google.com/maps/search/?api=1&query={{ $university['latitude'] }},{{ $university['longitude'] }}"
               target="_blank"
               style="display: flex; align-items: center; justify-content: center; gap: 8px; background: #3b82f6; color: white; padding: 10px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500; transition: background 0.3s;">
                <span>📍</span> View on Map
            </a>
            @endif
        </div>

        <div style="padding: 12px 20px; background: #f9fafb; border-top: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="font-size: 12px; color: #6b7280;">
                    @if($university['property_count'] > 0)
                        <span style="color: #10b981; font-weight: 600;">● Active</span>
                    @else
                        <span style="color: #9ca3af; font-weight: 600;">○ No Properties</span>
                    @endif
                </div>
                <div style="font-size: 12px; color: #6b7280;">
                    ID: #{{ $university['id'] }}
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="card" style="padding: 40px; text-align: center; color: #9ca3af; grid-column: 1 / -1;">
        <div style="font-size: 48px; margin-bottom: 10px;">🏫</div>
        <div style="font-size: 18px; font-weight: 500; margin-bottom: 5px;">No Universities Found</div>
        <div style="font-size: 14px;">Add universities to see analytics here</div>
    </div>
    @endforelse
</div>

<style>
    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }

    a:hover {
        background: #2563eb !important;
    }
</style>
@endsection
