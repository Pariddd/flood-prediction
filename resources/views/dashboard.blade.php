@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-card class="lg:col-span-2 p-0 overflow-hidden relative">
            <div class="absolute inset-0 bg-cover bg-center opacity-50" 
                 style="background-image: url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800');">
            </div>
            <div class="relative h-full text-white p-8 min-h-125 flex flex-col justify-between">
                <div class="flex justify-center items-start">
                    <div class="backdrop-blur-md rounded-3xl p-5">
                        <p class="text-2xl font-semibold text-center">{{ $day }}</p>
                        <p class="text-xl opacity-90 mt-1 text-center">{{ $date }}</p>
                        <p class="mt-2 text-sm flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            {{ $city }}
                        </p>
                    </div>
                </div>
                <div class="text-center">
                    <div class="flex justify-center mb-6">
                        @switch($weather)
                            @case('Clear')
                                <svg class="w-20 h-20 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                                </svg>
                                @break
                            @case('Clouds')
                                <svg class="w-20 h-20 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z"/>
                                </svg>
                                @break
                            @case('Rain')
                            @case('Drizzle')
                                <svg class="w-20 h-20 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z"/>
                                    <path d="M8 17a1 1 0 002 0v-2a1 1 0 10-2 0v2z"/>
                                    <path d="M11 17a1 1 0 002 0v-3a1 1 0 10-2 0v3z"/>
                                </svg>
                                @break
                            @case('Thunderstorm')
                                <svg class="w-20 h-20 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z"/>
                                    <path d="M9 8l-2 4h3l-1 4 4-6h-3l1-4z"/>
                                </svg>
                                @break
                            @default
                                <svg class="w-20 h-20 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z"/>
                                </svg>
                        @endswitch
                    </div>
                    
                    <h1 class="text-8xl font-bold leading-none">{{ $temperature }}°C
</h1>
                    <p class="text-2xl mt-4 font-light">{{ ucfirst($weather) }}
</p>
                </div>
                <div></div>
            </div>
        </x-card>
        <x-card class="flex flex-col gap-6">
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
                <div class="bg-white rounded-2xl p-4 text-center text-slate-800 transform hover:scale-105 transition-transform duration-200 shadow-lg">
                    <svg class="w-10 h-10 mx-auto text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs mt-2 text-slate-500 font-medium">Sun</p>
                    <p class="font-bold mt-1 text-base">23°C</p>
                </div>
                <div class="bg-slate-700/50 rounded-2xl p-4 text-center hover:bg-white hover:text-slate-800 transition-all duration-200 cursor-pointer group transform hover:scale-105">
                    <svg class="w-10 h-10 mx-auto text-slate-300 group-hover:text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z"/>
                    </svg>
                    <p class="text-xs mt-2 text-slate-400 group-hover:text-slate-500 font-medium">Mon</p>
                    <p class="font-bold mt-1 text-base group-hover:text-slate-800">28°C</p>
                </div>
                <div class="bg-slate-700/50 rounded-2xl p-4 text-center hover:bg-white hover:text-slate-800 transition-all duration-200 cursor-pointer group transform hover:scale-105">
                    <svg class="w-10 h-10 mx-auto text-blue-300 group-hover:text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z"/>
                    </svg>
                    <p class="text-xs mt-2 text-slate-400 group-hover:text-slate-500 font-medium">Tue</p>
                    <p class="font-bold mt-1 text-base group-hover:text-slate-800">02°C</p>
                </div>
                <div class="bg-slate-700/50 rounded-2xl p-4 text-center hover:bg-white hover:text-slate-800 transition-all duration-200 cursor-pointer group transform hover:scale-105">
                    <svg class="w-10 h-10 mx-auto text-slate-400 group-hover:text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z"/>
                    </svg>
                    <p class="text-xs mt-2 text-slate-400 group-hover:text-slate-500 font-medium">Wed</p>
                    <p class="font-bold mt-1 text-base group-hover:text-slate-800">14°C</p>
                </div>
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
        </x-card>

        @if(session('error'))
            <x-card class="bg-red-100 border-red-300 text-red-700 mb-4">
                {{ session('error') }}
            </x-card>
        @endif

    </div>
@endsection