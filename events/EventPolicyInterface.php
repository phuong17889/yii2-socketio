<?php

namespace phuongdev89\socketio\events;

/**
 * Interface EventPolicyInterface
 * Event policy
 *
 * @package phuongdev89\socketio\events
 */
interface EventPolicyInterface
{

    /**
     * @param $data
     *
     * @return bool
     */
    public function can($data): bool;
}
