<?php

namespace app\events\receiver;

use Exception;
use phuongdev89\socketio\Broadcast;
use phuongdev89\socketio\events\EventInterface;
use phuongdev89\socketio\events\EventSubInterface;

class RequestEvent implements EventSubInterface, EventInterface
{

    /**
     * @return array
     */
    public static function broadcastOn(): array
    {
        return ['receiver'];
    }

    /**
     * @return string
     */
    public static function name(): string
    {
        return 'request';
    }

    /**
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function handle(array $data)
    {
        Broadcast::emit(ResponseEvent::name(), $data);
        file_put_contents(\Yii::getAlias('@app/file.txt'), json_encode($data));
    }
}
