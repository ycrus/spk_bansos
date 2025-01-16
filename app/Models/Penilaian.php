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
        'period_id',
        'status',
        'jumlah_penerima'
    ];


    public function period()
    {
        return  $this->belongsTo(Period::class, 'period_id');
    }


    public function dataPenerima(): HasMany
    {
        return $this->hasMany(Calculate_Receiver::class);
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
