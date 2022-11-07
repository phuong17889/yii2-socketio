<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'broadcastEvent' => [
            'class' => '\phuong17889\socketio\components\BroadcastEvent',
            'nsp' => 'test', //must be changed
            // Namespaces with events folders
            'namespaces' => require 'events.php'
        ],
        'broadcastDriver' => [
            'class' => '\phuong17889\socketio\components\BroadcastDriver',
            'hostname' => '192.168.16.1',
            'port' => 6379,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
    'controllerMap' => [
        'socketio' => [ // Fixture generation command line.
            'class' => '\phuong17889\socketio\commands\SocketIoCommand',
            'server' => 'localhost:1369'
        ],
    ],
];
return $config;
