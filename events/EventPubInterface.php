<?php

namespace phuongdev89\socketio\events;

/**
 * Interface EventPubInterface
 * Event publish interface
 *
 * @package phuongdev89\socketio\events
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
