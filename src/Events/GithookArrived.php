<?php

namespace Codewiser\Githook\Events;

use Codewiser\Githook\Concerns\GitRepository;
use Codewiser\Githook\Concerns\Payload;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GithookArrived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  class-string<GitRepository>  $provider
     * @param  Payload  $payload
     */
    public function __construct(public string $provider, public Payload $payload)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
