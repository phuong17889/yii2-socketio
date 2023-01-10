<?php

namespace app\events\publisher;

use phuongdev89\socketio\events\EventInterface;
use phuongdev89\socketio\events\EventPubInterface;

class UpdateEvent implements EventPubInterface, EventInterface
{

    /**
     * @return array
     */
    public static function broadcastOn(): array
    {
        return ['publisher'];
    }

    /**
     * @return string
     */
    public static function name(): string
    {
        return 'update_on_publisher';
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
