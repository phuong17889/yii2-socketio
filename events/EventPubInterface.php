<?php

namespace phuong17889\socketio\events;

/**
 * Interface EventPubInterface
 * Event publish interface
 *
 * @package phuong17889\socketio\events
 */
interface EventPubInterface
{
    /**
     * Process event and return result to subscribers
     *
     * @param array $data
     *
     * @return array
     */
    public function fire(array $data): array;
}
