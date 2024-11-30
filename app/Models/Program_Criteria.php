<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program_Criteria extends Model
{
    use HasFactory;

    protected $table = 'program_criterias';
    protected $fillable = [
        'criteria_id',
        'program_id',
        'weight',
    ];
}
