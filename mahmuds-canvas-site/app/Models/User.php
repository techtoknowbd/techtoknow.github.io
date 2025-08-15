<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    // Relationships
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function profile(): HasMany
    {
        return $this->hasOne(UserProfile::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    public function cards(): HasMany
    {
        return $this->hasOne(UserCard::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    public function logins(): HasMany
    {
        return $this->hasMany(UserLogin::class);
    }

    public function logouts(): HasMany
    {
        return $this->hasMany(UserLogout::class);
    }

    public function passwordResets(): HasMany
    {
        return $this->hasMany(UserPasswordReset::class);
    }

    public function emailVerifications(): HasMany
    {
        return $this->hasMany(UserEmailVerification::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(UserSupport::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(UserFeedback::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(UserReport::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(UserBlock::class);
    }

    public function bans(): HasMany
    {
        return $this->hasMany(UserBan::class);
    }

    public function warnings(): HasMany
    {
        return $this->hasMany(UserWarning::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(UserNote::class);
    }

    // Helper methods
    public function hasRole($role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasPermission($permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists() ||
               $this->roles()->whereHas('permissions', function($query) use ($permission) {
                   $query->where('name', $permission);
               })->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isModerator(): bool
    {
        return $this->hasRole('moderator');
    }

    public function getFullNameAttribute(): string
    {
        if ($this->profile && $this->profile->first_name && $this->profile->last_name) {
            return $this->profile->first_name . ' ' . $this->profile->last_name;
        }
        return $this->name;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->profile && $this->profile->avatar) {
            return asset('storage/' . $this->profile->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }
}
