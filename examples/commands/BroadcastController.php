<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\events\publisher\UpdateEvent;
use Exception;
use phuongdev89\socketio\Broadcast;
use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BroadcastController extends Controller
{
    /**
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
