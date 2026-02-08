{{--
ProgressBar Widget View

Displays progress toward next 50-QSO milestone with:
- Progress bar (MaryUI component)
- Current/Target numbers
- Percentage
- Label
- TV size variant support

Size variants:
- normal: Standard dashboard view
- tv: Larger for kiosk/TV displays
--}}

<div class="flex flex-col gap-3" wire:poll.3s>
    {{-- Numbers Display --}}
    <div class="flex items-baseline justify-between">
        <div class="@if($size === 'tv') text-4xl @else text-2xl @endif font-bold">
            {{ $data['current'] }}
            <span class="@if($size === 'tv') text-2xl @else text-base @endif font-normal text-base-content/70">
                / {{ $data['target'] }}
            </span>
        </div>
        <div class="@if($size === 'tv') text-xl @else text-sm @endif font-medium text-base-content/70">
            {{ $data['percentage'] }}%
        </div>
    </div>

    {{-- Progress Bar --}}
    <x-progress
        :value="$data['percentage']"
        max="100"
        class="progress-primary @if($size === 'tv') h-8 @else h-4 @endif"
    />

    {{-- Label --}}
    <div class="@if($size === 'tv') text-lg @else text-sm @endif text-center text-base-content/70">
        To next milestone
    </div>
</div>
