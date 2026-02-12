<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'type',
        'title',
        'description',
        'file_path',
        'original_name',
        'mime_type',
        'size',
        'visibility',
        'is_archived',
        'archived_at',
        'archived_by',
        'uploaded_by',
        'subject_type',
        'subject_id',
        'view_roles',
        'view_users',
        'blocked_users',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'archived_at' => 'datetime',
        'view_roles' => 'array',
        'view_users' => 'array',
        'blocked_users' => 'array',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function archiver()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class)->orderBy('version', 'desc');
    }

    /**
     * Determine if the given user can view this document based on
     * visibility, roles, and per-user ACL rules.
     */
    public function canBeViewedBy(?User $user): bool
    {
        // Archived documents are only visible to privileged admins
        if ($this->is_archived) {
            if (!$user) {
                return false;
            }

            if ($user->isSuperAdmin() || $user->hasAdminRole('finance_admin') || $user->hasAdminRole('chairman')) {
                return true;
            }

            return false;
        }

        // Public link documents can be viewed by anyone unless explicitly blocked
        if ($this->visibility === 'public_link') {
            if (!$user) {
                return true;
            }

            $blockedUsers = collect($this->blocked_users ?? []);
            if ($blockedUsers->contains($user->id)) {
                return false;
            }

            return true;
        }

        // No user => cannot view non-public documents
        if (!$user) {
            return false;
        }

        // Super admin / key finance roles can always view
        if ($user->isSuperAdmin() || $user->hasAdminRole('finance_admin') || $user->hasAdminRole('chairman')) {
            return true;
        }

        // Explicitly blocked users cannot view
        $blockedUsers = collect($this->blocked_users ?? []);
        if ($blockedUsers->contains($user->id)) {
            return false;
        }

        // Explicit per-user allow list
        $viewUsers = collect($this->view_users ?? []);
        if ($viewUsers->isNotEmpty() && $viewUsers->contains($user->id)) {
            return true;
        }

        // Role-based allow list (matches admin role names)
        $viewRoles = collect($this->view_roles ?? []);
        if ($viewRoles->isNotEmpty() && $user->is_admin) {
            if (!$user->relationLoaded('adminRoles')) {
                $user->load('adminRoles');
            }

            $userRoleNames = $user->adminRoles->pluck('name');
            if ($userRoleNames->intersect($viewRoles)->isNotEmpty()) {
                return true;
            }
        }

        // Fallback to visibility
        if ($this->visibility === 'internal_admin') {
            // Internal admin documents are not visible to partners/members
            return $user->is_admin;
        }

        if ($this->visibility === 'partners') {
            // Visible to partners (and admins) unless blocked
            return $user->is_partner || $user->is_admin;
        }

        return false;
    }
}

