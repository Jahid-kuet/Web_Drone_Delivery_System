<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'module',
        'action',
        'resource',
        'is_system_permission',
        'is_active',
        'conditions',
        'metadata'
    ];

    protected $casts = [
        'conditions' => 'array',
        'metadata' => 'array',
        'is_system_permission' => 'boolean',
        'is_active' => 'boolean'
    ];

    protected $dates = ['deleted_at'];

    // Action constants
    const ACTION_CREATE = 'create';
    const ACTION_READ = 'read';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_APPROVE = 'approve';
    const ACTION_ASSIGN = 'assign';
    const ACTION_MONITOR = 'monitor';
    const ACTION_REPORT = 'report';

    // Module constants
    const MODULE_DRONES = 'drones';
    const MODULE_DELIVERIES = 'deliveries';
    const MODULE_HOSPITALS = 'hospitals';
    const MODULE_SUPPLIES = 'supplies';
    const MODULE_USERS = 'users';
    const MODULE_REPORTS = 'reports';
    const MODULE_SETTINGS = 'settings';
    const MODULE_AUDIT = 'audit';

    /**
     * Get roles that have this permission
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
                    ->withPivot('granted_at', 'is_active', 'conditions')
                    ->withTimestamps();
    }

    /**
     * Get role permission assignments
     */
    public function rolePermissions(): HasMany
    {
        return $this->hasMany(RolePermission::class);
    }

    /**
     * Scope for active permissions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for system permissions
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system_permission', true);
    }

    /**
     * Scope for custom permissions
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system_permission', false);
    }

    /**
     * Scope by module
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Get full permission string
     */
    public function getFullPermissionAttribute(): string
    {
        $permission = $this->module . '.' . $this->action;
        
        if ($this->resource) {
            $permission .= '.' . $this->resource;
        }
        
        return $permission;
    }

    /**
     * Check if permission applies to specific resource
     */
    public function appliesToResource($resource): bool
    {
        return !$this->resource || $this->resource === $resource;
    }

    /**
     * Check conditions for permission
     */
    public function checkConditions($context = []): bool
    {
        if (!$this->conditions) return true;
        
        foreach ($this->conditions as $condition => $value) {
            if (!isset($context[$condition]) || $context[$condition] !== $value) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get role count
     */
    public function getRoleCountAttribute(): int
    {
        return $this->roles()->wherePivot('is_active', true)->count();
    }

    /**
     * Get permission display name
     */
    public function getDisplayNameAttribute(): string
    {
        return ucwords(str_replace(['_', '.'], ' ', $this->name));
    }

    /**
     * Create permission from string
     */
    public static function createFromString($permissionString, $description = null): self
    {
        $parts = explode('.', $permissionString);
        
        $module = $parts[0] ?? 'general';
        $action = $parts[1] ?? 'read';
        $resource = $parts[2] ?? null;
        
        return static::create([
            'name' => $permissionString,
            'slug' => $permissionString,
            'description' => $description ?? "Permission for {$permissionString}",
            'module' => $module,
            'action' => $action,
            'resource' => $resource
        ]);
    }

    /**
     * Get actions array
     */
    public static function getActions(): array
    {
        return [
            self::ACTION_CREATE => 'Create',
            self::ACTION_READ => 'Read',
            self::ACTION_UPDATE => 'Update',
            self::ACTION_DELETE => 'Delete',
            self::ACTION_APPROVE => 'Approve',
            self::ACTION_ASSIGN => 'Assign',
            self::ACTION_MONITOR => 'Monitor',
            self::ACTION_REPORT => 'Report'
        ];
    }

    /**
     * Get modules array
     */
    public static function getModules(): array
    {
        return [
            self::MODULE_DRONES => 'Drones',
            self::MODULE_DELIVERIES => 'Deliveries',
            self::MODULE_HOSPITALS => 'Hospitals',
            self::MODULE_SUPPLIES => 'Medical Supplies',
            self::MODULE_USERS => 'Users',
            self::MODULE_REPORTS => 'Reports',
            self::MODULE_SETTINGS => 'Settings',
            self::MODULE_AUDIT => 'Audit Logs'
        ];
    }
}
