@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-card class="lg:col-span-2 p-0 overflow-hidden relative">
            <div class="absolute inset-0 bg-cover bg-center opacity-50" 
                 style="background-image: url('https://images.unsplash.com/photo-1548430065-53c58a6582dd?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');">
            </div>
            <div class="relative h-full text-white p-8 min-h-125 flex flex-col justify-between">
                <div class="flex justify-center items-start mt-10">
                    <div class="p-5">
                        <p class="text-3xl font-semibold text-center">{{ $day }}</p>
                        <p class="text-2xl opacity-90 mt-1 text-center">{{ $date }}</p>
                        <p class="mt-2 text-md flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            {{ $city }}
                        </p>
                    </div>
                </div>
                <div class="text-center ">
                    <div class="flex justify-center mb-9">
                        <img
                            src="https://openweathermap.org/img/wn/{{ $currentIcon }}@4x.png"
                            alt="Current weather icon"
                            class="w-50 h-50 drop-shadow-lg"
                        >
                    </div>
                    <h1 class="text-8xl font-bold leading-none">
                        {{ $temperature }}°C
                    </h1>
                    <p class="text-2xl mt-4 font-light capitalize">
                        {{ $description }}
                    </p>
                </div>
            </div>
        </x-card>
        <x-card class="flex flex-col gap-6">
            <div class="p-4 rounded-xl
                @if($riskLevel === 'Bahaya') bg-red-500/20
                @elseif($riskLevel === 'Waspada') bg-yellow-400/50
                @else bg-green-500/20
                @endif
                text-white text-center">

                <p class="text-sm uppercase tracking-wide">
                    Prediksi Risiko Banjir (ML)
                </p>

                <p class="text-2xl font-bold mt-1">
                    {{ $riskLevel }}
                </p>

                @if($confidence)
                    <p class="text-sm opacity-90 mt-1">
                        Confidence: {{ $confidence * 100 }}%
                    </p>
                @endif
            </div>
            <div class="border-t border-slate-700 my-2"></div>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 text-sm font-medium uppercase tracking-wide">Rain</span>
                    <span class="text-white text-xl font-bold">{{ $rain }} %</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 text-sm font-medium uppercase tracking-wide">Humidity</span>
                    <span class="text-white text-xl font-bold">{{ $humidity }} %</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 text-sm font-medium uppercase tracking-wide">Wind Speed</span>
                    <span class="text-white text-xl font-bold">{{ $wind }} km/h</span>
                </div>
            </div>
            <div class="border-t border-slate-700 my-2"></div>
            <div class="grid grid-cols-4 gap-3">
            @foreach($forecast as $index => $day)
                <div class="
                    rounded-2xl p-4 text-center transform transition-all duration-200
                    {{ $index === 0
                         ? 'bg-slate-700/70 text-white shadow-lg scale-105 border border-slate-500'
                        : 'bg-slate-700/40 text-slate-200 hover:bg-slate-600/60 hover:scale-105 cursor-pointer'
                                    }}
                ">
                    <div class="flex justify-center mb-2">
                        <img
                            src="https://openweathermap.org/img/wn/{{ $day['icon'] }}@2x.png"
                            alt="weather icon"
                            class="w-12 h-12"
                        >
                    </div>
                    <p class="text-xs font-medium mt-1">
                        {{ $day['day'] }}
                    </p>
                    <p class="font-bold mt-1 text-base">
                        {{ $day['temp'] }}°C
                    </p>
                </div>
            @endforeach
            </div>
            <form method="GET" action="/" class="space-y-3">
                <input
                    type="text"
                    name="city"
                    placeholder="Cari kota (contoh: Jakarta)"
                    value="{{ request('city') }}"
                    class="w-full px-4 py-3 rounded-xl 
                        bg-white text-slate-800 
                        focus:outline-none focus:ring-2 focus:ring-blue-500
                        shadow-sm"
                    required
                >
                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 
                        text-white font-semibold py-3.5 px-4 
                        rounded-xl transition-colors duration-200 
                        shadow-lg"
                >
                    Search Location
                </button>
            </form>
            @if(session('error'))
                <x-card class="bg-red-100 border-red-300 text-red-700 mb-4">
                    {{ session('error') }}
                </x-card>
            @endif
        </x-card>

    </div>
@endsection