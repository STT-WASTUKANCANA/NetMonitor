<?php

namespace App\Events;

use App\Models\Device;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $device;
    public $status;
    public $responseTime;

    /**
     * Create a new event instance.
     */
    public function __construct(Device $device, string $status, float $responseTime = null)
    {
        $this->device = $device;
        $this->status = $status;
        $this->responseTime = $responseTime;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('device-status'),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'device_id' => $this->device->id,
            'device_name' => $this->device->name,
            'ip_address' => $this->device->ip_address,
            'status' => $this->status,
            'response_time' => $this->responseTime,
            'updated_at' => $this->device->last_checked_at ? $this->device->last_checked_at->toISOString() : null,
        ];
    }
}
