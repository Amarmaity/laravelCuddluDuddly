<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class AdminUser extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Guard name (informational)
     * Not required by Laravel, but useful to keep as a reminder.
     */
    protected $guard = 'admin';

    /**
     * The table associated with the model.
     */
    protected $table = 'admin_users';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be type-cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Automatically hash password when setting it.
     * Allows assigning plain text like $admin->password = 'secret';
     */
    public function setPasswordAttribute($value)
    {
        if (empty($value)) {
            return;
        }

        // Only hash if not already hashed (very naive check)
        if (Hash::needsRehash($value)) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * Optional helper: check if admin is active
     */
    public function isActive(): bool
    {
        return (bool)$this->is_active;
    }

    /**
     * Optional helper: quick role check.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
}
