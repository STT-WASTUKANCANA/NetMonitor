<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'response_time',
        'status',
        'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
        'response_time' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $table = 'logs';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'checked_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Format the checked timestamp in a readable format.
     *
     * @return string|null
     */
    public function getCheckedFormattedAttribute()
    {
        return $this->checked_at ? $this->checked_at->format('l, d F Y H:i:s') : null;
    }

    /**
     * Format the checked timestamp in short format (d/m/Y H:i).
     *
     * @return string|null
     */
    public function getCheckedShortAttribute()
    {
        return $this->checked_at ? $this->checked_at->format('d/m/Y H:i') : null;
    }

    /**
     * Get the day of the week when the device was checked.
     *
     * @return string|null
     */
    public function getCheckedDayAttribute()
    {
        return $this->checked_at ? $this->checked_at->format('l') : null;
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

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
}
