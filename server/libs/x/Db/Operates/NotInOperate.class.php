<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/3
 * Time: 16:24
 */

namespace x\Db\Operates;
use x\Db\SqlBuilder;


class NotInOperate extends WhereOperate
{
    function __construct($value) {
        if(is_array($value)) {
            parent::__construct($value, "NOT IN");
        }elseif ($value instanceof SqlBuilder && $value -> isSubSelect()){
            parent::__construct($value, "IN");
        }else {
            throw new \ErrorException("必须是数组或者 SELECT 子句");
        }
    }
    public function build()
    {
        if($this->value instanceof SqlBuilder){
            return "NOT IN (".$this->value->build().")";
        }
        $arr = array_map(function($item){
            return WhereOperate::fixValue($item);
        }, $this->value);
        return "NOT IN (". join(", ", $arr).")";
    }
}