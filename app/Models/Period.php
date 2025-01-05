<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasFactory;

    protected $table = 'periods';
    protected $fillable = [
        'name',
        'program_id',
        'status',
        'description'
    ];


    public function program()
    {
        return  $this->belongsTo(Program::class, 'program_id');
    }
}
