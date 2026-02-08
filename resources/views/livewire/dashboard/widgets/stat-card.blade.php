{{--
StatCard Widget View

Displays a single metric in a card with icon, value, and label.
Supports normal and TV size variants.

Props from component:
- $data: Array with 'value', 'label', 'icon', 'color'
- $size: 'normal' or 'tv'
--}}

<div class="h-full" wire:poll.3s>
    @if ($size === 'tv')
        {{-- TV Mode: Large display for kiosk/TV dashboards --}}
        <x-card class="h-full flex flex-col items-center justify-center p-6 sm:p-8">
            <x-icon
                :name="$data['icon']"
                class="w-16 h-16 sm:w-20 sm:h-20 {{ $data['color'] }} mb-4"
            />

            <div class="text-center">
                <div class="text-4xl sm:text-5xl lg:text-6xl font-bold {{ $data['color'] }} mb-2">
                    {{ $data['value'] }}
                </div>
                <div class="text-xl sm:text-2xl text-base-content/70">
                    {{ $data['label'] }}
                </div>
            </div>
        </x-card>
    @else
        {{-- Normal Mode: Compact stat display --}}
        <x-stat
            :title="$data['label']"
            :value="$data['value']"
            :icon="$data['icon']"
            :color="$data['color']"
            class="h-full"
        />
    @endif
</div>
