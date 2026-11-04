<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Completion extends Model
{
    use HasFactory;

    protected $fillable = ['completed_on'];

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}
