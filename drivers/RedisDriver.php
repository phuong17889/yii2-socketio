<?php

namespace phuong17889\socketio\drivers;

use Predis\Client;
use yii\helpers\ArrayHelper;

/**
 *
 * Class RedisDriver
 *
 * @package phuong17889\socketio\drivers
 */
class RedisDriver
{
    public $hostname = 'localhost';

    public $port = 6379;

    public $password;
    /**
     * @var
     */
    protected $connection;

    /**
     * Get predis connection
     *
     * @return Client
     */
    public function getConnection($reset = false)
    {
        if (null === $this->connection || true === $reset) {
            $this->connection = new Client(ArrayHelper::merge([
                'scheme' => 'tcp',
                'read_write_timeout' => 0,
            ], [
                'host' => $this->hostname,
                'port' => $this->port,
                'password' => $this->password,
            ]));
        }

        return $this->connection;
    }
}
