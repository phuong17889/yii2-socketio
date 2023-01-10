<?php

namespace phuongdev89\socketio\events;

/**
 * Interface EventRoomInterface
 * Provide room support for event
 *
 * @package phuongdev89\socketio\events
 */
interface EventRoomInterface
{
    /**
     * Get room name
     * It's must be `room`
     *
     * @return string
     */
    public function room(): string;
}
