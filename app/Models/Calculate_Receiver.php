<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Calculate_Receiver extends Model
{
    use HasFactory;

    protected $table = 'calculate__receivers';
    protected $fillable = [
        'calculate_id',
        'receiver_id',
        'weight',
    ];
}
