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
     *
     * @return string
     */
    public function room(): string;
}
