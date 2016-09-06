<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/3
 * Time: 16:24
 */

namespace x\Db\Operates;
use x\Db\Dbase;


class NotLikeOperate extends WhereOperate
{
    function __construct($value) {
        if(!is_string($value)){
            throw new \ErrorException("必须是字符串");
        }
        parent::__construct($value, "NOT LIKE");
    }
    public function build()
    {
        return "NOT LIKE ". WhereOperate::fixValue("%".$this->value."%") ."";
    }
}