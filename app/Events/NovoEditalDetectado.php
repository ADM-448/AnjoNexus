<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NovoEditalDetectado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $edital;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(\App\Models\Edital $edital)
    {
        $this->edital = $edital;
    }

    /**
     * Get the channels the event should broadcast on.
     * Canal aberto "editais"
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\Channel('editais');
    }
}
