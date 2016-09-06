<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/4
 * Time: 0:53
 */

namespace x\Utils;


class Data
{
    private $_data;
    function __construct($data = null)
    {
        if(is_array($data)) {
            $this->_data = $data;
        }elseif(is_string($data)){
            try {
                $this->_data = json_decode($data, true);
            }catch (\ErrorException $err){

            }
        }
    }
    public function exists($key){
        if(!$this->_data)
            return false;
        if(!is_array($this->_data))
            return false;
        if(count($this->_data) < 1)
            return false;
        return isset($this->_data[$key]);
    }
    public function get($key, $dv = null){
        return $this->exists($key)
            ? $this->_data[$key]
            : $dv;
    }

    function __get($name)
    {
        return $this->get($name);
    }
}