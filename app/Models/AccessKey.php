<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AccessKey extends Model
{
    protected $fillable = [
        'key','package','duration_weeks','active','uses_limit','uses_count','valid_until'
    ];

    protected $casts = [
        'active' => 'boolean',
        'valid_until' => 'datetime',
    ];

    public function isUsable(): bool
    {
        if (!$this->active) return false;
        if ($this->valid_until instanceof Carbon && now()->greaterThan($this->valid_until)) return false;
        if (!is_null($this->uses_limit) && $this->uses_count >= $this->uses_limit) return false;
        return true;
    }
}
