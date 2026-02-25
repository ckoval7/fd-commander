<?php

use App\Models\Band;
use App\Models\Contact;
use App\Models\Event;
use App\Models\EventConfiguration;
use App\Models\EventType;
use App\Models\Mode;
use App\Models\OperatingClass;
use App\Models\Section;
use App\Services\CabrilloExporter;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeCabrilloConfig(array $configOverrides = [], array $eventOverrides = []): EventConfiguration
{
    $section = Section::factory()->create(['code' => 'CT']);

    $eventType = EventType::firstOrCreate(
        ['code' => 'FD'],
        ['name' => 'Field Day', 'description' => 'ARRL Field Day', 'is_active' => true]
    );

    $opClass = OperatingClass::firstOrCreate(
        ['code' => '2A', 'event_type_id' => $eventType->id],
        ['name' => 'Class 2A', 'description' => 'Two transmitters, non-emergency power']
    );

    $event = Event::factory()->create(array_merge([
        'start_time' => now()->subHours(12),
        'end_time' => now()->addHours(12),
    ], $eventOverrides));

    return EventConfiguration::factory()->create(array_merge([
        'event_id' => $event->id,
        'callsign' => 'W1AW',
        'club_name' => 'Anytown ARC',
        'section_id' => $section->id,
        'operating_class_id' => $opClass->id,
        'max_power_watts' => 100,
    ], $configOverrides));
}

it('includes required cabrillo header fields', function () {
    $config = makeCabrilloConfig();
    $output = app(CabrilloExporter::class)->export($config);

    expect($output)
        ->toContain('START-OF-LOG: 3.0')
        ->toContain('CREATED-BY: FD Log DB')
        ->toContain('CONTEST: ARRL-FIELD-DAY')
        ->toContain('CALLSIGN: W1AW')
        ->toContain('LOCATION: CT')
        ->toContain('CATEGORY-OPERATOR: 2A')
        ->toContain('CLUB: Anytown ARC')
        ->toContain('END-OF-LOG:');
});

it('maps power to HIGH when over 100 watts', function () {
    $config = makeCabrilloConfig(['max_power_watts' => 1500]);
    $output = app(CabrilloExporter::class)->export($config);
    expect($output)->toContain('CATEGORY-POWER: HIGH');
});

it('maps power to LOW for 6-100 watts', function () {
    $config = makeCabrilloConfig(['max_power_watts' => 100]);
    $output = app(CabrilloExporter::class)->export($config);
    expect($output)->toContain('CATEGORY-POWER: LOW');
});

it('maps power to QRP for 5 watts or less', function () {
    $config = makeCabrilloConfig(['max_power_watts' => 5]);
    $output = app(CabrilloExporter::class)->export($config);
    expect($output)->toContain('CATEGORY-POWER: QRP');
});

it('formats a CW qso line correctly', function () {
    $config = makeCabrilloConfig();
    $band = Band::factory()->create(['name' => '20m', 'frequency_mhz' => 14.0]);
    $mode = Mode::factory()->create(['name' => 'CW', 'category' => 'CW']);

    Contact::factory()->create([
        'event_configuration_id' => $config->id,
        'band_id' => $band->id,
        'mode_id' => $mode->id,
        'qso_time' => '2025-06-28 14:23:00',
        'callsign' => 'K1ABC',
        'received_exchange' => '1A ME',
        'is_duplicate' => false,
    ]);

    $output = app(CabrilloExporter::class)->export($config);

    expect($output)->toContain('QSO: 14000 CW 2025-06-28 1423 W1AW 2A CT K1ABC 1A ME');
});

it('maps phone mode to PH', function () {
    $config = makeCabrilloConfig();
    $band = Band::factory()->create(['name' => '20m', 'frequency_mhz' => 14.0]);
    $mode = Mode::factory()->create(['name' => 'Phone', 'category' => 'Phone']);

    Contact::factory()->create([
        'event_configuration_id' => $config->id,
        'band_id' => $band->id,
        'mode_id' => $mode->id,
        'qso_time' => '2025-06-28 14:23:00',
        'callsign' => 'K1ABC',
        'received_exchange' => '1A ME',
        'is_duplicate' => false,
    ]);

    $output = app(CabrilloExporter::class)->export($config);
    expect($output)->toContain('QSO: 14000 PH 2025-06-28 1423');
});

it('maps digital mode to DG', function () {
    $config = makeCabrilloConfig();
    $band = Band::factory()->create(['name' => '20m', 'frequency_mhz' => 14.0]);
    $mode = Mode::factory()->create(['name' => 'Digital', 'category' => 'Digital']);

    Contact::factory()->create([
        'event_configuration_id' => $config->id,
        'band_id' => $band->id,
        'mode_id' => $mode->id,
        'qso_time' => '2025-06-28 14:30:00',
        'callsign' => 'N2XYZ',
        'received_exchange' => '3A NNY',
        'is_duplicate' => false,
    ]);

    $output = app(CabrilloExporter::class)->export($config);
    expect($output)->toContain('QSO: 14000 DG 2025-06-28 1430');
});

it('excludes duplicate contacts from qso lines', function () {
    $config = makeCabrilloConfig();
    $band = Band::factory()->create(['frequency_mhz' => 14.0]);
    $mode = Mode::factory()->create(['category' => 'CW']);

    Contact::factory()->create([
        'event_configuration_id' => $config->id,
        'band_id' => $band->id,
        'mode_id' => $mode->id,
        'callsign' => 'K1DUPE',
        'is_duplicate' => true,
    ]);

    $output = app(CabrilloExporter::class)->export($config);
    expect($output)->not->toContain('K1DUPE');
});

it('generates a correct filename', function () {
    $config = makeCabrilloConfig([], ['start_time' => '2025-06-28 12:00:00']);
    $filename = app(CabrilloExporter::class)->filename($config);
    expect($filename)->toBe('w1aw-2025-field-day.log');
});
