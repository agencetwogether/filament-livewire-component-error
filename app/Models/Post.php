<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
    ];

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function mailings(): MorphMany
    {
        return $this->morphMany(Mailing::class, 'mailable');
    }

    /**
     * @return MorphOne<Cancelation>
     */
    public function cancelation(): MorphOne
    {
        return $this->morphOne(Cancelation::class, 'model');
    }
}
