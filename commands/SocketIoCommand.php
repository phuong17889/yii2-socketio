<?php

namespace phuongdev89\socketio\commands;

use Exception;
use phuongdev89\cron\commands\DaemonController;
use phuongdev89\socketio\traits\CommandTrait;
use Symfony\Component\Process\Exception\ProcessTimedOutException;

/**
 * Class SocketIoCommand
 * Run this daemon for listen socketio. Don't forget about run npm install in the folder "server".
 *
 * @package phuongdev89\socketio\commands
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
     * @throws Exception
     */
    public function worker()
    {
        $this->actionIndex();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionIndex()
    {
        $process = $this->nodejs();
        $process->setTimeout(3);
        try {
            $process->start();
	        while ($process->isRunning()) {
		        $this->addPid($process->getPid() + 1);
                $this->predis();
            }
        } catch (ProcessTimedOutException $e) {
            echo $e->getMessage() . PHP_EOL;
            $this->actionIndex();
        }
    }
}
