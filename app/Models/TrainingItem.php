<?php
// app/Models/TrainingItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingItem extends Model
{
    protected $table = 'training_items';

    protected $fillable = ['training_block_id','left_html','right_text','sort_order'];

    public function block()
    {
        return $this->belongsTo(TrainingBlock::class, 'training_block_id');
    }
}
