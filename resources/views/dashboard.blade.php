<x-layouts.app>
    <x-slot:title>Dashboard</x-slot:title>

    <div class="p-6">
        <h1 class="text-3xl font-bold mb-6">Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Stats cards will go here --}}
            <x-stat
                title="Total QSOs"
                value="0"
                icon="o-signal"
                class="bg-primary text-primary-content"
            />

            <x-stat
                title="Current Score"
                value="0"
                icon="o-trophy"
                class="bg-secondary text-secondary-content"
            />

            <x-stat
                title="Active Stations"
                value="0"
                icon="o-radio"
                class="bg-accent text-accent-content"
            />

            <x-stat
                title="Operators"
                value="0"
                icon="o-user-group"
                class="bg-neutral text-neutral-content"
            />
        </div>

        <div class="mt-8">
            <x-placeholder-page title="Dashboard Content Coming Soon" icon="o-home" />
        </div>
    </div>
</x-layouts.app>
