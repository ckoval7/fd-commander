{{--
ListWidget View

Displays scrollable lists of data in three formats:
- recent_contacts: Recent QSOs with time, callsign, band, mode
- active_stations: Active stations with operator, band, status
- equipment_status: Equipment with status and assignment

Size variants:
- normal: 15 items, standard text, max-h-96
- tv: 10 items, larger text, max-h-[600px]
--}}

<div class="flex flex-col h-full">
    @if(count($data) > 0)
        {{-- Scrollable List Container --}}
        <div class="overflow-y-auto @if($size === 'tv') max-h-[600px] @else max-h-96 @endif">
            <div class="@if($size === 'tv') space-y-4 @else space-y-3 @endif">
                @foreach($data as $item)
                    @if($item['type'] === 'recent_contact')
                        {{-- Recent Contact Item --}}
                        <div class="@if($size === 'tv') p-4 @else p-3 @endif bg-base-200 rounded-lg">
                            <div class="flex flex-col gap-2">
                                {{-- Time and Callsign Row --}}
                                <div class="flex items-baseline justify-between gap-3">
                                    <div class="@if($size === 'tv') text-2xl @else text-lg @endif font-bold text-primary">
                                        {{ $item['callsign'] }}
                                    </div>
                                    <div class="@if($size === 'tv') text-base @else text-xs @endif text-base-content/70 flex-shrink-0">
                                        {{ $item['time_ago'] }}
                                    </div>
                                </div>
                                {{-- Band, Mode, Operator Row --}}
                                <div class="flex items-center gap-3 @if($size === 'tv') text-lg @else text-sm @endif text-base-content/80">
                                    <span class="font-medium">{{ $item['band'] }}</span>
                                    <span class="text-base-content/50">•</span>
                                    <span class="font-medium">{{ $item['mode'] }}</span>
                                    <span class="text-base-content/50">•</span>
                                    <span>{{ $item['operator'] }}</span>
                                </div>
                            </div>
                        </div>

                    @elseif($item['type'] === 'active_station')
                        {{-- Active Station Item --}}
                        <div class="@if($size === 'tv') p-4 @else p-3 @endif bg-base-200 rounded-lg">
                            <div class="flex flex-col gap-2">
                                {{-- Station Name --}}
                                <div class="@if($size === 'tv') text-2xl @else text-lg @endif font-bold">
                                    {{ $item['station_name'] }}
                                </div>
                                {{-- Operator and Band Row --}}
                                <div class="flex items-center gap-2 @if($size === 'tv') text-lg @else text-sm @endif text-base-content/80">
                                    <span>{{ $item['operator_name'] }}</span>
                                    <span class="text-base-content/50">operating on</span>
                                    <span class="font-medium">{{ $item['band'] }}</span>
                                    <span class="font-medium">{{ $item['mode'] }}</span>
                                </div>
                                {{-- Status Badge --}}
                                <div>
                                    <x-badge
                                        :value="$item['status']"
                                        class="badge-{{ $item['status_color'] }} @if($size === 'tv') badge-md @else badge-sm @endif"
                                    />
                                </div>
                            </div>
                        </div>

                    @elseif($item['type'] === 'equipment')
                        {{-- Equipment Status Item --}}
                        <div class="@if($size === 'tv') p-4 @else p-3 @endif bg-base-200 rounded-lg">
                            <div class="flex flex-col gap-2">
                                {{-- Equipment Name --}}
                                <div class="@if($size === 'tv') text-2xl @else text-lg @endif font-bold">
                                    {{ $item['equipment_name'] }}
                                </div>
                                {{-- Status and Assignment Row --}}
                                <div class="flex items-center gap-3 flex-wrap">
                                    <x-badge
                                        :value="$item['status']"
                                        class="badge-{{ $item['status_color'] }} @if($size === 'tv') badge-md @else badge-sm @endif"
                                    />
                                    @if($item['assigned_to'] !== 'Unassigned')
                                        <span class="@if($size === 'tv') text-lg @else text-sm @endif text-base-content/70">
                                            Assigned to {{ $item['assigned_to'] }}
                                        </span>
                                    @else
                                        <span class="@if($size === 'tv') text-lg @else text-sm @endif text-base-content/50 italic">
                                            Not assigned
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="flex items-center justify-center @if($size === 'tv') min-h-[400px] @else min-h-[300px] @endif">
            <div class="text-center">
                <div class="@if($size === 'tv') text-2xl @else text-lg @endif text-base-content/50">
                    No data available
                </div>
            </div>
        </div>
    @endif
</div>
