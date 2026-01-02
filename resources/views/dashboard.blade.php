@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h2 class="font-semibold mb-2">Cuaca Saat Ini</h2>
            <p class="text-slate-500 text-sm">Belum ada data</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5">
            <h2 class="font-semibold mb-2">Risiko Banjir</h2>
            <p class="text-slate-500 text-sm">Belum ada prediksi</p>
        </div>
    </div>
@endsection
