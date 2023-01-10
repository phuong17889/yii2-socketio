Socket.io Yii extension
=======================

Use all power of socket.io in your Yii 2 project.

Install
------
##### Install nodejs (tested with node 16)
```
    curl -fsSL https://deb.nodesource.com/setup_16.x | sudo -E bash -
    sudo apt-get install -y nodejs
```
##### Add to `composer.json`
```bash
{
    "require" : {
        "phuongdev89/yii2-socketio": "^2"
    },
    ...
    "scripts" : {
        "post-install-cmd": "cd vendor/phuongdev89/yii2-socketio && /usr/bin/npm install",
        "post-update-cmd": "cd vendor/phuongdev89/yii2-socketio && /usr/bin/npm install"
    }
}
```
Config
------
#### Console config (simple fork)
```php
    'controllerMap' => [
        'socketio' => [
            'class' => \phuongdev89\socketio\commands\SocketIoCommand::class,
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
        'broadcastEvent' => [
            'class' => \phuongdev89\socketio\components\BroadcastEvent::class,
            'nsp' => 'some_unique_key', //must be changed
            // Namespaces with events folders
            'namespaces' => [
                'app\socketio',
            ]
        ],
        'broadcastDriver' => [
            'class' => \phuongdev89\socketio\components\BroadcastDriver::class,
            'hostname' => 'localhost',
            'port' => 6379,
        ],    
    ]
```
Usage
-----
### Publisher
Create publisher from server to client
```php
    use phuongdev89\socketio\events\EventInterface;
    use phuongdev89\socketio\events\EventPubInterface;
    
    class CountEvent implements EventInterface, EventPubInterface
    {
        /**
         * Channel name. For client side this is nsp.
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
On client using socketio to receive data from server
```js
    var socket = io('localhost:1367/notifications');
    socket.on('update_notification_count', function(data){
        console.log(data)
    });
```
Using to broadcast data to client
```php
    //Run broadcast to client
    \phuongdev89\socketio\Broadcast::emit(CountEvent::name(), ['count' => 10]);

```

### Receiver
Create receiver from client to server
```php
    use phuongdev89\socketio\events\EventInterface;
    use phuongdev89\socketio\events\EventSubInterface;
    
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
            file_put_contents(\Yii::getAlias('@app/file.txt'), json_encode($data));
        }
    }
```
On client using socketio to emit data to server
```js
    var socket = io('localhost:1367/notifications');
    socket.emit('mark_as_read_notification', {id: 10});
```
### Receiver with checking from client to server
You can have publisher and receiver in one event. If you need check data from client to server you should use: 
- EventPolicyInterface
```php
    use phuongdev89\socketio\events\EventSubInterface;
    use phuongdev89\socketio\events\EventInterface;
    use phuongdev89\socketio\events\EventPolicyInterface;
    
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
         
        /**
        * @param $data
        * @return bool
        */
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
            file_put_contents(\Yii::getAlias('@app/file.txt'), json_encode($data));
        }
    }
```

### Subscribe room
Socket.io has room function. If you need it, you should implement `EventRoomInterface`

```php
    use phuongdev89\socketio\events\EventPubInterface;
    use phuongdev89\socketio\events\EventInterface;
    use phuongdev89\socketio\events\EventRoomInterface;
    
    class CountEvent implements EventInterface, EventPubInterface, EventRoomInterface
    {
        /**
         * User id
         * @var int
         */
        protected $user_id;
        
        /**
         * Channel name. For client side this is nsp.
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
            return 'user_id_' . $this->user_id;
        }            
            
        /**
         * Emit client event
         * @param array $data
         * @return array
         */
        public function fire(array $data): array
        {
            $this->user_id = $data['user_id'];
            return [
                'count' => 10,
            ];
        }
    }
```
### Subscribe room with event
You should use trait `ListenTrait`
```php
    use phuongdev89\socketio\events\EventPubInterface;
    use phuongdev89\socketio\events\EventInterface;
    use phuongdev89\socketio\events\EventRoomInterface;
    use phuongdev89\socketio\traits\ListenTrait;
    
    class CountEvent implements EventInterface, EventPubInterface, EventRoomInterface
    {
        use ListenTrait;
        /**
         * User id
         * @var int
         */
        protected $userId;
        
        /**
         * Channel name. For client side this is nsp.
         */
        public static function broadcastOn(): array
        {
            return ['notifications'];
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
On client using socketio to join the room, and listen data
```js
    var socket = io('localhost:1367/notifications');
    socket.emit('join', {room: 'user_id_10'});
    // Now you will receive data from 'room-10'
    socket.on('update_notification_count', function(data){
        console.log(data)
    });
    // You can leave room
    socket.emit('leave');
```
Using to broadcast data to client on the room
```php
    //Run broadcast to user id = 10 
    \phuongdev89\socketio\Broadcast::emitToRoom(CountEvent::class, [
        'count' => 4, 
        'user_id' => 10,//push data to room-10
    ]);
```
