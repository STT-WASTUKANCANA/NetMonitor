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

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'last_checked_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the current time with the application timezone.
     *
     * @return \Illuminate\Support\Carbon
     */
    public function currentTimestamp()
    {
        return now();
    }

    /**
     * Format the last checked timestamp in a readable format.
     *
     * @return string|null
     */
    public function getLastCheckedFormattedAttribute()
    {
        return $this->last_checked_at ? $this->last_checked_at->format('l, d F Y H:i:s') : null;
    }

    /**
     * Format the last checked timestamp in short format (d/m/Y H:i).
     *
     * @return string|null
     */
    public function getLastCheckedShortAttribute()
    {
        return $this->last_checked_at ? $this->last_checked_at->format('d/m/Y H:i') : null;
    }

    /**
     * Get the day of the week when the device was last checked.
     *
     * @return string|null
     */
    public function getLastCheckedDayAttribute()
    {
        return $this->last_checked_at ? $this->last_checked_at->format('l') : null;
    }

    /**
     * Get the creation date in readable format.
     *
     * @return string|null
     */
    public function getCreatedFormattedAttribute()
    {
        return $this->created_at ? $this->created_at->format('l, d F Y H:i:s') : null;
    }

    /**
     * Get the update date in readable format.
     *
     * @return string|null
     */
    public function getUpdatedFormattedAttribute()
    {
        return $this->updated_at ? $this->updated_at->format('l, d F Y H:i:s') : null;
    }

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
