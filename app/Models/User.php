<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_partner',
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
            'is_admin' => 'boolean',
            'is_partner' => 'boolean',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Admin roles for this user (super_admin, finance_admin, store_admin, etc.).
     */
    public function adminRoles()
    {
        return $this->belongsToMany(AdminRole::class, 'admin_role_user')
            ->withTimestamps();
    }

    public function partner()
    {
        return $this->hasOne(Partner::class);
    }

    public function isSuperAdmin(): bool
    {
        if (!$this->is_admin) {
            return false;
        }
        
        // Load roles if not already loaded
        if (!$this->relationLoaded('adminRoles')) {
            $this->load('adminRoles');
        }
        
        return $this->adminRoles->contains('name', 'super_admin');
    }

    public function hasAdminRole(string $role): bool
    {
        if (!$this->is_admin) {
            return false;
        }
        
        // Load roles if not already loaded
        if (!$this->relationLoaded('adminRoles')) {
            $this->load('adminRoles');
        }
        
        return $this->adminRoles->contains('name', $role);
    }
}
