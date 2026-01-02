@extends('layouts.app')

@section('title', 'Heatmap Risiko Banjir')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white">
                Heatmap Risiko Banjir
            </h1>
            <p class="text-slate-400 mt-1">Monitoring area {{ $city }}</p>
        </div>

        <form method="GET" action="/heatmap" class="flex gap-2">
            <input 
                type="text" 
                name="city" 
                value="{{ request('city', 'Banda Aceh') }}"
                placeholder="Cari lokasi..."
                class="bg-slate-800 border border-slate-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                Cari
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-card class="bg-linear-to-br from-slate-800 to-slate-900 border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm font-medium">Total Area</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-blue-500/20 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
            </div>
        </x-card>

        <x-card class="bg-linear-to-br from-red-900/20 to-red-800/20 border-red-800/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-300 text-sm font-medium">Bahaya</p>
                    <p class="text-3xl font-bold text-red-400 mt-1">{{ $stats['bahaya'] }}</p>
                </div>
                <div class="bg-red-500/20 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </x-card>

        <x-card class="bg-linear-to-br from-orange-900/20 to-orange-800/20 border-orange-800/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-300 text-sm font-medium">Waspada</p>
                    <p class="text-3xl font-bold text-orange-400 mt-1">{{ $stats['waspada'] }}</p>
                </div>
                <div class="bg-orange-500/20 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
            </div>
        </x-card>

        <x-card class="bg-linear-to-br from-green-900/20 to-green-800/20 border-green-800/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-300 text-sm font-medium">Aman</p>
                    <p class="text-3xl font-bold text-green-400 mt-1">{{ $stats['aman'] }}</p>
                </div>
                <div class="bg-green-500/20 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </x-card>
    </div>

    <x-card class="p-0 overflow-hidden">
        <div id="map" class="w-full h-150"></div>
    </x-card>

    <x-card>
        <h3 class="text-lg font-semibold text-white mb-4">Legenda Risiko</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-4 h-4 rounded-full bg-green-500"></div>
                <div>
                    <p class="text-white font-medium">Aman</p>
                    <p class="text-slate-400 text-sm">Risiko rendah, kondisi normal</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-4 h-4 rounded-full bg-orange-500"></div>
                <div>
                    <p class="text-white font-medium">Waspada</p>
                    <p class="text-slate-400 text-sm">Risiko sedang, perlu monitoring</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-4 h-4 rounded-full bg-red-500"></div>
                <div>
                    <p class="text-white font-medium">Bahaya</p>
                    <p class="text-slate-400 text-sm">Risiko tinggi, perlu antisipasi</p>
                </div>
            </div>
        </div>
    </x-card>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const map = L.map('map').setView([{{ $lat }}, {{ $lon }}], 13);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap contributors © CARTO',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    const points = @json($points);

    points.forEach(p => {
        const marker = L.circleMarker([p.lat, p.lon], {
            radius: 10,
            color: '#fff',
            weight: 2,
            fillColor: p.color,
            fillOpacity: 0.8
        }).addTo(map);

        const popupContent = `
            <div class="p-2 min-w-[200px]">
                <h4 class="font-bold text-lg mb-2 ${
                    p.risk === 'Bahaya' ? 'text-red-600' : 
                    p.risk === 'Waspada' ? 'text-orange-600' : 
                    'text-green-600'
                }">
                    ${p.risk}
                </h4>
                <div class="space-y-1 text-sm">
                    <p><strong>Probabilitas:</strong> ${p.probability}%</p>
                    <p><strong>Hujan 24j:</strong> ${p.data.rain_24h} mm</p>
                    <p><strong>Hujan 3h:</strong> ${p.data.rain_3d} mm</p>
                    <p><strong>Kelembaban:</strong> ${p.data.humidity}%</p>
                    <p><strong>Suhu:</strong> ${p.data.temperature}°C</p>
                    <p><strong>Kecepatan Angin:</strong> ${p.data.wind_speed} m/s</p>
                    <p><strong>Elevasi:</strong> ${p.data.elevation} m</p>
                </div>
            </div>
        `;

        marker.bindPopup(popupContent);

        marker.on('mouseover', function() {
            this.setStyle({
                radius: 12,
                weight: 3
            });
        });

        marker.on('mouseout', function() {
            this.setStyle({
                radius: 10,
                weight: 2
            });
        });
    });

    L.marker([{{ $lat }}, {{ $lon }}], {
        icon: L.divIcon({
            className: 'custom-pin',
            html: '<div style="background: #3b82f6; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        })
    }).addTo(map).bindPopup(`<strong>{{ $city }}</strong><br>Pusat monitoring`);

    if (points.length > 0) {
        const bounds = L.latLngBounds(points.map(p => [p.lat, p.lon]));
        map.fitBounds(bounds, { padding: [50, 50] });
    }
</script>

<style>
    .leaflet-popup-content-wrapper {
        border-radius: 8px;
    }
    .leaflet-popup-content {
        margin: 0;
    }
</style>
@endsection