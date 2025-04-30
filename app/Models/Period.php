<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Period extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'periods';
    protected $fillable = [
        'name',
        'program_id',
        'status',
        'description'
    ];


    public function program()
    {
        return  $this->belongsTo(Program::class, 'program_id')->withTrashed();
    }

    public function programActive()
    {
        return  $this->belongsTo(Program::class, 'program_id')->withoutTrashed();
    }
}
