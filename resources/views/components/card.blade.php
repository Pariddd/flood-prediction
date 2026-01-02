<div {{ $attributes->merge([
    'class' => 'bg-slate-800 rounded-xl shadow-lg border border-slate-700 p-5'
]) }}>
    {{ $slot }}
</div>