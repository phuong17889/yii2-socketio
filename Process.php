<?php

namespace phuong17889\socketio;

use Symfony\Component\Process\Process as SymfonyProcess;
use Yii;

/**
 * Class Process
 *
 * @package phuong17889\socketio
 */
class Process
{
    /**
     * @var array
     */
    private static $_inWork = [];

    /**
     * @var
     */
    public $yiiAlias;

    /**
     * @return int
     */
    public function getParallelEnv(): int
    {
        return getenv('SOCKET_IO.PARALLEL') ? getenv('SOCKET_IO.PARALLEL') : 10;
    }

    /**
     * Run process. If more then limit then wait and try run process on more time.
     *
     * @param string $handle
     * @param array $data
     *
     * @return SymfonyProcess
     */
    public function run(string $handle, array $data)
    {
        $this->inWork();

        while (count(self::$_inWork) >= $this->getParallelEnv()) {
            usleep(100);

            $this->inWork();
        }

        return $this->push($handle, $data);
    }

    /**
     * In work processes
     */
    private function inWork()
    {
        foreach (self::$_inWork as $i => $process) {
            if (false === $process->isRunning()) {
                unset(self::$_inWork[$i]);
            }
        }
    }

    /**
     * Create cmd process and push to queue.
     *
     * @param string $handle
     * @param array $data
     *
     * @return SymfonyProcess
     */
    private function push(string $handle, array $data): SymfonyProcess
    {
	    $cmd = [
		    'php',
		    'yii',
		    'socketio/process',
		    $handle,
		    json_encode($data),
	    ];

        if (is_null($this->yiiAlias)) {
            if (file_exists(Yii::getAlias('@app/yii'))) {
                $this->yiiAlias = '@app';
            } elseif (file_exists(Yii::getAlias('@app/../yii'))) {
                $this->yiiAlias = '@app/../';
            }
        }
	    $process = new SymfonyProcess($cmd, Yii::getAlias($this->yiiAlias));
        $process->setTimeout(10);
        $process->run();

        self::$_inWork[] = $process;

        return $process;
    }
}
