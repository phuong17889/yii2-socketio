<?php

namespace app\events\receiver;

use phuong17889\socketio\events\EventInterface;
use phuong17889\socketio\events\EventPubInterface;

class ResponseEvent implements EventPubInterface, EventInterface
{
    /**
     * @return array
     */
    public static function broadcastOn(): array
    {
        return ['receiver'];
    }

    /**
     * @return string
     */
    public static function name(): string
    {
        return 'response';
    }

    /**
     * @param array $data
     * @return array
     */
    public function fire(array $data): array
    {
        return $data;
    }
}
