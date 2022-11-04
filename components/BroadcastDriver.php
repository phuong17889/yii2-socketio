<?php

namespace phuong17889\socketio\components;

use Predis\Client;
use Predis\PubSub\Consumer;
use yii\base\Component;

/**
 * @method Consumer|null pubSubLoop()
 * @method Consumer|null publish()
 */
class BroadcastDriver extends Component
{

    /**
     * @var string redis hostname
     */
    public $hostname = 'localhost';

    /**
     * @var int redis port
     */
    public $port = 6379;

    /**
     * @var string redis password
     */
    public $password = null;

    /**
     * @var Client
     */
    protected $connection = null;

    /**
     * Get predis connection
     *
     * @return Client
     */
    public function init()
    {
        if (null === $this->connection) {
            $this->connection = new Client([
                'scheme' => 'tcp',
                'read_write_timeout' => 0,
                'host' => $this->hostname,
                'port' => $this->port,
                'password' => $this->password,
            ]);
        }

        return $this->connection;
    }

    /**
     * @param $name
     * @param $params
     * @return mixed
     */
    public function __call($name, $params)
    {
        return $this->connection->$name($params);
    }
}
