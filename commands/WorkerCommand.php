<?php

namespace phuong17889\socketio\commands;

use yii\console\Controller;

/**
 * Socketio server. You should run two commands: "socketio/node-js-server" and "socketio/php-server". Use pm2 as daemon manager.
 *
 * @package phuong17889\socketio\commands
 */
class WorkerCommand extends Controller {

	use CommandTrait;

	/**
	 * @var string
	 */
	public $defaultAction = 'work';

	/**
	 * @var int
	 */
	public $delay = 15;

	/**
	 * Node js listener.
	 *
	 * @throws \Exception
	 */
	public function actionNodeJsServer() {
		$process = $this->nodejs();
		$process->setTimeout(null);
		$process->setIdleTimeout(null);
		$process->run();
	}

	/**
	 * Php listener
	 *
	 * @throws \Exception
	 */
	public function actionPhpServer() {
		while (true) {
			$this->predis();
		}
	}

	/**
	 * @return FileOutput
	 */
	protected function output($text) {
		$this->stdout($text);
	}
}
