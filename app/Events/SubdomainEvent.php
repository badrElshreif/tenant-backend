<?php
namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class SubdomainEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public string $message;
    public string $subdomain;

    public function __construct(string $message)
    {
        $this->message = $message;
        $this->subdomain = request()->getHost(); // Get the current subdomain
    }

    public function broadcastOn()
    {
        return ['messages.' . $this->subdomain]; // Unique channel per subdomain
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }
}
