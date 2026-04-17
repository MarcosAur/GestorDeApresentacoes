<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro - Gestor de Apresentação Gemini</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@700&family=Manrope:wght@400;500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface text-white font-body antialiased flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <h1 class="font-display text-5xl font-bold tracking-tight text-primary mb-2">GEMINI</h1>
            <p class="text-white/60 font-medium">Gestão de Concursos e Apresentações</p>
        </div>

        <div class="bg-surface-container-low p-8 rounded-2xl shadow-2xl border border-outline-variant/10 backdrop-blur-xl">
            <h2 class="text-2xl font-display font-bold mb-8 tracking-tight">Crie sua conta</h2>

            <form method="POST" action="/register" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-admin font-medium text-white/60 mb-2">Nome Completo</label>
                    <input type="text" id="name" name="name" required autofocus
                        class="w-full bg-surface-container-highest border-none rounded-lg p-3 text-white focus:ring-2 focus:ring-primary transition-all outline-none"
                        value="{{ old('name') }}">
                    @error('name')
                        <p class="text-error text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-admin font-medium text-white/60 mb-2">E-mail</label>
                    <input type="email" id="email" name="email" required
                        class="w-full bg-surface-container-highest border-none rounded-lg p-3 text-white focus:ring-2 focus:ring-primary transition-all outline-none"
                        value="{{ old('email') }}">
                    @error('email')
                        <p class="text-error text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-admin font-medium text-white/60 mb-2">Senha</label>
                    <input type="password" id="password" name="password" required
                        class="w-full bg-surface-container-highest border-none rounded-lg p-3 text-white focus:ring-2 focus:ring-primary transition-all outline-none">
                    @error('password')
                        <p class="text-error text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-admin font-medium text-white/60 mb-2">Confirmar Senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full bg-surface-container-highest border-none rounded-lg p-3 text-white focus:ring-2 focus:ring-primary transition-all outline-none">
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        class="w-full bg-gradient-to-br from-primary to-primary-container p-4 rounded-xl font-bold text-surface hover:opacity-90 transition-all shadow-lg shadow-primary/20">
                        Cadastrar Agora
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm text-white/60">Já tem uma conta? <a href="{{ route('login') }}" class="text-secondary hover:underline transition-all">Faça login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
