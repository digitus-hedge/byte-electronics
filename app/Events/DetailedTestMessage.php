<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
class DetailedTestMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $sender;
    public $timestamp;
    public $data;

    /**
     * Create a new event instance.
     */
    public function __construct(string $message, string $sender)
    {
        $this->message = $message;
        $this->sender = $sender;
        $this->timestamp = now()->toDateTimeString();
        $this->data = [
            'id' => rand(1000, 9999), // Simulate an order ID
            'status' => 'pending',
            'details' => 'This is a test payload with extra info',
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            // new PrivateChannel('Byte-Test'),
            new Channel('Byte-Test'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'DetailedTestMessage'; // Custom event name for listeners
    }

   

    public function broadcastWith():array
    {
        Log::info('Broadcasting DetailedTestMessage', [
            'message' => $this->message,
            'sender' => $this->sender,
        ]);
        return [
            'message' => $this->message,
            'sender' => $this->sender,
            'timestamp' => $this->timestamp,
            'data' => $this->data,
        ];
    }
}
