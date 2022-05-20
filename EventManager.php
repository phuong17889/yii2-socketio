<?php

namespace phuong17889\socketio;

use Yii;
use yii\base\Component;

class EventManager extends Component
{
    /**
     * Array of events namespaces
     *
     * @var array
     */
    public $namespaces = [];

    /**
     * You can set unique nsp for channels
     *
     * @var string
     */
    public $nsp = '';

    /**
     * List with all events
     *
     * @var array
     */
    protected static $list = [];
    protected static $listReverse = [];

    /**
     * @return array
     */
    public function getList(): array
    {
        if (empty(static::$list)) {
            foreach ($this->namespaces as $key => $namespace) {
                $alias = Yii::getAlias('@' . str_replace('\\', '/', trim($namespace, '\\')));
                foreach (glob(sprintf('%s/**.php', $alias)) as $file) {
                    $className = sprintf('%s\%s', $namespace, basename($file, '.php'));
                    if (method_exists($className, 'name')) {
                        static::$list[$className::name()] = $className;
                    }
                }
            }
        }
        return static::$list;
    }

    /**
     * @return array
     */
    public function getListReverse(): array
    {
        if (empty(static::$listReverse)) {
            foreach ($this->namespaces as $key => $namespace) {
                $alias = Yii::getAlias('@' . str_replace('\\', '/', trim($namespace, '\\')));
                foreach (glob(sprintf('%s/**.php', $alias)) as $file) {
                    $className = sprintf('%s\%s', $namespace, basename($file, '.php'));
                    if (method_exists($className, 'name')) {
                        if (strpos($className::name(), $key) !== false) {
                            static::$listReverse[$className::name()] = $className;
                        } else {
                            static::$listReverse[$key . '_' . $className::name()] = $className;
                        }
                    }
                }
            }
        }
        return static::$listReverse;
    }
}
