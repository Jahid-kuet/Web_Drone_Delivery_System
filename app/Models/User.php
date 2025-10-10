<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
        'phone',
        'address',
        'city',
        'state_province',
        'postal_code',
        'country',
        'profile_photo',
        'hospital_id',
        'license_expiry_date',
        'emergency_contact_name',
        'emergency_contact_phone',
        'status',
        'last_active_at',
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
            'license_expiry_date' => 'date',
            'last_active_at' => 'datetime',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the roles assigned to the user
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withTimestamps();
    }

    /**
     * Get the hospital this user belongs to
     */
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Get the delivery requests created by this user
     */
    public function deliveryRequests(): HasMany
    {
        return $this->hasMany(DeliveryRequest::class, 'requested_by_user_id');
    }

    /**
     * Get the delivery requests approved by this user
     */
    public function approvedDeliveryRequests(): HasMany
    {
        return $this->hasMany(DeliveryRequest::class, 'approved_by_user_id');
    }

    /**
     * Get the deliveries assigned to this user (pilot)
     */
    public function assignedDeliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'assigned_pilot_id');
    }

    /**
     * Get the deliveries confirmed by this user
     */
    public function confirmedDeliveries(): HasMany
    {
        return $this->hasMany(DeliveryConfirmation::class, 'confirmed_by_user_id');
    }

    /**
     * Get all notifications for this user
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    /**
     * Get audit logs for this user's actions
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    /**
     * Get audit logs where this user is the subject
     */
    public function auditableRecords(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    // ==================== ROLE & PERMISSION METHODS ====================

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if user has a specific role by slug
     */
    public function hasRoleSlug(string $roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    /**
     * Check if user has any of the given role slugs
     */
    public function hasAnyRoleSlug(array $roleSlugs): bool
    {
        return $this->roles()->whereIn('slug', $roleSlugs)->exists();
    }

    /**
     * Check if user has all of the given roles
     */
    public function hasAllRoles(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->count() === count($roleNames);
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permissionName): bool
    {
        // Super admin has all permissions
        if ($this->hasRole('super_admin')) {
            return true;
        }

        // Check if any of the user's roles have this permission
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionName) {
                $query->where('name', $permissionName);
            })
            ->exists();
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissionNames): bool
    {
        if ($this->hasRole('super_admin')) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionNames) {
                $query->whereIn('name', $permissionNames);
            })
            ->exists();
    }

    /**
     * Assign a role to the user
     */
    public function assignRole(string|Role $role): self
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        if (!$this->hasRole($role->name)) {
            $this->roles()->attach($role->id);
        }

        return $this;
    }

    /**
     * Remove a role from the user
     */
    public function removeRole(string|Role $role): self
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
            if (!$role) {
                return $this;
            }
        }

        $this->roles()->detach($role->id);

        return $this;
    }

    /**
     * Sync user roles
     */
    public function syncRoles(array $roles): self
    {
        $roleIds = collect($roles)->map(function ($role) {
            if ($role instanceof Role) {
                return $role->id;
            }
            return Role::where('name', $role)->firstOrFail()->id;
        });

        $this->roles()->sync($roleIds);

        return $this;
    }

    // ==================== STATUS & ACTIVITY METHODS ====================

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is suspended
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Check if user is a drone pilot
     */
    public function isDronePilot(): bool
    {
        return $this->hasAnyRole(['drone_operator', 'admin', 'super_admin']);
    }

    /**
     * Check if pilot license is valid
     */
    public function hasValidLicense(): bool
    {
        if (!$this->license_expiry_date) {
            return true;
        }

        return $this->license_expiry_date->isFuture();
    }

    /**
     * Check if pilot license is expiring soon (within 30 days)
     */
    public function licenseExpiringSoon(): bool
    {
        if (!$this->license_expiry_date) {
            return false;
        }

        return $this->license_expiry_date->isFuture() 
            && $this->license_expiry_date->diffInDays(now()) <= 30;
    }

    /**
     * Update user's last active timestamp
     */
    public function updateLastActive(): void
    {
        $this->update(['last_active_at' => now()]);
    }

    /**
     * Get user's full address
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    // ==================== QUERY SCOPES ====================

    /**
     * Scope: Active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Suspended users
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    /**
     * Scope: Users with specific role
     */
    public function scopeWithRole($query, string $roleName)
    {
        return $query->whereHas('roles', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    /**
     * Scope: Users from specific hospital
     */
    public function scopeFromHospital($query, int $hospitalId)
    {
        return $query->where('hospital_id', $hospitalId);
    }

    /**
     * Scope: Drone pilots (users with drone_operator role)
     */
    public function scopeDronePilots($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('slug', 'drone_operator');
        });
    }

    /**
     * Scope: Users with expiring licenses
     */
    public function scopeExpiringLicenses($query, int $days = 30)
    {
        return $query->whereNotNull('license_expiry_date')
            ->whereBetween('license_expiry_date', [now(), now()->addDays($days)]);
    }
}
