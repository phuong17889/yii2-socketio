<?php

namespace phuong17889\socketio\events;

interface EventPolicyInterface
{

	/**
	 * @param $data
	 *
	 * @return bool
	 */
	public function can($data): bool;
}
