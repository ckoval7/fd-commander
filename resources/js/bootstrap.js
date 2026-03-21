import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

globalThis.axios = axios;
globalThis.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Configure Laravel Echo if broadcasting is enabled
// For now, we'll set up Echo to connect to Reverb when it's available
// This prevents errors when Echo is not fully configured
globalThis.Pusher = Pusher;

try {
    // Only initialize Echo if Reverb configuration exists
    // This will prevent connection errors during development
    if (import.meta.env.VITE_REVERB_APP_KEY) {
        globalThis.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST ?? globalThis.location.hostname,
            wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
            wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
            forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
        });
    }
} catch (error) {
    console.warn('Laravel Echo not configured:', error.message);
}
