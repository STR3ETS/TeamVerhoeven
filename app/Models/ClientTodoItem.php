<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientTodoItem extends Model
{
    use HasFactory;

    protected $table = 'client_todo_items';

    protected $fillable = [
        'client_user_id',
        'created_by_user_id',
        'completed_by_user_id',
        'label',
        'is_optional',
        'position',
        'completed_at',
        'due_date',
        'notes',
        'source',
        'package',
        'duration_weeks',
    ];

    protected $casts = [
        'is_optional'    => 'boolean',
        'completed_at'   => 'datetime',
        'due_date'       => 'date',
        'position'       => 'integer',
        'duration_weeks' => 'integer',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by_user_id');
    }

    /** Scope: open items voor een client */
    public function scopeOpen($q)
    {
        return $q->whereNull('completed_at');
    }

    /** Scope: gesorteerd op position, id */
    public function scopeOrdered($q)
    {
        return $q->orderBy('position')->orderBy('id');
    }
}
