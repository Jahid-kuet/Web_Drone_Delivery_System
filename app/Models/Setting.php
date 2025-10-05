<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
        'is_editable',
        'validation_rules',
        'options',
        'default_value',
        'sort_order',
        'requires_restart',
        'metadata'
    ];

    protected $casts = [
        'options' => 'array',
        'metadata' => 'array',
        'is_public' => 'boolean',
        'is_editable' => 'boolean',
        'requires_restart' => 'boolean'
    ];

    // Type constants
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_JSON = 'json';
    const TYPE_ARRAY = 'array';

    // Group constants
    const GROUP_GENERAL = 'general';
    const GROUP_DRONE = 'drone';
    const GROUP_DELIVERY = 'delivery';
    const GROUP_NOTIFICATION = 'notification';
    const GROUP_SECURITY = 'security';
    const GROUP_EMAIL = 'email';
    const GROUP_SMS = 'sms';

    /**
     * Scope by group
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope for public settings
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for editable settings
     */
    public function scopeEditable($query)
    {
        return $query->where('is_editable', true);
    }

    /**
     * Get typed value
     */
    public function getTypedValueAttribute()
    {
        switch ($this->type) {
            case self::TYPE_INTEGER:
                return (int) $this->value;
            case self::TYPE_BOOLEAN:
                return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            case self::TYPE_JSON:
            case self::TYPE_ARRAY:
                return json_decode($this->value, true);
            default:
                return $this->value;
        }
    }

    /**
     * Set typed value
     */
    public function setTypedValue($value): void
    {
        if (in_array($this->type, [self::TYPE_JSON, self::TYPE_ARRAY]) && is_array($value)) {
            $this->value = json_encode($value);
        } elseif ($this->type === self::TYPE_BOOLEAN) {
            $this->value = $value ? '1' : '0';
        } else {
            $this->value = (string) $value;
        }
    }

    /**
     * Get a setting value by key
     */
    public static function getValue($key, $default = null)
    {
        return Cache::remember("setting.{$key}", 3600, function() use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->typed_value : $default;
        });
    }

    /**
     * Set a setting value by key
     */
    public static function setValue($key, $value): bool
    {
        $setting = static::firstOrCreate(['key' => $key]);
        $setting->setTypedValue($value);
        $result = $setting->save();
        
        // Clear cache
        Cache::forget("setting.{$key}");
        
        return $result;
    }

    /**
     * Get all settings as key-value pairs
     */
    public static function getAllSettings($group = null): array
    {
        $query = static::query();
        
        if ($group) {
            $query->where('group', $group);
        }
        
        return $query->get()->mapWithKeys(function($setting) {
            return [$setting->key => $setting->typed_value];
        })->toArray();
    }

    /**
     * Get public settings
     */
    public static function getPublicSettings(): array
    {
        return static::where('is_public', true)
            ->get()
            ->mapWithKeys(function($setting) {
                return [$setting->key => $setting->typed_value];
            })
            ->toArray();
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        $settings = static::all();
        foreach ($settings as $setting) {
            Cache::forget("setting.{$setting->key}");
        }
    }

    /**
     * Get setting groups
     */
    public static function getGroups(): array
    {
        return [
            self::GROUP_GENERAL => 'General',
            self::GROUP_DRONE => 'Drone Settings',
            self::GROUP_DELIVERY => 'Delivery Settings',
            self::GROUP_NOTIFICATION => 'Notifications',
            self::GROUP_SECURITY => 'Security',
            self::GROUP_EMAIL => 'Email',
            self::GROUP_SMS => 'SMS'
        ];
    }

    /**
     * Get setting types
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_STRING => 'String',
            self::TYPE_INTEGER => 'Integer',
            self::TYPE_BOOLEAN => 'Boolean',
            self::TYPE_JSON => 'JSON',
            self::TYPE_ARRAY => 'Array'
        ];
    }

    /**
     * Boot method to clear cache on save
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saved(function($setting) {
            Cache::forget("setting.{$setting->key}");
        });
        
        static::deleted(function($setting) {
            Cache::forget("setting.{$setting->key}");
        });
    }
}
