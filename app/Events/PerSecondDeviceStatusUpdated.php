<?php

namespace App\Events;

use App\Models\Device;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PerSecondDeviceStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $device;
    public $status;
    public $responseTime;
    public $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(Device $device, string $status, float $responseTime = null)
    {
        $this->device = $device;
        $this->status = $status;
        $this->responseTime = $responseTime;
        $this->timestamp = now()->toISOString();
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
            'updated_at' => $this->timestamp,
        ];
    }
}
