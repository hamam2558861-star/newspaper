<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'job_title', 'bio', 'avatar', 'phone', 'twitter', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->name === 'super_admin';
    }

    public function hasPermission(string $permission): bool
    {
        return $this->is_active && ($this->role?->hasPermission($permission) ?? false);
    }

    /** هل للمستخدم أي صلاحية دخول للوحة التحكم؟ */
    public function canAccessAdmin(): bool
    {
        return $this->is_active && $this->role && ! empty($this->role->permissions);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? upload_url($this->avatar)
            : 'https://ui-avatars.com/api/?background=0f2a47&color=fff&bold=true&name=' . urlencode($this->name);
    }
}
