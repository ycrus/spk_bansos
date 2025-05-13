<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Criteria extends Model
{
    use HasFactory;

    protected $table = 'criterias';
    protected $fillable = [
        'title',
        'unit',
        'description',
        'is_active',
    ];

    public function programs(): BelongsToMany
    {
        return  $this->belongsToMany(Program::class, 'program_criterias', 'program_id')->withPivot(['weight'])->withTimestamps();
    }
}
