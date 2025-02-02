<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tg_name',
        'phone'
    ];

    protected $filters = [
        'name',
        'email',
        'phone',
        'tg_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'role' => $this->role,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function questionaries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Questionary::class);
    }

    public static function createFromQuestionary(Questionary $questionary)
    {

        return self::create([
            'name' => $questionary->name,
            'email' => $questionary->email,
            'password' => $questionary->password,
            'tg_name' => $questionary->tg_name,
            'phone' => $questionary->phone,
        ]);
    }

    public function getFilters()
    {
        return $this->filters;
    }
}
