<?php

namespace phuong17889\socketio;

use yii\web\AssetBundle;

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
    public $sourcePath = '@vendor/phuong17889/yii2-socketio/server/node_modules/socket.io-client/dist';

    /**
     * @var array
     */
    public $js = ['socket.io.js'];
}
