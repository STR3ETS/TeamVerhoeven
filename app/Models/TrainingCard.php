<?php
// app/Models/TrainingCard.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingCard extends Model
{
    protected $fillable = ['training_section_id','title','sort_order'];

    public function section()
    {
        return $this->belongsTo(TrainingSection::class);
    }

    public function blocks()
    {
        return $this->hasMany(TrainingBlock::class)->orderBy('sort_order');
    }
}
