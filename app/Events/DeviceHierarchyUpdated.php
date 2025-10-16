<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Device;

class DeviceHierarchyUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $hierarchyData;
    public $changedDeviceId;
    public $changedDeviceStatus;

    /**
     * Create a new event instance.
     */
    public function __construct($hierarchyData, $changedDeviceId = null, $changedDeviceStatus = null)
    {
        $this->hierarchyData = $hierarchyData;
        $this->changedDeviceId = $changedDeviceId;
        $this->changedDeviceStatus = $changedDeviceStatus;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('device-hierarchy'),
        ];
    }
    
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'device.hierarchy.updated';
    }
}
