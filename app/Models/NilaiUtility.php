<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiUtility extends Model
{
    use HasFactory;
    protected $table = 'nilai_utilities';
    protected $fillable = [
        'penilaian_id',
        'receiver_id',
        'kelurahan',
        'pekerjaan',
        'penghasilan',
        'status_tempat_tinggal',
        'status_perkawinan',
        'jumlah_tanggungan',
        'keadaan_rumah',
        'disabilitas',
        'pendidikan',
        'fasilitas_mck',
        'bahan_bakar_harian',
        'kepemilikan_kendaraan',
    ];

    public function penerima()
    {
        return  $this->belongsTo(Receiver::class, 'receiver_id');
    }
}
