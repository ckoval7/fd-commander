<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventConfiguration extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'event_id',
        'created_by_user_id',
        'callsign',
        'club_name',
        'logo_path',
        'tagline',
        'is_active',
        'section_id',
        'operating_class_id',
        'transmitter_count',
        'has_gota_station',
        'gota_callsign',
        'max_power_watts',
        'power_multiplier',
        'uses_commercial_power',
        'uses_generator',
        'uses_battery',
        'uses_solar',
        'uses_wind',
        'uses_water',
        'uses_methane',
        'uses_other_power',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'has_gota_station' => 'boolean',
            'uses_commercial_power' => 'boolean',
            'uses_generator' => 'boolean',
            'uses_battery' => 'boolean',
            'uses_solar' => 'boolean',
            'uses_wind' => 'boolean',
            'uses_water' => 'boolean',
            'uses_methane' => 'boolean',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function operatingClass(): BelongsTo
    {
        return $this->belongsTo(OperatingClass::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
