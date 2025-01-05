<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaians';
    protected $fillable = [
        'period_id'
    ];


    public function period()
    {
        return  $this->belongsTo(Period::class, 'period_id');
    }


    public function dataPenerima(): HasMany
    {
        return $this->hasMany(Calculate_Receiver::class);
    }
}
