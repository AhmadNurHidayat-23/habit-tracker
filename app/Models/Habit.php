<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function completions(): HasMany
    {
        return $this->hasMany(Completion::class)->orderByDesc('completed_on');
    }
}
