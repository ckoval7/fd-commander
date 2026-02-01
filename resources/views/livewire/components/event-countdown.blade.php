<div
    @if($event) wire:poll.{{ $pollingInterval }}s="updateComponent" @endif
    class="flex flex-col lg:flex-row items-start lg:items-center gap-2 lg:gap-3 text-sm lg:text-base"
    aria-live="polite"
    aria-label="Event countdown timer"
>
    @if($event)
        {{-- Event Badge and Name --}}
        <div class="flex items-center gap-2">
            <span class="badge {{ $badgeClass }} badge-sm">
                {{ $state === 'active' ? 'LIVE' : strtoupper($state) }}
            </span>
            <span class="font-semibold {{ $textClass }}">
                {{ $event->name }}
            </span>
            @if($state === 'active')
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-success"></span>
                </span>
            @endif
        </div>

        {{-- Separator (desktop only) --}}
        <span class="hidden lg:inline text-base-content/30">|</span>

        {{-- Countdown Label and Time --}}
        <div class="flex items-center gap-2">
            <span class="text-sm font-medium">
                {{ $label }}:
            </span>
            <span class="font-mono font-bold {{ $textClass }}">
                {{ $this->formattedCountdown }}
            </span>
        </div>

        {{-- Separator (desktop only) --}}
        <span class="hidden lg:inline text-base-content/30">|</span>

        {{-- Clocks --}}
        <div class="flex items-center gap-3 text-sm">
            <div class="flex items-center gap-1">
                <span class="text-base-content/70">Local:</span>
                <span class="font-mono font-semibold">{{ $localTime }}</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="text-base-content/70">UTC:</span>
                <span class="font-mono font-semibold">{{ $utcTime }}</span>
            </div>
        </div>
    @endif
</div>
