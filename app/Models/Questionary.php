<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Questionary extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'cat_photo',
        'phone',
        'tg_name',
    ];
    protected $hidden = [
        'password',
    ];

    protected $filters = [
        'name',
        'email',
        'phone',
        'tg_name',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function isOwner(User $user): bool {
        return $user->id == $this->user()->id;
    }
}
