<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

class Program extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'programs';
    protected $fillable = [
        'name',
    ];

    public function criterias(): BelongsToMany
    {
        return  $this->belongsToMany(Criteria::class, 'program_criterias', 'criteria_id')->withPivot(['weight'])->withTimestamps();
    }

    public function criteriaProgram(): HasMany
    {
        return $this->hasMany(Program_Criteria::class);
    }
}
