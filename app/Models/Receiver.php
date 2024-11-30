<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Receiver extends Model
{
    use HasFactory;

    protected $table = 'receivers';
    protected $fillable = [
        'nama',
        'nik',
        'tanggal_lahir',
        'rt',
        'rw',
        'kelurahan',
        'pekerjaan',
        'penghasilan',
        'status_tempat_tinggal',
        'status_perkawinan',
        'jumlah_tanggungan',
        'status_tempat_tinggal',
        'keadaan_rumah',
        'disabilitas',
        'pendidikan',
        'fasilitas_mck',
        'bahan_bakar_harian',
        'kepemilikan_kendaraan',
    ];

    public function umur(): Attribute
    {
        return Attribute::make(
            get: fn() => Carbon::parse($this->tanggal_lahir)->age,
        );
    }


    public function calculates(): BelongsToMany
    {
        return  $this->belongsToMany(Calculate::class, 'calculate__receivers')->withPivot(['weight'])->withTimestamps();
    }
}
