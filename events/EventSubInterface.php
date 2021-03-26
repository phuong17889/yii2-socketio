<?php

namespace phuong17889\socketio\events;

/**
 * Interface EventSubInterface
 * Event subscriber interface
 *
 * @package phuong17889\socketio\events
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
