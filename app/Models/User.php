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

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
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

    public function member()
    {
        return $this->hasOne(Member::class);
    }

    /**
     * All admin roles attached to this user.
     */
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

    /**
     * Check if user has a given permission (via any of their roles).
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!$this->is_admin) {
            return false;
        }

        if (!$this->relationLoaded('adminRoles')) {
            $this->load('adminRoles.permissions');
        } else {
            $this->loadMissing('adminRoles.permissions');
        }

        foreach ($this->adminRoles as $role) {
            if ($role->permissions->contains('name', $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Projects this user is assigned to manage
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Check if user is assigned to a project
     */
    public function isAssignedToProject($projectId): bool
    {
        return $this->projects()->where('project_id', $projectId)->exists();
    }

    /**
     * Get user's role for a specific project
     */
    public function getProjectRole($projectId): ?string
    {
        $assignment = $this->projects()->where('project_id', $projectId)->first();
        return $assignment ? $assignment->pivot->role : null;
    }
}
