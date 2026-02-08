<x-layouts.app>
    <x-slot:title>{{ $dashboard->title }}</x-slot:title>

    <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl sm:text-3xl font-bold truncate">{{ $dashboard->title }}</h1>
                @if($dashboard->description)
                    <p class="text-sm sm:text-base text-base-content/60 mt-1">{{ $dashboard->description }}</p>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap gap-2">
                {{-- Dashboard Switcher --}}
                <x-button
                    label="Switch Dashboard"
                    icon="o-squares-2x2"
                    class="btn-outline min-h-[2.75rem] sm:min-h-[1.75rem]"
                    @click="$dispatch('open-dashboard-manager')"
                />

                {{-- Customize Button --}}
                <x-button
                    label="Customize Dashboard"
                    icon="o-cog-6-tooth"
                    class="btn-primary min-h-[2.75rem] sm:min-h-[1.75rem]"
                    @click="$dispatch('toggle-edit-mode')"
                />
            </div>
        </div>

        {{-- Edit Mode Controls (shown when edit mode is active) --}}
        <div
            x-data="{ editMode: false }"
            x-on:toggle-edit-mode.window="editMode = !editMode"
            x-show="editMode"
            x-cloak
            class="bg-warning/10 border border-warning rounded-lg p-4"
        >
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2">
                    <x-icon name="o-pencil-square" class="w-5 h-5 text-warning" />
                    <span class="font-medium text-warning">Edit Mode Active</span>
                    <span class="text-sm text-base-content/60">Drag widgets to rearrange</span>
                </div>

                <div class="flex flex-wrap gap-2">
                    <x-button
                        label="Add Widget"
                        icon="o-plus"
                        class="btn-sm btn-outline min-h-[2.75rem] sm:min-h-[1.75rem]"
                        @click="$dispatch('open-widget-picker')"
                    />
                    <x-button
                        label="Done"
                        class="btn-sm btn-success min-h-[2.75rem] sm:min-h-[1.75rem]"
                        @click="editMode = false"
                    />
                </div>
            </div>
        </div>

        {{-- Widget Grid --}}
        <div
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6"
            x-data="{ editMode: false }"
            x-on:toggle-edit-mode.window="editMode = !editMode"
        >
            @foreach($widgets as $widget)
                @if($widget['visible'] ?? true)
                    <div
                        class="dashboard-widget-container"
                        data-widget-id="{{ $widget['id'] }}"
                        :class="{ 'cursor-move': editMode }"
                    >
                        @livewire(
                            'dashboard.widgets.' . str_replace('_', '-', $widget['type']),
                            [
                                'config' => $widget['config'],
                                'size' => 'normal',
                                'widgetId' => $widget['id']
                            ],
                            key($widget['id'])
                        )
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Empty State --}}
        @if(empty($widgets) || collect($widgets)->where('visible', true)->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 sm:py-16 text-center">
                <x-icon name="o-squares-2x2" class="w-12 h-12 sm:w-16 sm:h-16 text-base-content/30 mb-4" />
                <h2 class="text-lg sm:text-xl font-semibold text-base-content/60 mb-2">No Widgets Added</h2>
                <p class="text-sm sm:text-base text-base-content/50 mb-6 max-w-md">
                    Get started by adding widgets to your dashboard. Click the "Customize Dashboard" button above.
                </p>
                <x-button
                    label="Add Your First Widget"
                    icon="o-plus"
                    class="btn-primary"
                    @click="$dispatch('toggle-edit-mode'); $dispatch('open-widget-picker')"
                />
            </div>
        @endif

        {{-- Dashboard Manager Modal (will be implemented in Task #23) --}}
        {{-- Placeholder for DashboardManager component --}}
        <div x-data x-on:open-dashboard-manager.window="alert('Dashboard Manager will be available in Task #23')"></div>

        {{-- Widget Picker Modal (will be implemented in Task #24) --}}
        {{-- Placeholder for WidgetConfigurator component --}}
        <div x-data x-on:open-widget-picker.window="alert('Widget Picker will be available in Task #24')"></div>
    </div>
</x-layouts.app>
