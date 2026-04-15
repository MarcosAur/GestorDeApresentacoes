@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="font-display text-4xl font-bold tracking-tight text-white">Dashboard</h1>
    <p class="text-surface-container-highest font-medium">Bem-vindo ao Gestor de Apresentação Gemini.</p>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-surface-container-low p-6 rounded-xl border border-outline-variant/10">
            <h3 class="font-display text-lg font-bold text-primary">Status</h3>
            <p class="mt-2 text-3xl font-bold">Ativo</p>
        </div>
    </div>
</div>
@endsection
