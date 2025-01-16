<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rangking extends Model
{
    use HasFactory;
    protected $table = 'rangkings';
    protected $fillable = [
        'penilaian_id',
        'receiver_id',
        'rangking',
        'total',
        'is_ranked',
        'status',
    ];

    public function penerima()
    {
        return  $this->belongsTo(Receiver::class, 'receiver_id');
    }

    public function getIsBansosStatus()
    {
        return $this->is_ranked ? 'Yes' : 'No';
    }
}
