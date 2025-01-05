<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Program_Criteria extends Model
{
    use HasFactory;

    protected $table = 'program_criterias';
    protected $fillable = [
        'criteria_id',
        'program_id',
        'weight',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Ambil total weight untuk program_id saat ini, kecuali yang sedang diedit
            $totalWeight = static::where('program_id', $model->program_id)
                ->where('id', '!=', $model->id) // Exclude the current record if updating
                ->sum('weight');

            // Tambahkan weight dari item yang sedang disimpan
            $totalWeight += $model->weight;

            // Validasi: Total weight tidak boleh melebihi 100
            if ($totalWeight > 100) {
                throw ValidationException::withMessages([
                    'weight' => 'The total weight for this program must not exceed 100%.',
                ]);
            }
        });
    }
}
