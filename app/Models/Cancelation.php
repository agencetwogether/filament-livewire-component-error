<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cancelation extends Model
{
    protected $fillable = [
        'causer',
        'date',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }
}
