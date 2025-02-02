<?php

namespace App\Models;

use Database\Factories\QuestionaryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Questionary extends Model
{
    /** @use HasFactory<QuestionaryFactory> */
    use HasFactory;
    use SoftDeletes;

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

    public function isOwner(User $user): bool
    {
        /** @var User|null $currentUser */
        $currentUser = Auth::user();
        return $user->id === $currentUser->id;
    }

    public function getCatPhotoUrlAttribute()
    {
        return !is_null($this->cat_photo) ? Storage::url($this->cat_photo) : null;
    }
}
