<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionRenewal extends Model
{
    protected $fillable = [
        'user_id',
        'first_renewed_at',
    ];

    protected $casts = [
        'first_renewed_at' => 'datetime',
    ];

    /**
     * De gebruiker die verlengd heeft.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check of een user ooit verlengd heeft.
     */
    public static function hasRenewed(int $userId): bool
    {
        return self::where('user_id', $userId)->exists();
    }

    /**
     * Registreer een verlenging voor een user.
     */
    public static function recordRenewal(int $userId): self
    {
        return self::create([
            'user_id' => $userId,
            'first_renewed_at' => now(),
        ]);
    }
}
