<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Mailing extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function mailable(): MorphTo
    {
        return $this->morphTo();
    }
}
