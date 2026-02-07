<div>
    @if(!$eventConfigurationId)
        <x-card class="shadow-md">
            <div class="text-center py-12">
                <x-icon name="o-exclamation-circle" class="w-16 h-16 mx-auto text-warning" />
                <p class="mt-4 text-lg font-medium">No Active Event</p>
                <p class="text-sm text-base-content/70 mt-2">Please activate an event to view the logbook.</p>
            </div>
        </x-card>
    @else
        {{-- Stats Summary --}}
        <div wire:loading.remove class="mb-6">
            @include('livewire.logbook.partials.stats-summary')
        </div>

        {{-- Loading State for Stats --}}
        <div wire:loading class="mb-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @for($i = 0; $i < 6; $i++)
                    <x-card class="shadow-md">
                        <div class="h-16 animate-pulse bg-base-300 rounded"></div>
                    </x-card>
                @endfor
            </div>
        </div>

        {{-- Filter Panel --}}
        <div class="mb-6">
            @include('livewire.logbook.partials.filter-panel')
        </div>

        {{-- Results View --}}
        <div>
            @include('livewire.logbook.partials.results-view')
        </div>
    @endif
</div>
