<?php

namespace phuong17889\socketio\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Access Message asset bundle.
 *
 * @author Dmitry Turchanin
 */
class SocketIoAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@vendor/phuong17889/yii2-socketio/node_modules/socket.io-client/dist';

    /**
     * @var array
     */
    public $js = ['socket.io.js'];

    /**
     * @var array
     */
    public $jsOptions = ['position' => View::POS_HEAD];
}
