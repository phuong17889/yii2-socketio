<?php

namespace phuong17889\socketio\commands;

use phuong17889\cron\commands\DaemonController;
use yii\base\InvalidConfigException;

/**
 * Class SocketIoCommand
 * Run this daemon for listen socketio. Don't forget about run npm install in the folder "server".
 *
 * @package phuong17889\socketio\commands
 */
class SocketIoCommand extends DaemonController
{
    use CommandTrait;

    /**
     * Daemon name
     *
     * @return string
     */
    protected function daemonName(): string
    {
        return 'socket.io';
    }

    /**
     * SocketOI worker
     * @throws InvalidConfigException
     */
    public function worker()
    {
        $process = $this->nodejs();
        $process->disableOutput();
        $process->start();

        // Save node proccess pid
        $this->addPid($process->getPid());

        while ($process->isRunning()) {
            $this->predis();
        }
    }
}
