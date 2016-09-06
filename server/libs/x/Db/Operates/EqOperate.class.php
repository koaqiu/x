<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/3
 * Time: 16:18
 */

namespace x\Db\Operates;


use x\Db\Dbase;

class EqOperate extends WhereOperate
{
    function __construct($value) {
        parent::__construct($value, "=");
    }
}