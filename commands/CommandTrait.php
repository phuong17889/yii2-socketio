<?php

namespace phuong17889\socketio\commands;

use phuong17889\socketio\Broadcast;
use Symfony\Component\Process\Process;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

trait CommandTrait
{
    /**
     * @var string
     */
    public $server = 'locahost:1212';

    /**
     * [
     *     key => 'path to key',
     *     cert => 'path to cert',
     * ]
     *
     * @var array
     */
    public $ssl = [];

    /**
     * Process job by id and connection
     */
    public function actionProcess($handler, $data)
    {
        Broadcast::process($handler, @json_decode($data, true) ?? []);
    }

	/**
	 * @return Process
	 */
	public function nodejs()
    {
        // Automatically send every new message to available log routes
        Yii::getLogger()->flushInterval = 1;
	    $cmd = [
		    'node',
		    realpath(dirname(__FILE__) . '/../server') . '/index.js',
	    ];
        $args = array_filter([
            'server' => $this->server,
            'pub' => Json::encode(array_filter([
                'host' => Broadcast::getDriver()->hostname,
                'port' => Broadcast::getDriver()->port,
                'password' => Broadcast::getDriver()->password,
            ])),
            'sub' => Json::encode(array_filter([
                'host' => Broadcast::getDriver()->hostname,
                'port' => Broadcast::getDriver()->port,
                'password' => Broadcast::getDriver()->password,
            ])),
            'channels' => implode(',', Broadcast::channels()),
            'nsp' => Broadcast::getManager()->nsp,
            'ssl' => empty($this->ssl) ? null : Json::encode($this->ssl),
            'runtime' => Yii::getAlias('@runtime/logs'),
        ], 'strlen');
        foreach ($args as $key => $value) {
            $cmd[] = '-' . $key . '=\'' . $value . '\'';
        }
	    return new Process($cmd);
    }

	/**
	 * Predis proccess
	 * @throws InvalidConfigException
	 * @throws \Exception
	 */
    public function predis()
    {
        $pubSubLoop = function () {
            $client = Broadcast::getDriver()->getConnection(true);

            // Initialize a new pubsub consumer.
            $pubsub = $client->pubSubLoop();

            $channels = [];
            foreach (Broadcast::channels() as $key => $channel) {
                $channels[$key] = $channel . '.io';
            }

            // Subscribe to your channels
            $pubsub->subscribe(ArrayHelper::merge(['control_channel'], $channels));

            // Start processing the pubsup messages. Open a terminal and use redis-cli
            // to push messages to the channels. Examples:
            //   ./redis-cli PUBLISH notifications "this is a test"
            //   ./redis-cli PUBLISH control_channel quit_loop
            foreach ($pubsub as $message) {
                switch ($message->kind) {
                    case 'subscribe':
                        $this->output("Subscribed to $message->channel\n");
                        break;
                    case 'message':
                        if ('control_channel' == $message->channel) {
                            if ('quit_loop' == $message->payload) {
                                $this->output("Aborting pubsub loop...\n");
                                $pubsub->unsubscribe();
                            } else {
                                $this->output("Received an unrecognized command: $message->payload\n");
                            }
                        } else {
                            $payload = Json::decode($message->payload);
                            $data = $payload['data'] ?? [];
	                        if (isset($data['channel']) && $data['channel'] != '' && strpos($payload['name'], $data['channel']) === false) {
		                        $payload['name'] = $data['channel'] . '_' . $payload['name'];
	                        }
                            Broadcast::on($payload['name'], $data);
                            // Received the following message from {$message->channel}:") {$message->payload}";
                        }
                        break;
                }
            }

            // Always unset the pubsub consumer instance when you are done! The
            // class destructor will take care of cleanups and prevent protocol
            // desynchronizations between the client and the server.
            unset($pubsub);
        };

        // Auto recconnect on redis timeout
        try {
            $pubSubLoop();
        } catch (\Predis\Connection\ConnectionException $e) {
            $pubSubLoop();
        }

        return true;
    }
}
