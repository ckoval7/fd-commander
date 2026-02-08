<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - {{ \App\Models\Setting::get('site_name') ?: config('app.name') }}</title>

    {{-- Set theme before page renders to prevent flash --}}
    <script>
        (function() {
            let theme = localStorage.getItem('theme');
            // If no saved preference, default to light and save it
            if (!theme) {
                theme = 'light';
                localStorage.setItem('theme', theme);
            }
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* TV Dashboard Specific Styles */
        body {
            overflow: hidden; /* Prevent scrolling */
        }

        .tv-dashboard-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .tv-widget-grid {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            grid-template-rows: repeat(2, 1fr);
            gap: 1.5rem;
            padding: 1.5rem;
        }

        /* Kiosk mode hint */
        .kiosk-hint {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            opacity: 0;
            animation: fadeInOut 4s ease-in-out;
        }

        @keyframes fadeInOut {
            0%, 100% { opacity: 0; }
            10%, 90% { opacity: 1; }
        }

        /* Fullscreen API support */
        .tv-dashboard-container:-webkit-full-screen {
            width: 100vw;
            height: 100vh;
        }

        .tv-dashboard-container:-moz-full-screen {
            width: 100vw;
            height: 100vh;
        }

        .tv-dashboard-container:fullscreen {
            width: 100vw;
            height: 100vh;
        }
    </style>
</head>
<body class="font-sans antialiased bg-base-200">
    <div class="tv-dashboard-container" id="tv-dashboard">
        {{-- Header Bar (minimal, can be hidden in kiosk mode) --}}
        <div
            class="bg-base-100 border-b border-base-300 px-6 py-3 flex items-center justify-between"
            x-data="{ showHeader: {{ $kiosk ? 'false' : 'true' }} }"
            x-show="showHeader"
        >
            <div class="flex items-center gap-4">
                <x-app-brand />
                <div class="text-sm text-base-content/60">{{ $title }}</div>
            </div>

            <div class="flex items-center gap-3">
                <livewire:components.event-countdown />
                <x-custom-theme-toggle />

                {{-- Fullscreen Toggle Button --}}
                <button
                    @click="toggleFullscreen()"
                    class="btn btn-sm btn-ghost"
                    title="Toggle Fullscreen (Press 'f')"
                >
                    <x-icon name="o-arrows-pointing-out" class="w-5 h-5" />
                </button>
            </div>
        </div>

        {{-- Widget Grid (5 columns × 2 rows) --}}
        <div class="tv-widget-grid">
            @foreach($widgets as $widget)
                @if($widget['visible'] ?? true)
                    <div
                        class="tv-widget-cell"
                        data-widget-id="{{ $widget['id'] }}"
                    >
                        @livewire(
                            'dashboard.widgets.' . str_replace('_', '-', $widget['type']),
                            [
                                'config' => $widget['config'],
                                'size' => 'tv',
                                'widgetId' => $widget['id']
                            ],
                            key($widget['id'])
                        )
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Kiosk Mode Hint (shown on load if enabled) --}}
        @if(config('dashboard.kiosk.show_exit_hint') && $kiosk)
            <div class="kiosk-hint">
                Press 'ESC' to exit fullscreen
            </div>
        @endif
    </div>

    {{-- Toast notification area --}}
    <x-toast />

    {{-- Kiosk Mode & Fullscreen Logic --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            const container = document.getElementById('tv-dashboard');
            const kioskShortcut = '{{ config('dashboard.kiosk.shortcut', 'f') }}'.toLowerCase();
            const autoEnableKiosk = {{ config('dashboard.kiosk.auto_enable_on_tv') ? 'true' : 'false' }};
            const isKioskMode = {{ $kiosk ? 'true' : 'false' }};

            // Toggle fullscreen function
            window.toggleFullscreen = function() {
                if (!document.fullscreenElement) {
                    container.requestFullscreen().catch(err => {
                        console.error('Error attempting to enable fullscreen:', err);
                    });
                } else {
                    document.exitFullscreen();
                }
            };

            // Keyboard shortcut for fullscreen
            document.addEventListener('keydown', (e) => {
                if (e.key.toLowerCase() === kioskShortcut && !e.ctrlKey && !e.metaKey && !e.altKey) {
                    e.preventDefault();
                    window.toggleFullscreen();
                }
            });

            // Auto-enable kiosk mode if configured and URL param is set
            if (autoEnableKiosk && isKioskMode) {
                // Wait a moment for page to settle, then enter fullscreen
                setTimeout(() => {
                    window.toggleFullscreen();
                }, 500);
            }

            // Listen for fullscreen changes
            document.addEventListener('fullscreenchange', () => {
                if (document.fullscreenElement) {
                    console.log('Entered fullscreen mode');
                } else {
                    console.log('Exited fullscreen mode');
                }
            });

            // Toast notification listener
            Livewire.on('notify', (event) => {
                const isError = event.title.toLowerCase().includes('error');
                const iconSvg = isError
                    ? '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>'
                    : '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>';

                window.toast({
                    toast: {
                        title: event.title,
                        description: event.description,
                        icon: iconSvg,
                        css: isError ? 'alert-error' : 'alert-success',
                        timeout: 3000
                    }
                });
            });
        });
    </script>
</body>
</html>
