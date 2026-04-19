<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotaAtribuida implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $contestId
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('concurso.' . $this->contestId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'NotaAtribuida';
    }
}
