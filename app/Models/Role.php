<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'level',
        'capabilities',
        'is_system_role',
        'is_active',
        'max_users',
        'metadata'
    ];

    protected $casts = [
        'capabilities' => 'array',
        'metadata' => 'array',
        'is_system_role' => 'boolean',
        'is_active' => 'boolean'
    ];

    protected $dates = ['deleted_at'];

    // Level constants
    const LEVEL_SUPER_ADMIN = 'super_admin';
    const LEVEL_ADMIN = 'admin';
    const LEVEL_MANAGER = 'manager';
    const LEVEL_OPERATOR = 'operator';
    const LEVEL_STAFF = 'staff';
    const LEVEL_USER = 'user';

    /**
     * Get users with this role
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')
                    ->withPivot('assigned_at', 'expires_at', 'is_active')
                    ->withTimestamps();
    }

    /**
     * Get permissions for this role
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
                    ->withPivot('granted_at', 'is_active', 'conditions')
                    ->withTimestamps();
    }

    /**
     * Get user role assignments
     */
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * Scope for active roles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for system roles
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system_role', true);
    }

    /**
     * Scope for non-system roles
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system_role', false);
    }

    /**
     * Scope by level
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Check if role has permission
     */
    public function hasPermission($permission): bool
    {
        if (is_string($permission)) {
            return $this->permissions()->where('slug', $permission)->exists();
        }
        
        if ($permission instanceof Permission) {
            return $this->permissions()->where('id', $permission->id)->exists();
        }
        
        return false;
    }

    /**
     * Grant permission to role
     */
    public function grantPermission(Permission $permission, $grantedBy = null): bool
    {
        if ($this->hasPermission($permission)) {
            return true;
        }
        
        $this->permissions()->attach($permission->id, [
            'granted_by_user_id' => $grantedBy,
            'granted_at' => now(),
            'is_active' => true
        ]);
        
        return true;
    }

    /**
     * Revoke permission from role
     */
    public function revokePermission(Permission $permission): bool
    {
        $this->permissions()->detach($permission->id);
        return true;
    }

    /**
     * Check if role can be assigned to more users
     */
    public function canAssignMoreUsers(): bool
    {
        if (!$this->max_users) return true;
        
        $currentCount = $this->users()->wherePivot('is_active', true)->count();
        return $currentCount < $this->max_users;
    }

    /**
     * Get role hierarchy level (numeric)
     */
    public function getHierarchyLevelAttribute(): int
    {
        $levels = [
            self::LEVEL_SUPER_ADMIN => 100,
            self::LEVEL_ADMIN => 80,
            self::LEVEL_MANAGER => 60,
            self::LEVEL_OPERATOR => 40,
            self::LEVEL_STAFF => 20,
            self::LEVEL_USER => 10
        ];
        
        return $levels[$this->level] ?? 0;
    }

    /**
     * Check if this role is higher than another
     */
    public function isHigherThan(Role $role): bool
    {
        return $this->hierarchy_level > $role->hierarchy_level;
    }

    /**
     * Get user count
     */
    public function getUserCountAttribute(): int
    {
        return $this->users()->wherePivot('is_active', true)->count();
    }

    /**
     * Get permission count
     */
    public function getPermissionCountAttribute(): int
    {
        return $this->permissions()->wherePivot('is_active', true)->count();
    }

    /**
     * Get available levels
     */
    public static function getLevels(): array
    {
        return [
            self::LEVEL_SUPER_ADMIN => 'Super Admin',
            self::LEVEL_ADMIN => 'Admin',
            self::LEVEL_MANAGER => 'Manager',
            self::LEVEL_OPERATOR => 'Operator',
            self::LEVEL_STAFF => 'Staff',
            self::LEVEL_USER => 'User'
        ];
    }
}
