<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallsignChangeRequest extends Model
{
    protected $fillable = [
        'user_id',
        'old_callsign',
        'new_callsign',
        'reason',
        'status',
        'reviewed_by',
        'admin_notes',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function approve(User $admin, ?string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $admin->id,
            'admin_notes' => $notes,
            'reviewed_at' => now(),
        ]);

        // Update user's callsign
        $this->user->update(['call_sign' => $this->new_callsign]);

        // Audit log
        AuditLog::log(
            'user.callsign.changed',
            $admin->id,
            $this->user,
            ['call_sign' => $this->old_callsign],
            ['call_sign' => $this->new_callsign],
            true
        );
    }

    public function reject(User $admin, ?string $notes = null): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $admin->id,
            'admin_notes' => $notes,
            'reviewed_at' => now(),
        ]);

        AuditLog::log(
            'user.callsign.change_rejected',
            $admin->id,
            $this->user
        );
    }
}
