<?php

namespace phuong17889\socketio\events;

interface EventPolicyInterface
{
    public function can($data): bool;
}
