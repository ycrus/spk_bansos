<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Result extends Model
{
    use HasFactory;

    protected $table = 'results';
    protected $fillable = [
        'period_id',
    ];


    public function period()
    {
        return  $this->belongsTo(Period::class, 'period_id');
    }


    public function nilaiParameter(): HasMany
    {
        return $this->hasMany(NilaiBobot::class);
    }

    public function nilaiUtility(): HasMany
    {
        return $this->hasMany(NilaiUtility::class);
    }

    public function nilaiAkhir(): HasMany
    {
        return $this->hasMany(NilaiAkhir::class);
    }

    public function ranking(): HasMany
    {
        return $this->hasMany(Rangking::class);
    }

    public function result(): HasMany
    {
        return $this->hasMany(Rangking::class);
    }
}
