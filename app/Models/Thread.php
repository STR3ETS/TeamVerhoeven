<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thread extends Model
{
    protected $fillable = [
        'client_user_id',
        'coach_user_id',
        'subject',
    ];

    /** Berichten binnen deze thread */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /** De client als User */
    public function clientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    /** De coach als User */
    public function coachUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_user_id');
    }
}
