<?php

namespace phuong17889\socketio\events;

/**
 * Interface EventRoomInterface
 * Provide room support for event
 *
 * @package phuong17889\socketio\events
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
