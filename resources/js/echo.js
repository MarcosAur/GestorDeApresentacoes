import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'minha-chave-local',
    wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Remove o cabeçalho X-Socket-ID se for a string "undefined" no Axios
window.axios.interceptors.request.use((config) => {
    if (config.headers) {
        // Compatibilidade com Axios 1.x (AxiosHeaders instance)
        if (typeof config.headers.delete === 'function') {
            if (config.headers.get('X-Socket-Id') === 'undefined' || config.headers.get('x-socket-id') === 'undefined') {
                config.headers.delete('X-Socket-Id');
                config.headers.delete('x-socket-id');
            }
        } else {
            // Compatibilidade com dicionário padrão de objetos
            Object.keys(config.headers).forEach(key => {
                if (key.toLowerCase() === 'x-socket-id' && String(config.headers[key]) === 'undefined') {
                    delete config.headers[key];
                }
            });
        }
    }
    return config;
});

// Remove o cabeçalho X-Socket-ID se for a string "undefined" no Livewire
document.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ options }) => {
        if (options.headers) {
            Object.keys(options.headers).forEach(key => {
                if (key.toLowerCase() === 'x-socket-id' && String(options.headers[key]) === 'undefined') {
                    delete options.headers[key];
                }
            });
        }
    });
});
