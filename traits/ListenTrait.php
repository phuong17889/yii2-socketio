<?php
/**
 * @author  Phuong Dev
 * @email   phuongdev89@gmail.com
 * @date    3/26/2021
 * @time    11:39 AM
 */

namespace phuongdev89\socketio\traits;

trait ListenTrait
{

    /**
     * Listen event on room
     * @param array $data
     */
    public function listen(array $data)
    {
        $channel = current(self::broadcastOn());
        if (isset($data['channel']) && $data['channel'] == $channel) {
            if (isset($data['type']) && isset($data['room_id'])) {
                switch ($data['type']) {
                    case 'leave':
                        $this->onLeave($data['room_id']);
                        break;
                    case 'join':
                        $this->onJoin($data['room_id']);
                        break;
                    case 'disconnect':
                        $this->onDisconnect($data['room_id']);
                        break;
                }
            }
        }
    }

    /**
     * one channel has only one room event
     * @return string
     */
    public static function name(): string
    {
        return 'room';
    }

    /**
     * @param $room_id
     *
     * @return mixed
     */
    abstract public function onLeave($room_id);

    /**
     * @param $room_id
     *
     * @return mixed
     */
    abstract public function onDisconnect($room_id);

    /**
     * @param $room_id
     *
     * @return mixed
     */
    abstract public function onJoin($room_id);
}
