<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Calculate extends Model
{
    use HasFactory;

    protected $table = 'calculates';
    protected $fillable = [
        'batch',
        'program_id',
    ];

    public function calculateReceiver(): HasMany
    {
        return $this->hasMany(Calculate_Receiver::class);
    }

    public function receiver(): BelongsToMany
    {
        return  $this->belongsToMany(Receiver::class, 'calculate__receivers')->withPivot(['weight'])->withTimestamps();
    }
}
