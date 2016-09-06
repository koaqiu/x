<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/3
 * Time: 16:24
 */

namespace x\Db\Operates;
use x\Db\Dbase;


abstract class WhereOperate
{
    public $value;
    protected $_op;

    function __construct($value, $op = "")
    {
        $this->value = $value;
        $this->_op = $op;
    }

    public static function fixValue($value){
        if(is_numeric($value)){
            return "".$value;
        }else if(is_string($value)){
            $value = Dbase::get()->EscapeString($value);
            return "'$value'";
        }else if(is_bool($value)){
            if($value === true) {
                return "1";
            }else if($value === false) {
                return "0";
            }
        }else if($value instanceof \DateTime) {
            //date('Y-m-d H:i:s',time())
            return "'".$value->format('Y-m-d H:i:s')."'";
        }
        return "'".$value."'";
    }
    public function build()
    {
//        if(is_numeric($this->value)){
//            return $this->_op." ".$this->value;
//        }else if(is_string($this->value)){
//            $value = Dbase::get()->EscapeString($this->value);
//            return $this->_op." '$value'";
//        }else if(is_bool($this->value)){
//            if($this->value === true) {
//                return $this->_op." 1";
//            }else if($this->value === false) {
//                return $this->_op." 0";
//            }
//        }else if($this->value instanceof \DateTime) {
//            //date('Y-m-d H:i:s',time())
//            return $this->_op." " . $this->value->format('Y-m-d H:i:s');
//        }
//        return $this->_op." ".$this->value;
        return $this->_op." ".self::fixValue($this->value);
    }
}