<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'type',
        'hierarchy_level',
        'parent_id',
        'location',
        'description',
        'status',
        'last_checked_at',
        'is_active',
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Device::class, 'parent_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(DeviceLog::class, 'device_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'device_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUtama($query)
    {
        return $query->where('hierarchy_level', 'utama');
    }

    public function scopeSub($query)
    {
        return $query->where('hierarchy_level', 'sub');
    }

    public function scopeDevice($query)
    {
        return $query->where('hierarchy_level', 'device');
    }
}
