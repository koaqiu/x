<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/3
 * Time: 23:30
 */

namespace x\Utils;


use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use MatthiasMullie\Scrapbook\Adapters\Apc;
use MatthiasMullie\Scrapbook\Adapters\Flysystem;
use MatthiasMullie\Scrapbook\Adapters\MemoryStore;

use MatthiasMullie\Scrapbook\Adapters\Redis;
use x\App;

class Cache
{
    private static $_inc;
    private $_cache;
    private function __construct(array $config = null)
    {
        if (!$config) {
            $config = App::getConfig()['cache'];
        }

        switch ($config['type']){
            case 2:
                $client = new \Redis();
                $client->connect($config['host'], $config['port']);
                $this->_cache = new Redis($client);
                break;
            case 3:
                $this->_cache = new Apc();
                break;
            case 7:
                // create Flysystem object
                $adapter = new Local(APP_TEMP_PATH.'cache', LOCK_EX);
                $filesystem = new Filesystem($adapter);
                $this->_cache= new Flysystem($filesystem);
                break;
            default:
                $this->_cache = new MemoryStore($config['memory_limit']);
                break;
        }
    }

    public function exists($key){
        return $this->_cache->get($key) !== false;
    }
    public function clear(){
        $this->_cache->flush();
        return $this;
    }
    public function remove($key){
        $this->_cache->delete($key);
        return $this;
    }
    public function set($key, $value, $expire = 0){
        $this->_cache->set($key, $value, $expire);
        return $this;
    }
    public function get($key, $dv = null){
        $r = $this->_cache->get($key);
        return $r === false
            ? $dv
            : $r;
    }

    public static function getService(array $config = null)
    {
        if (!self::$_inc) {
            self::$_inc = new Cache($config);
        }
        return self::$_inc;
    }

}