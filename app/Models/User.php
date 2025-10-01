<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Profielen per rol
    public function clientProfile()
    {
        return $this->hasOne(ClientProfile::class, 'user_id');
    }

    public function coachProfile()
    {
        return $this->hasOne(CoachProfile::class, 'user_id');
    }

    // Als coach: alle clients die aan mij gekoppeld zijn
    public function clients()
    {
        return $this->hasMany(ClientProfile::class, 'coach_id')->with('user');
    }

    // Als client: alle intakes
    public function intakes()
    {
        return $this->hasMany(Intake::class, 'client_id');
    }

    // Als client: orders
    public function orders()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    // Helpers
    public function isClient(): bool { return $this->role === 'client'; }
    public function isCoach(): bool  { return $this->role === 'coach'; }
    public function isAdmin(): bool  { return $this->role === 'admin'; }
}
