Socket.io Yii extension
=======================

Use all power of socket.io in your Yii 2 project.

[![Latest Stable Version](https://poser.pugx.org/phuong17889/yii2-socketio/v/stable)](https://packagist.org/packages/phuong17889/yii2-socketio) [![Total Downloads](https://poser.pugx.org/phuong17889/yii2-socketio/downloads)](https://packagist.org/packages/phuong17889/yii2-socketio) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phuong17889/yii2-socketio/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phuong17889/yii2-socketio/?branch=master)[![Code Climate](https://codeclimate.com/github/phuong17889/yii2-socketio/badges/gpa.svg)](https://codeclimate.com/github/phuong17889/yii2-socketio)

Config
------

##### Install node + additional npm
```bash
    curl -fsSL https://deb.nodesource.com/setup_15.x | sudo -E bash -
    sudo apt-get install -y nodejs
    cd vendor/phuong17889/yii2-soketio/server
    npm install
```

#### Console config (simple fork)
```php
    'controllerMap' => [
        'socketio' => [
            'class' => \phuong17889\socketio\commands\SocketIoCommand::class,
            'server' => 'localhost:1367'
        ],
    ]       
```
###### Start sockeio server
```bash
    php yii socketio/start
```
###### Stop sockeio server
```bash
    php yii socketio/stop
```

##### Common config
```php
    'components' =>[
        'broadcastEvents' => [
            'class' => \phuong17889\socketio\EventManager::class,
            'nsp' => 'some_unique_key', //must be changed
            // Namespaces with events folders
            'namespaces' => [
                'app\socketio',
            ]
        ],
        'broadcastDriver' => [
            'class' => \phuong17889\socketio\drivers\RedisDriver::class,
            'hostname' => 'localhost',
            'port' => 6379,
        ],    
    ]
```
## Publisher
### Create publisher from server to client
```php
    use phuong17889\socketio\events\EventInterface;
    use phuong17889\socketio\events\EventPubInterface;
    
    class CountEvent implements EventInterface, EventPubInterface
    {
        /**
         * Changel name. For client side this is nsp.
         */
        public static function broadcastOn(): array
        {
            return ['notifications'];
        }
    
        /**
         * Event name
         */
        public static function name(): string
        {
            return 'update_notification_count';
        }
            
        /**
         * Emit client event
         * @param array $data
         * @return array
         */
        public function fire(array $data): array
        {
            return $data;
        }
    }
```
```js
    var socket = io('localhost:1367/notifications');
    socket.on('update_notification_count', function(data){
        console.log(data)
    });
```
```php
    //Run broadcast to client
    \phuong17889\socketio\Broadcast::emit(CountEvent::name(), ['count' => 10]);

```

## Receiver
### Create receiver from client to server
```php
    use phuong17889\socketio\events\EventInterface;
    use phuong17889\socketio\events\EventSubInterface;
    
    class MarkAsReadEvent implements EventInterface, EventSubInterface
    {
        /**
         * Changel name. For client side this is nsp.
         */
        public static function broadcastOn(): array
        {
            return ['notifications'];
        }
    
        /**
         * Event name
         */
        public static function name(): string
        {
            return 'mark_as_read_notification';
        }
            
        /**
         * Emit client event
         * @param array $data
         * @return array
         */
        public function handle(array $data)
        {
            // Mark notification as read
            // And call client update
            // Broadcast::emit('update_notification_count', ['some_key' => 'some_value']);
            
            // Push some log
            file_put_contents(\Yii::getAlias('@app/../file.txt'), serialize($data));
        }
    }
```
```js
    var socket = io('localhost:1367/notifications');
    socket.emit('mark_as_read_notification', {id: 10});
```

### Receiver with checking from client to server
You can have publisher and receiver in one event. If you need check data from client to server you should use: 
- EventPolicyInterface

```php
    use phuong17889\socketio\events\EventSubInterface;
    use phuong17889\socketio\events\EventInterface;
    use phuong17889\socketio\events\EventPolicyInterface;
    
    class MarkAsReadEvent implements EventInterface, EventSubInterface, EventPolicyInterface
    {
        /**
         * Changel name. For client side this is nsp.
         */
        public static function broadcastOn(): array
        {
            return ['notifications'];
        }
    
        /**
         * Event name
         */
        public static function name(): string
        {
            return 'mark_as_read_notification';
        }
         
        public function can($data): bool
        {
            // Check data from client    
            return true;
        }        
        
        /**
         * Emit client event
         * @param array $data
         * @return array
         */
        public function handle(array $data)
        {
            // Mark notification as read
            // And call client update
            Broadcast::emit('update_notification_count', ['some_key' => 'some_value']);
        }
    }
```

## Room
### Subscribe room
Socket.io has room function. If you need it, you should implement `EventRoomInterface`

```php
    use phuong17889\socketio\events\EventPubInterface;
    use phuong17889\socketio\events\EventInterface;
    use phuong17889\socketio\events\EventRoomInterface;
    
    class CountEvent implements EventInterface, EventPubInterface, EventRoomInterface
    {
        /**
         * User id
         * @var int
         */
        protected $userId;
        
        /**
         * Changel name. For client side this is nsp.
         */
        public static function broadcastOn(): array
        {
            return ['notifications'];
        }
    
        /**
         * Event name
         */
        public static function name(): string
        {
            return 'update_notification_count';
        }
           
        /**
         * Socket.io room
         * @return string
         */
        public function room(): string
        {
            return 'user_id_' . $this->userId;
        }            
            
        /**
         * Emit client event
         * @param array $data
         * @return array
         */
        public function fire(array $data): array
        {
            $this->userId = $data['userId'];
            return [
                'count' => 10,
            ];
        }
    }
```
### Subscribe room with event
You should use trait `ListenTrait`

```php
    use phuong17889\socketio\events\EventPubInterface;
    use phuong17889\socketio\events\EventInterface;
    use phuong17889\socketio\events\EventRoomInterface;
    use phuong17889\socketio\events\ListenTrait;
    
    class CountEvent implements EventInterface, EventPubInterface, EventRoomInterface
    {
        use ListenTrait;
        /**
         * User id
         * @var int
         */
        protected $userId;
        
        /**
         * Changel name. For client side this is nsp.
         */
        public static function broadcastOn(): array
        {
            return ['notifications'];
        }
    
        /**
         * Event name
         */
        public static function name(): string
        {
            return 'update_notification_count';
        }
           
        /**
         * Socket.io room
         * @return string
         */
        public function room(): string
        {
            return 'user_id_' . $this->userId;
        }            
            
        /**
         * Emit client event
         * @param array $data
         * @return array
         */
        public function fire(array $data): array
        {
            $this->userId = $data['userId'];
            return [
                'count' => 10,
            ];
        }
        
        public function handle(array $data)
        {
            $this->listen($data); //must place before your code
            file_put_contents(\Yii::getAlias('@app/../file.txt'), serialize($data));
        }
        
        public function onLeave($room_id)
        {
         // TODO: Implement onLeave() method.
        }
        
        public function onDisconnect($room_id)
        {
         // TODO: Implement onDisconnect() method.
        }
        
        public function onJoin($room_id)
        {
         // TODO: Implement onJoin() method.
        }
    }

```
```js
    var socket = io('localhost:1367/notifications');
    socket.emit('join', {room: 'user_id_<?= 10 ?>'});
    // Now you will receive data from 'room-10'
    socket.on('update_notification_count', function(data){
        console.log(data)
    });
    // You can leave room
    socket.emit('leave');
```
```php
    //Run broadcast to user id = 10 
    \phuong17889\socketio\Broadcast::emit(CountEvent::name(), ['count' => 10, 'userId' => 10]);

```
