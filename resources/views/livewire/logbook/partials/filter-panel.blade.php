{{-- Filter Panel Component --}}
<x-card class="shadow-md">
    <div x-data="{ showFilters: true }">
        {{-- Header with Toggle --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <h3 class="text-lg font-semibold">Filters</h3>
            <div class="flex items-center gap-2">
                <button
                    @click="showFilters = !showFilters"
                    class="lg:hidden btn btn-sm btn-ghost min-h-[2.75rem] sm:min-h-[1.75rem]"
                    type="button"
                >
                    <x-icon x-show="!showFilters" name="o-chevron-down" class="w-5 h-5" />
                    <x-icon x-show="showFilters" name="o-chevron-up" class="w-5 h-5" />
                    <span class="ml-1" x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
                </button>
                <button
                    wire:click="resetFilters"
                    class="btn btn-sm btn-outline min-h-[2.75rem] sm:min-h-[1.75rem]"
                    type="button"
                >
                    <x-icon name="o-x-mark" class="w-4 h-4" />
                    <span class="hidden sm:inline ml-1">Reset Filters</span>
                    <span class="sm:hidden ml-1">Reset</span>
                </button>
            </div>
        </div>

        {{-- Filter Controls --}}
        <div x-show="showFilters" x-collapse>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                {{-- Band Filter --}}
                <x-select
                    label="Band"
                    wire:model.live="band_id"
                    :options="$this->bands"
                    placeholder="All Bands"
                    icon="o-signal"
                />

                {{-- Mode Filter --}}
                <x-select
                    label="Mode"
                    wire:model.live="mode_id"
                    :options="$this->modes"
                    placeholder="All Modes"
                    icon="o-radio"
                />

                {{-- Station Filter --}}
                <x-select
                    label="Station"
                    wire:model.live="station_id"
                    :options="$this->stations"
                    placeholder="All Stations"
                    icon="o-home"
                />

                {{-- Operator Filter --}}
                <x-select
                    label="Operator"
                    wire:model.live="operator_id"
                    :options="$this->operators"
                    option-label="display_name"
                    placeholder="All Operators"
                    icon="o-user"
                />

                {{-- Section Filter --}}
                <x-select
                    label="Section"
                    wire:model.live="section_id"
                    :options="$this->sections"
                    option-label="code"
                    placeholder="All Sections"
                    icon="o-map"
                />

                {{-- Callsign Search --}}
                <x-input
                    label="Callsign"
                    wire:model.live.debounce.500ms="callsign_search"
                    placeholder="Search callsign..."
                    icon="o-magnifying-glass"
                    clearable
                />

                {{-- Time From --}}
                <x-datetime
                    label="Time From"
                    wire:model.live="time_from"
                    type="datetime-local"
                    icon="o-calendar"
                />

                {{-- Time To --}}
                <x-datetime
                    label="Time To"
                    wire:model.live="time_to"
                    type="datetime-local"
                    icon="o-calendar"
                />

                {{-- Duplicate Filter --}}
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-base-content/70">Duplicate Status</label>
                    <div class="flex flex-col gap-2">
                        <x-radio
                            wire:model.live="show_duplicates"
                            :options="[
                                ['id' => null, 'name' => 'All Contacts'],
                                ['id' => 'exclude', 'name' => 'Exclude Duplicates'],
                                ['id' => 'only', 'name' => 'Duplicates Only']
                            ]"
                        />
                    </div>
                </div>

                {{-- Transcribed Filter --}}
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-base-content/70">Transcribed Status</label>
                    <div class="flex flex-col gap-2">
                        <x-radio
                            wire:model.live="show_transcribed"
                            :options="[
                                ['id' => null, 'name' => 'All Contacts'],
                                ['id' => 'only', 'name' => 'Transcribed Only']
                            ]"
                        />
                    </div>
                </div>

            </div>

            {{-- Active Filters Summary --}}
            @php
                $activeFilters = collect([
                    'Band' => $band_id,
                    'Mode' => $mode_id,
                    'Station' => $station_id,
                    'Operator' => $operator_id,
                    'Section' => $section_id,
                    'Callsign' => $callsign_search,
                    'Time Range' => ($time_from || $time_to) ? 'Active' : null,
                    'Duplicates' => $show_duplicates,
                    'Transcribed' => $show_transcribed,
                ])->filter()->count();
            @endphp

            @if($activeFilters > 0)
                <div class="mt-4 pt-4 border-t border-base-300">
                    <div class="flex items-center gap-2 text-sm text-base-content/70">
                        <x-icon name="o-funnel" class="w-4 h-4" />
                        <span>{{ $activeFilters }} {{ Str::plural('filter', $activeFilters) }} active</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-card>
