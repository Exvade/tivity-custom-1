<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guest extends Model
{
    protected $fillable = ['invitation_id', 'name', 'token', 'max_submissions', 'submission_count', 'last_submitted_at'];

    protected function casts(): array
    {
        return ['last_submitted_at' => 'datetime'];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function wishes(): HasMany
    {
        return $this->hasMany(Wish::class);
    }
}
