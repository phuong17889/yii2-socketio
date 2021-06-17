<?php
/**
 * Created by FES VPN.
 * @project bestbuyiptv-shop
 * @author  Le Phuong
 * @email   phuong17889[at]gmail.com
 * @date    3/26/2021
 * @time    11:39 AM
 */

namespace phuong17889\socketio\events;
use yii\helpers\Json;

trait ListenTrait
{

	/**
	 * Listen event on room
	 * @param array $data
	 */
	public function listen(array $data)
	{
		$channel = current(self::broadcastOn());
		if (isset($data['channel']) && $data['channel'] == $channel)
		{
			file_put_contents(\Yii::getAlias('@runtime/' . $channel . '-listen.txt'), Json::encode($data), FILE_APPEND);
			if (isset($data['type']) && isset($data['room_id']))
			{
				switch ($data['type'])
				{
					case 'leave':
						$this->onLeave($data['room_id']);
						break;
					case 'join':
						$this->onJoin($data['room_id']);
						break;
					case 'disconnect':
						$this->onDisconnect($data['room_id']);
						break;
				}
			}
		}
	}

	/**
	 * @return string
	 */
	public static function name(): string
	{
		return 'room';//one channel has only one room event
	}

	/**
	 * @param $room_id
	 *
	 * @return mixed
	 */
	abstract public function onLeave($room_id);

	/**
	 * @param $room_id
	 *
	 * @return mixed
	 */
	abstract public function onDisconnect($room_id);

	/**
	 * @param $room_id
	 *
	 * @return mixed
	 */
	abstract public function onJoin($room_id);
}
