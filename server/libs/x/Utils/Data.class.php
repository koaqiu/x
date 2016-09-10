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

    /**
     * 读取整型
     * @param $key
     * @param int $dv
     * @return int
     */
    public function getInt($key, $dv = 0){
        if($this->exists($key)){
            $v = $this->_data[$key];
            if(is_numeric($v)){
                return intval($v);
            }
        }
        return $dv;
    }

    /**
     * 读取浮点型
     * @param $key
     * @param float $dv
     * @return float
     */
    public function getFloat($key, $dv = 0.0){
        if($this->exists($key)){
            $v = $this->_data[$key];
            if(is_numeric($v)){
                return floatval($v);
            }
        }
        return $dv;
    }
    /**
     * 读取日期类型
     * @param $key
     * @param null|DateTime $dv
     * @return null|DateTime
     */
    public function getDate($key, $dv = null){
        if($this->exists($key)){
            $v = $this->_data[$key];
            return new DateTime($v);
        }
        return $dv;
    }
}