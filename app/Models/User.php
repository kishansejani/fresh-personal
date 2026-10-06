<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'role',
        'role_id',
        'language',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function isAdmin(): bool
    {
        if (in_array($this->role, ['admin', 'super_admin'])) {
            return true;
        }
        if ($this->roleModel && in_array($this->roleModel->name, ['admin', 'super_admin'])) {
            return true;
        }
        if ($this->roleModel && !in_array($this->roleModel->name, ['user', 'customer'])) {
            return $this->roleModel->permissions()->exists();
        }
        return false;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin' || ($this->roleModel && $this->roleModel->name === 'super_admin');
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        if ($this->roleModel) {
            return $this->roleModel->hasPermission($permission);
        }
        return false;
    }
}
