<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalonPenerima extends Model
{
    use HasFactory;

    protected $table = 'calon_penerima';
    protected $fillable = [
        'penilaian_id',
        'receiver_id',
    ];

    public function penerima()
    {
        return  $this->belongsTo(Receiver::class, 'receiver_id');
    }

}
