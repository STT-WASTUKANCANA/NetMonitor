<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'message',
        'status',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'resolved_at',
        'created_at',
        'updated_at',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Format the resolved timestamp in a readable format.
     *
     * @return string|null
     */
    public function getResolvedFormattedAttribute()
    {
        return $this->resolved_at ? $this->resolved_at->format('l, d F Y H:i:s') : null;
    }

    /**
     * Format the resolved timestamp in short format (d/m/Y H:i).
     *
     * @return string|null
     */
    public function getResolvedShortAttribute()
    {
        return $this->resolved_at ? $this->resolved_at->format('d/m/Y H:i') : null;
    }

    /**
     * Get the day of the week when the alert was resolved.
     *
     * @return string|null
     */
    public function getResolvedDayAttribute()
    {
        return $this->resolved_at ? $this->resolved_at->format('l') : null;
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

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }
}
