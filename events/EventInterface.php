<?php

namespace phuongdev89\socketio\events;

/**
 * Interface EventInterface
 * Event name and broadcast nsp
 *
 * @package phuongdev89\socketio\events
 */
interface EventInterface
{
    /**
     * List broadcast nsp array
     *
     * @return array
     */
    public static function broadcastOn(): array;

    /**
     * Get event name
     *
     * @return string
     */
    public static function name(): string;
}
