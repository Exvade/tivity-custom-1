<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wish extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invitation_id', 'guest_id', 'guest_name', 'attendance', 'guest_count',
        'message', 'status', 'moderation_reason', 'ip_hash', 'user_agent',
        'moderated_by', 'moderated_at',
    ];

    protected function casts(): array
    {
        return ['moderated_at' => 'datetime'];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }
}
