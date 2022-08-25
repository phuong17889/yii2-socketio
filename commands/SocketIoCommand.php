<?php

namespace phuong17889\socketio\commands;

use Exception;
use phuong17889\cron\commands\DaemonController;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
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
	 * @throws Exception
	 */
	public function worker()
    {
	    $process = $this->nodejs();
		$process->setTimeout(3600);
	    try {
		    $process->run();
		    if (!$process->isSuccessful()) {
			    echo $process->getErrorOutput();
			    exit(0);
		    }
		    // Save node process pid
		    $this->addPid($process->getPid());
		    while ($process->isRunning()) {
			    $this->predis();
		    }
	    } catch (ProcessTimedOutException $e) {
		    $this->actionRestart();
	    }
    }
}
