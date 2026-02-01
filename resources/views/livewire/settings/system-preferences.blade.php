<div class="space-y-6">
    <x-card>
        <x-slot:title>Regional Settings</x-slot:title>

        <div class="space-y-4">
            <x-select
                label="Timezone"
                wire:model.live="timezone"
                required
                icon="o-globe-americas"
                :options="collect(timezone_identifiers_list())->mapWithKeys(fn($tz) => [$tz => $tz])->toArray()"
                searchable
            />

            <x-select
                label="Date Format"
                wire:model.live="date_format"
                required
                icon="o-calendar"
                :options="[
                    'Y-m-d' => '2026-02-01 (ISO)',
                    'm/d/Y' => '02/01/2026 (US)',
                    'd/m/Y' => '01/02/2026 (EU)',
                ]"
            />

            <x-select
                label="Time Format"
                wire:model.live="time_format"
                required
                icon="o-clock"
                :options="[
                    'H:i:s' => '14:30:00 (24-hour)',
                    'h:i:s A' => '02:30:00 PM (12-hour)',
                ]"
            />

            <x-alert class="alert-info">
                <div class="text-sm">
                    <strong>Preview:</strong> {{ $this->preview }}
                </div>
            </x-alert>
        </div>
    </x-card>

    <x-card>
        <x-slot:title>Contact Information</x-slot:title>

        <x-input
            label="Contact Email"
            type="email"
            wire:model="contact_email"
            icon="o-envelope"
            hint="Public contact email for the site"
        />
    </x-card>

    <x-card>
        <x-slot:title>API Settings</x-slot:title>

        <x-input
            label="Callsign Lookup API Key"
            type="text"
            wire:model="api_key"
            icon="o-key"
            hint="Optional - API key for callook.info integration (future feature)"
        />
    </x-card>

    <div class="flex justify-end">
        <x-button wire:click="save" class="btn-primary" icon="o-check">
            Save Preferences
        </x-button>
    </div>
</div>
