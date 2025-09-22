<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'position',
        'company',
        'avatar_url',
        'last_seen',
        'role',
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
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password'         => 'hashed',
            'last_seen'        => 'datetime',
        ];
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    public function preference(): HasOne {
        return $this->hasOne(UserPreference::class);
    }

    public function expertise(): BelongsToMany {
        return $this->belongsToMany(Expertise::class, 'user_expertises');
    }
    public function conversations(): BelongsToMany {
        return $this->belongsToMany(Conversation::class, 'conversation_participants');
    }

    public function messages(): HasMany {
        return $this->hasMany(Message::class, 'sender_id');
    }
    public function isOnline(): bool {
        return (bool) ($this->last_seen && $this->last_seen->gt(now()->subMinutes(5)));
    }
}
