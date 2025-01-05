<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calculate_Receiver extends Model
{
    use HasFactory;

    protected $table = 'calculate__receivers';
    protected $fillable = [
        'penilaian_id',
        'receiver_id',
    ];

    public function penerima()
    {
        return  $this->belongsTo(Receiver::class, 'receiver_id');
    }
}
