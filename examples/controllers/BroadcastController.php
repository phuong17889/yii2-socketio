<?php

namespace app\controllers;

use app\events\publisher\UpdateEvent;
use Exception;
use phuongdev89\socketio\Broadcast;
use yii\web\Controller;

class BroadcastController extends Controller
{
    /**
     * @return void
     * @throws Exception
     */
    public function actionPublisher()
    {
        $j = rand(10, 100);
        Broadcast::emit(UpdateEvent::name(), [
            date('Y-m-d H:i:s') => 'Below will be ' . $j . ' messages, started from ' . date('Y-m-d H:i:s')
        ]);
        for ($i = 1; $i <= $j; $i++) {
            Broadcast::emit(UpdateEvent::name(), [
                date('Y-m-d H:i:s') => $i
            ]);
            sleep(1);
        }
    }
}
