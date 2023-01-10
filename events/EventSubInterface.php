<?php

namespace phuongdev89\socketio\events;

/**
 * Interface EventSubInterface
 * Event subscriber interface
 *
 * @package phuongdev89\socketio\events
 */
interface EventSubInterface
{
    /**
     * Handle published event data
     *
     * @param array $data
     *
     * @return mixed
     */
    public function handle(array $data);
}
