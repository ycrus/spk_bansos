<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    use HasFactory;

    protected $table = 'parameters';
    protected $fillable = [
        'title',
        'operation',
        'start',
        'end',
        'unit',
        'description',
        'parameter_weight',
        'criteria_id'
    ];

    public function criteria()
    {
        return  $this->belongsTo(Criteria::class, 'criteria_id');
    }
}
