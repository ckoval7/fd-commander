{{--
Feed Widget View

Displays a scrollable list of recent activity notifications.
Supports normal and TV size variants with feed type filtering.

Props from component:
- $data: Array with 'items' (feed items), 'feed_type', 'feed_label'
- $size: 'normal' or 'tv'

Each item: id, icon, title, message, time_ago, read
--}}

<div class="h-full flex flex-col">
    @if ($size === 'tv')
        {{-- TV Mode: Larger text and spacing for kiosk/TV dashboards --}}
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-base-content">
                {{ $data['feed_label'] }}
            </h2>
            <x-badge value="{{ count($data['items']) }} items" class="badge-ghost badge-lg" />
        </div>

        <div class="overflow-y-auto max-h-[500px] space-y-3 pr-1"
             x-data="{ items: @js(count($data['items'])) }"
        >
            @forelse ($data['items'] as $item)
                <div
                    wire:key="feed-item-{{ $item['id'] }}"
                    class="flex items-start gap-4 p-4 rounded-xl transition-colors
                        {{ $item['read'] ? 'bg-base-200/50' : 'bg-primary/10 border-l-4 border-primary' }}"
                >
                    <div class="flex-shrink-0 mt-1">
                        <x-icon
                            :name="$item['icon']"
                            class="w-8 h-8 {{ $item['read'] ? 'text-base-content/50' : 'text-primary' }}"
                        />
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-baseline justify-between gap-2">
                            <span class="text-lg {{ $item['read'] ? 'font-normal text-base-content/70' : 'font-bold text-base-content' }} truncate">
                                {{ $item['title'] }}
                            </span>
                            <span class="text-base text-base-content/50 flex-shrink-0">
                                {{ $item['time_ago'] }}
                            </span>
                        </div>
                        <p class="text-base {{ $item['read'] ? 'text-base-content/50' : 'text-base-content/80' }} mt-1 line-clamp-2">
                            {{ $item['message'] }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 text-base-content/50">
                    <x-icon name="o-inbox" class="w-16 h-16 mb-4" />
                    <p class="text-xl">No activity yet</p>
                </div>
            @endforelse
        </div>
    @else
        {{-- Normal Mode: Compact feed display --}}
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-base-content/70 uppercase tracking-wide">
                {{ $data['feed_label'] }}
            </h3>
            <x-badge value="{{ count($data['items']) }}" class="badge-ghost badge-xs" />
        </div>

        <div class="overflow-y-auto max-h-[350px] space-y-1 pr-1"
             x-data="{ items: @js(count($data['items'])) }"
        >
            @forelse ($data['items'] as $item)
                <div
                    wire:key="feed-item-{{ $item['id'] }}"
                    class="flex items-start gap-2 p-2 rounded-lg transition-colors
                        {{ $item['read'] ? 'hover:bg-base-200/50' : 'bg-primary/5 border-l-2 border-primary' }}"
                >
                    <div class="flex-shrink-0 mt-0.5">
                        <x-icon
                            :name="$item['icon']"
                            class="w-5 h-5 {{ $item['read'] ? 'text-base-content/40' : 'text-primary' }}"
                        />
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-baseline justify-between gap-1">
                            <span class="text-sm {{ $item['read'] ? 'font-normal text-base-content/60' : 'font-semibold text-base-content' }} truncate">
                                {{ $item['title'] }}
                            </span>
                            <span class="text-xs text-base-content/40 flex-shrink-0">
                                {{ $item['time_ago'] }}
                            </span>
                        </div>
                        <p class="text-xs {{ $item['read'] ? 'text-base-content/40' : 'text-base-content/70' }} truncate">
                            {{ $item['message'] }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-8 text-base-content/50">
                    <x-icon name="o-inbox" class="w-10 h-10 mb-2" />
                    <p class="text-sm">No activity yet</p>
                </div>
            @endforelse
        </div>
    @endif
</div>
