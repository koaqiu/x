<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/3
 * Time: 16:43
 */

namespace x\Db\Operates;


class EgtOperate extends WhereOperate
{

    function __construct($value) {
        parent::__construct($value, ">=");
    }
}